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
    $page     = !empty($data['page'])     ? (int)$data['page']     : 1;
    $per_page = !empty($data['per_page']) ? (int)$data['per_page'] : 10;
    $offset   = ($page - 1) * $per_page;

    if ($memberId <= 0) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "member_id is required.",
            "data"    => []
        ];
    }

    // Check member exists
    $memberCheck = $obj->select("SELECT id, family_id FROM family_member WHERE id = $memberId AND deleted_at IS NULL LIMIT 1");

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

    $familyId = $memberCheck[0]['family_id'];
    $safeFamily = addslashes($familyId);

    // Get shakhe from family_registration
    $shakheSql = "SELECT shakhe FROM family_registration WHERE family_id = '$safeFamily' LIMIT 1";
    $shakheSql   = preg_replace('/\s+/', ' ', trim($shakheSql));
    $shakheCheck = $obj->select($shakheSql);

    if ($shakheCheck === false || $shakheCheck === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    if (empty($shakheCheck)) {
        return [
            "code"    => "404",
            "success" => "0",
            "message" => "Family registration not found.",
            "data"    => []
        ];
    }

    $shakheId = (int)$shakheCheck[0]['shakhe'];

    // Count total besanas for this shakhe
    $countSql  = "SELECT COUNT(*) AS total FROM form_besana_posters WHERE deceased_shakhe = $shakheId";
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

    // Fetch besanas for this shakhe
    // $csql = "SELECT fbp.id, fbp.member_id, fbp.deceased_name, fbp.deceased_photo, fbp.deceased_native, fbp.deceased_shakhe, fbp.date_of_demise, fbp.age_of_death, fbp.besna_day, fbp.besna_date, fbp.besna_time_from, fbp.besna_time_to, fbp.besna_location, fbp.besna_venue_hall, fbp.prayer_note, fbp.createdon, fbp.updatedon FROM form_besana_posters fbp INNER JOIN family_member fm ON fm.id = fbp.member_id INNER JOIN family_registration fr ON fr.family_id = fm.family_id WHERE fr.shakhe = $shakheId ORDER BY fbp.id DESC LIMIT $per_page OFFSET $offset";
    // $csql  = preg_replace('/\s+/', ' ', trim($csql));
    // $posts = $obj->select($csql);

    $csql = "SELECT id, member_id, deceased_name, deceased_photo, deceased_generated_photo, deceased_native, deceased_shakhe, date_of_demise, age_of_death, besna_day, besna_date, besna_time_from, besna_time_to, besna_location, besna_venue_hall, prayer_note, createdon, updatedon FROM form_besana_posters WHERE deceased_shakhe = $shakheId ORDER BY id DESC LIMIT $per_page OFFSET $offset";
    $csql  = preg_replace('/\s+/', ' ', trim($csql));
    $posts = $obj->select($csql);

    // DEBUG
    // return [
    //     "debug"  => true,
    //     "query"  => $csql,
    //     "posts"  => $posts
    // ];

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
            "shakhe_id"   => $shakheId,
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
        "message" => "No besana records found for your shakhe.",
        "data"    => []
    ];
}
