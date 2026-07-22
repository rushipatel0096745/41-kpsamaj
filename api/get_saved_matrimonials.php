<?php
include_once("config/common.php");
 
//$taluka_code = $_POST['taluka_code'];
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 

function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);
	
    //$query = "SELECT bm.id, bm.member_id,bm.saved_member_id, fm.surname, fm.name FROM bookmark_matrimonials bm, family_member fm WHERE bm.member_id = '".$data['member_id']."' AND fm.id= bm.saved_member_id";


	$query = "SELECT
    bm.id,
    bm.member_id,
    bm.saved_member_id,
    fm.surname,
    fm.name,
    fm.dob,
    lvc.village_city_name AS city,
    'Patidar' AS community,
    '' AS occupation,
    1 AS is_verified,
    le.education AS qualification,
    fmt.user_photo_1
FROM bookmark_matrimonials bm
JOIN family_member fm
    ON fm.id = bm.saved_member_id
LEFT JOIN list_village_city lvc
    ON lvc.id = fm.city
LEFT JOIN form_educations fe
    ON fe.member_id = fm.id
LEFT JOIN list_education le
    ON le.id = fe.education
LEFT JOIN form_matromonials fmt
    ON fmt.member_id = fm.id
WHERE bm.member_id = '".$data['member_id']."'";

	$listData = $obj->select($query);

	if (count($listData) > 0) {
		$response = array("code"=>"200","success"=>"1","message" => "Data list","data"=>$listData);	
	}else{
		$response = array("code"=>"200","success"=>"0","message" => "Data not found","data"=>"");
	}

	return $response;

}
?>


