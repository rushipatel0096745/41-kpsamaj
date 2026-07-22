<?php
include_once("config/common.php");
 
//$taluka_code = $_POST['taluka_code'];
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 

function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);
	
    $query = "SELECT
    mr.id,
    mr.member_id,
    mr.request_id,
    fm.surname,
    fm.name
FROM submit_matrimonial_requests mr
JOIN family_member fm
    ON fm.id = mr.request_id
WHERE mr.request_id = '".$data['member_id']."'";
	
	$listData = $obj->select($query);

	if (count($listData) > 0) {
		$response = array("code"=>"200","success"=>"1","message" => "Data list","data"=>$listData);	
	}else{
		$response = array("code"=>"200","success"=>"0","message" => "Data not found","data"=>"");
	}

	return $response;

}
?>


