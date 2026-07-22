<?php
include_once("config/common.php");

//$taluka_code = $_POST['taluka_code'];
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 

function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);
	$query = "SELECT id,district_id,taluka_name FROM list_taluka WHERE district_id = '".$data['district_id']."'";
	$listData = $obj->select($query);

	if (count($listData) > 0) {
		$response = array("code"=>"200","success"=>"1","message" => "Data list","data"=>$listData);	
	}else{
		$response = array("code"=>"200","success"=>"0","message" => "Data not found","data"=>"");
	}

	return $response;

}
?>