<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

    
        //$csql = "SELECT * FROM posts WHERE member_id = '".$data['member_id']."'";
        $csql = "UPDATE family_member SET is_active = '".$data['is_active']."' WHERE id = '".$data['member_id']."'";
        $posts = $obj->edit($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member updated List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 