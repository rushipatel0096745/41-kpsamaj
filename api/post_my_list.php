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


        $csql = "SELECT fm_post.name, p.* FROM posts p LEFT JOIN family_member fm_post ON fm_post.id = p.member_id LEFT JOIN family_registration fr_post ON fr_post.family_id = fm_post.family_id 
LEFT JOIN family_member fm_login ON fm_login.id = '".$data['member_id']."' LEFT JOIN family_registration fr_login ON fr_login.family_id = fm_login.family_id WHERE p.member_id = '".$data['member_id']."' GROUP BY p.id ORDER BY p.createdon DESC LIMIT 10 OFFSET ".$data['page']."";
        $posts = $obj->select($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Post My List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 