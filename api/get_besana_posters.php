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

    $memberId = (int)($data['member_id'] ?? 0);

    if ($memberId <= 0) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "member_id is required.",
            "data"    => []
        ];
    }

    // Check member exists
    $memberCheck = $obj->select("SELECT id FROM family_member WHERE id = $memberId AND deleted_at IS NULL LIMIT 1");

    if ($memberCheck === false || $memberCheck === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    if (empty($memberCheck)) {
        return [
            "code"    => "404",
            "success" => "0",
            "message" => "Member not found.",
            "data"    => []
        ];
    }

    $page     = !empty($data['page'])     ? (int)$data['page']     : 1;
    $per_page = !empty($data['per_page']) ? (int)$data['per_page'] : 10;
    $offset   = ($page - 1) * $per_page;

    // Count total
    $countSql  = "SELECT COUNT(*) AS total FROM form_besana_posters WHERE 1";
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

    $csql = "SELECT id, member_id, deceased_name, deceased_photo, deceased_native, deceased_shakhe, date_of_demise, age_of_death, besna_day, besna_date, besna_time_from, besna_time_to, besna_location, besna_venue_hall, prayer_note, createdon, updatedon FROM form_besana_posters ORDER BY id DESC LIMIT $per_page OFFSET $offset";

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
            "code"        => "200",
            "success"     => "1",
            "message"     => "Besana list fetched successfully.",
            "total"       => $total,
            "page"        => $page,
            "per_page"    => $per_page,
            "total_pages" => (int)ceil($total / $per_page),
            "data"        => $posts
        ];
    }

    return [
        "code"    => "200",
        "success" => "0",
        "message" => "No besana records found.",
        "data"    => []
    ];
}
?>