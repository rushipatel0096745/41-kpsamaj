<?php
include_once("config/common.php");
// include_once("token_check.php");

$json   = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

function main($json)
{
    $obj  = new MySQLCN();
    $data = json_decode($json, true);

    $besanaId = (int)($data['besana_id'] ?? 0);
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

    if ($besanaId <= 0) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "besana_id is required.",
            "data"    => []
        ];
    }

    // Get besana detail
    $csql = "SELECT fbp.id, fbp.member_id, fbp.deceased_name, fbp.deceased_photo, fbp.deceased_generated_photo, fbp.deceased_native, fbp.deceased_shakhe, fbp.date_of_demise, fbp.age_of_death, fbp.besna_day, fbp.besna_date, fbp.besna_time_from, fbp.besna_time_to, fbp.besna_location, fbp.besna_venue_hall, fbp.prayer_note, fbp.createdon, fbp.updatedon FROM form_besana_posters fbp WHERE fbp.id = $besanaId LIMIT 1";

    $posts = $obj->select($csql);

    if ($posts === false || $posts === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    if (empty($posts)) {
        return [
            "code"    => "404",
            "success" => "0",
            "message" => "Besana not found.",
            "data"    => []
        ];
    }

    $besana = $posts[0];

    // Get signatories for this besana
    $sigSql      = "SELECT id, signatory_name, signatory_relation FROM besana_signatories WHERE besana_id = $besanaId ORDER BY id ASC";
    $signatories = $obj->select($sigSql);

    $besana['signatories'] = ($signatories && count($signatories) > 0) ? $signatories : [];

    return [
        "code"    => "200",
        "success" => "1",
        "message" => "Besana detail fetched successfully.",
        "data"    => $besana
    ];
}
?>