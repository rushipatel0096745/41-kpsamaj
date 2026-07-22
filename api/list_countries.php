<?php
include_once("config/common.php");

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

	// Count total countries that have members
	// $countSql  = "SELECT COUNT(DISTINCT lc.id) AS total FROM list_countries lc INNER JOIN family_member fm ON LOWER(fm.country) = LOWER(lc.country_name) AND fm.deleted_at IS NULL AND fm.is_active = '1' WHERE lc.delflag = 0";
	$countSql = "SELECT COUNT(*) AS total FROM list_countries WHERE delflag = 0";

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

	// $query = "SELECT lc.id, lc.country_name, COUNT(fm.id) AS total_members FROM list_countries lc INNER JOIN family_member fm ON LOWER(fm.country) = LOWER(lc.country_name) AND fm.deleted_at IS NULL AND fm.is_active = '1' WHERE lc.delflag = 0 GROUP BY lc.id, lc.country_name HAVING COUNT(fm.id) > 0 ORDER BY total_members DESC, lc.country_name ASC LIMIT $per_page OFFSET $offset";
	$query = "SELECT lc.id, lc.country_name, COUNT(fm.id) AS total_members FROM list_countries lc LEFT JOIN family_member fm ON LOWER(fm.country) = LOWER(lc.country_name) AND fm.deleted_at IS NULL AND fm.is_active = '1' WHERE lc.delflag = 0 GROUP BY lc.id, lc.country_name ORDER BY total_members DESC, lc.country_name ASC LIMIT $per_page OFFSET $offset";

	$listData = $obj->select($query);

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
			"code"        => "200",
			"success"     => "1",
			"message"     => "Data list",
			"total"       => $total,
			"page"        => $page,
			"per_page"    => $per_page,
			"total_pages" => (int)ceil($total / $per_page),
			"data"        => $listData
		];
	}

	return [
		"code"    => "200",
		"success" => "0",
		"message" => "Data not found",
		"data"    => []
	];
}
