<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

       
        $csql = "SELECT fm_all.*, (SELECT is_accepted FROM chat_request WHERE from_member_id = '".$data['member_id']."' AND to_member_id = fm_all.id) AS is_chat_accepted FROM family_member fm_all JOIN family_registration fr_all ON fr_all.family_id = fm_all.family_id WHERE fr_all.native = (SELECT fr.native FROM family_member fm JOIN family_registration fr ON fr.family_id = fm.family_id WHERE fm.id = '".$data['member_id']."') ORDER BY fm_all.id ASC LIMIT 10 OFFSET ".$data['page'];
        $posts = $obj->select($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 