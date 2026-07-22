<?php
include_once("config/common.php");
include_once("token_check.php");

$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

function main($json)
{
    $obj = new MySQLCN();
    $data = json_decode($json, true);

    $search   = trim($data['search'] ?? '');
    $gender   = trim($data['gender'] ?? '');
    $age_from = trim($data['age_from'] ?? '');
    $age_to   = trim($data['age_to'] ?? '');

    // Default pagination
    $page     = !empty($data['page']) ? (int)$data['page'] : 0;
    $per_page = !empty($data['per_page']) ? (int)$data['per_page'] : 10;

    // Calculate offset
    $offset = ($page - 1) * $per_page;

    // current member
    $currentMemberId = (int)($data['member_id'] ?? 0);

    $where = [];

    // Search by name
    if (!empty($search)) {
        $where[] = "fm.name LIKE '%" . addslashes($search) . "%'";
    }

    // Gender filter
    if (!empty($gender)) {
        $where[] = "fm.gender = '" . addslashes($gender) . "'";
    }

    // Age filter
    if ($age_from !== '' && $age_to !== '') {
        $where[] = "TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()) BETWEEN " . (int)$age_from . " AND " . (int)$age_to;
    } elseif ($age_from !== '') {
        $where[] = "TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()) >= " . (int)$age_from;
    } elseif ($age_to !== '') {
        $where[] = "TIMESTAMPDIFF(YEAR, fm.dob, CURDATE()) <= " . (int)$age_to;
    }

    // $csql = "SELECT fm.id AS member_id,fm.* FROM family_member fm";
    $csql = "SELECT
                fm.id AS member_id,
                fm.*,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM submit_matrimonial_requests smr
                        WHERE smr.member_id = $currentMemberId
                        AND smr.request_id = fm.id
                    )
                    THEN '1'
                    ELSE '0'
                END AS is_interested
            FROM family_member fm";

    if (!empty($where)) {
        $csql .= " WHERE " . implode(" AND ", $where);
    }

    $csql .= " ORDER BY fm.id DESC LIMIT $per_page OFFSET $offset";

    $posts = $obj->select($csql);

    if (count($posts) > 0) {
        $response = [
            "code" => "200",
            "success" => "1",
            "message" => "Data list.",
            "data" => $posts
        ];
    } else {
        $response = [
            "code" => "200",
            "success" => "0",
            "message" => "No result found.",
            "data" => $csql
        ];
    }

    return $response;
}
?>