<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

    
        $csql = "SELECT fm.id, fm.family_id, fm.name, fm.surname,cr.from_member_id, cr.to_member_id,cr.is_accepted FROM chat_request cr, family_member fm WHERE cr.to_member_id = '".$data['member_id']."' AND cr.to_member_id = fm.id LIMIT 10 OFFSET ".$data['page']."";
        $posts = $obj->select($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Post List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
       
    
	return $response;
}

?> 