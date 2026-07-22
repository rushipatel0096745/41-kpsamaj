<?php
include_once("config/common.php");

//$taluka_code = $_POST['taluka_code'];
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 

function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);
	$query = "SELECT id, state_id, district_name FROM list_district WHERE state_id = '".$data['state_id']."'";
	$listData = $obj->select($query);

	if ((isset($data['mobile']) or (isset($data['email']) and isset($data['otp']) {
		$response = array(
            "code" => "200",
            "success" => "1",
            "message" => "User successfuly deleted",
            "data" => ""
        );
	}else if((isset($data['mobile']) or (isset($data['email']) and empty($data['otp']){
		 // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        $response = array(
            "code" => "200",
            "success" => "1",
            "message" => "OTP generated successfully",
            "data" => "",
            "otp" => $otp
        );
	}else{
        $response = array(
            "code" => "500",
            "success" => "0",
            "message" => "Somthing went wrong try again",
            "data" => ""
        );
    }

	return $response;

}
?>