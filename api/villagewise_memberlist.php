<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

       
        $csql = "SELECT n.*,fr.family_id,COUNT(fm.id) AS member_count From native n INNER JOIN family_registration fr ON fr.native = n.id INNER JOIN family_member fm ON fr.family_id = fm.family_id GROUP BY n.id,n.native_name";
        $posts = $obj->select($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 