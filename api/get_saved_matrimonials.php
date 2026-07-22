<?php
include_once("config/common.php");

//$taluka_code = $_POST['taluka_code'];
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

// function main($json)
// {
//     $obj = new MySQLCN();
//     $data = json_decode($json, true);

//     //$query = "SELECT bm.id, bm.member_id,bm.saved_member_id, fm.surname, fm.name FROM bookmark_matrimonials bm, family_member fm WHERE bm.member_id = '".$data['member_id']."' AND fm.id= bm.saved_member_id";


//     // $query = "SELECT
//     // bm.id,
//     // bm.member_id,
//     // bm.saved_member_id,
//     // fm.surname,
//     // fm.name,
//     // fm.dob,
//     // lvc.village_city_name AS city,
//     // 'Patidar' AS community,
//     // '' AS occupation,
//     // 1 AS is_verified,
//     // le.education AS qualification,
//     // fmt.user_photo_1
//     //     FROM bookmark_matrimonials bm
//     //     JOIN family_member fm
//     //         ON fm.id = bm.saved_member_id
//     //     LEFT JOIN list_village_city lvc
//     //         ON lvc.id = fm.city
//     //     LEFT JOIN form_educations fe
//     //         ON fe.member_id = fm.id
//     //     LEFT JOIN list_education le
//     //         ON le.id = fe.education
//     //     LEFT JOIN form_matromonials fmt
//     //         ON fmt.member_id = fm.id
//     //     WHERE bm.member_id = '".$data['member_id']."'";

//     // $listData = $obj->select($query);

//     $memberId = $data['member_id'];

//     $csql = preg_replace('/\s+/', ' ', trim($query));


//     $listData = $obj->select($csql);



//     if (count($listData) > 0) {
//         $response = array("code" => "200", "success" => "1", "message" => "Data list", "data" => $listData);
//     } else {
//         $response = array("code" => "200", "success" => "0", "message" => "Data not found", "data" => "");
//     }

//     return $response;
// }

function main($json)
{
    $obj  = new MySQLCN();
    $data = json_decode($json, true);

    $memberId = (int)($data['member_id'] ?? 0);

    if ($memberId <= 0) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "member_id is required.",
            "data"    => []
        ];
    }

    $query = "
        SELECT
            bm.saved_member_id                                      AS id,
            fm.id                                                   AS member_id,
            CONCAT(fm.surname, ' ', fm.name)                        AS name,
            CONCAT(TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()), ' yrs')  AS age,
            lvc.village_city_name                                   AS city,
            'Patidar'                                               AS community,
            (
                SELECT le.education
                FROM form_educations fe
                JOIN list_education le ON le.id = fe.education
                WHERE fe.member_id = fm.id
                LIMIT 1
            ) AS qualification,
            '' AS occupation,
            '1' AS is_verified,
            CASE
                WHEN EXISTS (
                    SELECT 1 FROM submit_matrimonial_requests smr
                    WHERE smr.member_id = $memberId AND smr.request_id = fm.id
                )
                THEN '1' ELSE '0'
            END AS interest_sent,
            CASE
                WHEN EXISTS (
                    SELECT 1 FROM submit_matrimonial_requests smr
                    WHERE smr.member_id = $memberId AND smr.request_id = fm.id
                )
                THEN '1' ELSE '0'
            END AS awaiting_response,
            (
                SELECT fmt.user_photo_1
                FROM form_matromonials fmt
                WHERE fmt.member_id = fm.id
                LIMIT 1
            ) AS user_photo_1,
            DATE(bm.createdon) AS saved_date
        FROM bookmark_matrimonials bm
        JOIN family_member fm ON fm.id = bm.saved_member_id
        LEFT JOIN list_village_city lvc ON lvc.id = fm.city
        WHERE bm.member_id = $memberId
        ORDER BY bm.id DESC
    ";

    $csql     = preg_replace('/\s+/', ' ', trim($query));
    $listData = $obj->select($csql);

    if ($listData === false || $listData === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    if (count($listData) > 0) {
        return [
            "code"    => "200",
            "success" => "1",
            "message" => "Saved profiles fetched successfully.",
            "data"    => $listData
        ];
    }

    return [
        "code"    => "200",
        "success" => "0",
        "message" => "No saved profiles found.",
        "data"    => []
    ];
}
