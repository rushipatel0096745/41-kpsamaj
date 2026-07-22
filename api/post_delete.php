<?php
include_once("config/common.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

    $csql = "DELETE FROM posts WHERE id = '".$data['vid']."'";
    $advertise = $obj->delete($csql);
       
    $response = array("code"=>"200","success"=>"1","message" => "Post Deleted.","data"=>'');
        
  	return $response;
}

?> 