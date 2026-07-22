<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);

       /* $csql = "SELECT * FROM family_registration WHERE native = (SELECT native FROM family_registration WHERE id = '".$data['member_id']."');";*/
	   $csql = "SELECT fm.*, fr.native, n.native_name FROM family_member fm INNER JOIN family_registration fr ON fm.family_id = fr.family_id INNER JOIN native n ON fr.native = n.id INNER JOIN family_registration fr_login ON fr.native = fr_login.native INNER JOIN family_member fm_login ON fr_login.family_id = fm_login.family_id WHERE fm_login.id = '".$data['member_id']."' ORDER BY fm.family_id, fm.name;";
        $posts = $obj->select($csql);
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member List.","data"=>$posts);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 