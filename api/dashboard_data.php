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
    $memberSql  = "SELECT id FROM family_member WHERE id = $memberId AND deleted_at IS NULL LIMIT 1";
    $memberData = $obj->select($memberSql);

    if ($memberData === false || $memberData === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    if (empty($memberData)) {
        return [
            "code"    => "404",
            "success" => "0",
            "message" => "Member not found.",
            "data"    => []
        ];
    }

    // Total members
    $totalData   = $obj->select("SELECT COUNT(*) AS total FROM family_member WHERE deleted_at IS NULL");

    // Total male
    $maleData    = $obj->select("SELECT COUNT(*) AS total 
                                FROM family_member 
                                WHERE gender = 'Male' AND deleted_at IS NULL");

    // Total female
    $femaleData  = $obj->select("SELECT COUNT(*) AS total 
                                FROM family_member 
                                WHERE gender = 'Female' AND deleted_at IS NULL");

    // Total countries that have members
    // $countryData = $obj->select("SELECT COUNT(DISTINCT fm.country) AS total 
    //                             FROM family_member fm 
    //                             WHERE fm.country IS NOT NULL 
    //                             AND fm.country != '' 
    //                             AND fm.deleted_at IS NULL");

    // Total countries that have members
    $countryData = $obj->select("SELECT COUNT(DISTINCT lc.id) AS total 
                                FROM list_countries lc 
                                INNER JOIN family_member fm 
                                ON LOWER(fm.country) = LOWER(lc.country_name) 
                                AND fm.deleted_at IS NULL thid is the ruhiks
                                WHERE lc.delflag = 0");

    // Total registered villages
    $villageData = $obj->select("SELECT COUNT(DISTINCT fm.village_id) AS total 
                                FROM family_member fm 
                                INNER JOIN list_village_city lvc 
                                ON lvc.id = fm.village_id 
                                WHERE fm.village_id IS NOT NULL 
                                AND lvc.delflag = 0 
                                AND fm.deleted_at IS NULL");

    // Total villages
    $villageDataTemp = $obj->select("SELECT COUNT(DISTINCT id) AS total 
                                FROM list_village_city");

    if (
        $totalData   === false || $totalData   === null ||
        $maleData    === false || $maleData    === null ||
        $femaleData  === false || $femaleData  === null ||
        $countryData === false || $countryData === null ||
        $villageData === false || $villageData === null
    ) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    return [
        "code"    => "200",
        "success" => "1",
        "message" => "Data fetched successfully.",
        "data"    => [
            "total_members"   => (int)$totalData[0]['total'],
            "total_male"      => (int)$maleData[0]['total'],
            "total_female"    => (int)$femaleData[0]['total'],
            "total_countries" => (int)$countryData[0]['total'],
            "total_villages"  => (int)$villageDataTemp[0]['total'],
            "total_mandal"    => 0
        ]
    ];
}
