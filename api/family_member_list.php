<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

        $csql = "SELECT * FROM family_member WHERE family_id = (SELECT family_id FROM family_member WHERE id = '".$data['member_id']."' AND primary_head = 'Y');";
        $posts = $obj->select($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 