<?php
include_once("config/common.php");
include_once("token_check.php");

$json   = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

function main($json)
{
    $obj  = new MySQLCN();
    $data = json_decode($json, true);

    $page     = !empty($data['page'])     ? (int)$data['page']     : 1;
    $per_page = !empty($data['per_page']) ? (int)$data['per_page'] : 20;
    $offset   = ($page - 1) * $per_page;

    $countryId   = (int)($data['country_id']   ?? 0);
    $countryName = trim($data['country_name']  ?? '');

    // Need at least one
    if ($countryId <= 0 && $countryName === '') {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "Either country_id or country_name is required.",
            "data"    => []
        ];
    }

    // Resolve country name from id if country_id is passed
    if ($countryId > 0) {
        $countrySql  = "SELECT country_name FROM list_countries WHERE id = $countryId AND delflag = 0 LIMIT 1";
        $countryData = $obj->select($countrySql);

        if (empty($countryData)) {
            return [
                "code"    => "404",
                "success" => "0",
                "message" => "Country not found.",
                "data"    => []
            ];
        }

        $countryName = $countryData[0]['country_name'];
    }

    $safeCountry = addslashes($countryName);

    // Count query
    $countSql = "
        SELECT COUNT(*) AS total
        FROM family_member fm
        WHERE LOWER(fm.country) = LOWER('$safeCountry')
        AND fm.deleted_at IS NULL
        AND fm.is_active = '1'
    ";

    $countSql  = preg_replace('/\s+/', ' ', trim($countSql));
    $countData = $obj->select($countSql);

    if ($countData === false || $countData === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    $total = (int)$countData[0]['total'];

    if ($total === 0) {
        return [
            "code"    => "200",
            "success" => "0",
            "message" => "No members found for this country.",
            "data"    => []
        ];
    }

    // Main query
    $csql = "
        SELECT
            fm.id,
            CONCAT(fm.surname, ' ', fm.name)        AS name,
            fm.gender,
            TIMESTAMPDIFF(YEAR, fm.dob, CURDATE())  AS age,
            fm.city,
            fm.state,
            fm.country,
            fm.photo                                AS user_photo,
            COALESCE(fm.education, '')              AS qualification,
            COALESCE(fm.profession, '')             AS occupation,
            COALESCE(fm.blood_group, '')            AS blood_group,
            COALESCE(fm.gotra, '')                  AS gotra,
            fm.admin_status
        FROM family_member fm
        WHERE LOWER(fm.country) = LOWER('$safeCountry')
        AND fm.deleted_at IS NULL
        AND fm.is_active = '1'
        ORDER BY fm.id DESC
        LIMIT $per_page OFFSET $offset
    ";

    $csql  = preg_replace('/\s+/', ' ', trim($csql));
    $posts = $obj->select($csql);

    if ($posts === false || $posts === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    return [
        "code"        => "200",
        "success"     => "1",
        "message"     => "Members fetched successfully.",
        "country"     => $countryName,
        "total"       => $total,
        "page"        => $page,
        "per_page"    => $per_page,
        "total_pages" => (int)ceil($total / $per_page),
        "data"        => $posts
    ];
}
?>