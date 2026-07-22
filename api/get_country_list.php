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

    // Count query for total (only countries with members)
    $countSql = "
        SELECT COUNT(*) AS total
        FROM list_countries lc
        INNER JOIN family_member fm
            ON LOWER(fm.country) = LOWER(lc.country_name)
            AND fm.deleted_at IS NULL
            AND fm.is_active = '1'
        WHERE lc.delflag = 0
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

    $csql = "
        SELECT
            lc.id,
            lc.country_name,
            lc.iso_code,
            lc.phone        AS phone_code,
            lc.currency,
            lc.latitude,
            lc.longitude,
            COUNT(fm.id)    AS total_members
        FROM list_countries lc
        INNER JOIN family_member fm
            ON LOWER(fm.country) = LOWER(lc.country_name)
            AND fm.deleted_at IS NULL
            AND fm.is_active  = '1'
        WHERE lc.delflag = 0
        GROUP BY
            lc.id,
            lc.country_name,
            lc.iso_code,
            lc.phone,
            lc.currency,
            lc.latitude,
            lc.longitude
        HAVING COUNT(fm.id) > 0
        ORDER BY total_members DESC, lc.country_name ASC
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

    if (count($posts) > 0) {
        return [
            "code"       => "200",
            "success"    => "1",
            "message"    => "Countries fetched successfully.",
            "total"      => $total,
            "page"       => $page,
            "per_page"   => $per_page,
            "total_pages" => (int)ceil($total / $per_page),
            "data"       => $posts
        ];
    }

    return [
        "code"    => "200",
        "success" => "0",
        "message" => "No countries found.",
        "data"    => []
    ];
}
?>