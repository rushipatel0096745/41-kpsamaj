<?php
include_once("config/common.php");

//$taluka_code = $_POST['taluka_code'];
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

// function main($json){
// 	$obj = new MySQLCN();
// 	$data = json_decode($json, true);

//     $query = "SELECT
//     mr.id,
//     mr.member_id,
//     mr.request_id,
//     fm.surname,
//     fm.name
// FROM submit_matrimonial_requests mr
// JOIN family_member fm
//     ON fm.id = mr.request_id
// WHERE mr.request_id = '".$data['member_id']."'";

// 	$listData = $obj->select($query);

// 	if (count($listData) > 0) {
// 		$response = array("code"=>"200","success"=>"1","message" => "Data list","data"=>$listData);	
// 	}else{
// 		$response = array("code"=>"200","success"=>"0","message" => "Data not found","data"=>"");
// 	}

// 	return $response;

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
        mr.id                                               AS request_id,
        mr.member_id                                        AS from_member_id,
        CONCAT(fm.surname, ' ', fm.name)                    AS name,
        TIMESTAMPDIFF(YEAR, fm.dob, CURDATE())              AS age,
        ''                                                  AS profession,
        lvc.village_city_name                               AS village,
        (
            SELECT fmt.user_photo_1
            FROM form_matromonials fmt
            WHERE fmt.member_id = fm.id
            LIMIT 1
        ) AS user_photo_1,
        mr.createdon                                       AS sent_at,
        COALESCE(mr.action, 'pending')                      AS status
    FROM submit_matrimonial_requests mr
    JOIN family_member fm
        ON fm.id = mr.member_id
    LEFT JOIN list_village_city lvc
        ON lvc.id = fm.city
    WHERE mr.request_id = $memberId
    ORDER BY mr.id DESC
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
			"message" => "Requests fetched successfully.",
			"data"    => $listData
		];
	}

	return [
		"code"    => "200",
		"success" => "0",
		"message" => "No requests found.",
		"data"    => []
	];
}
