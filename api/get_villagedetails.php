<?php
include_once("config/common.php");
include_once("token_check.php");
$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result); 


function main($json){
	$obj = new MySQLCN();
	$data = json_decode($json, true);
	$common_data = array();

       
        $csql = "SELECT COUNT(fm.id) AS member_count From native n INNER JOIN family_registration fr ON fr.native = n.id INNER JOIN family_member fm ON fr.family_id = fm.family_id WHERE n.id='".$data['village_id']."' GROUP BY n.id,n.native_name";
        $posts = $obj->select($csql);
		$mailsql = "SELECT COUNT(fm.id) AS member_count From native n INNER JOIN family_registration fr ON fr.native = n.id INNER JOIN family_member fm ON fr.family_id = fm.family_id WHERE n.id='".$data['village_id']."' AND fm.gender = 'Male' GROUP BY n.id,n.native_name";
		$mailposts = $obj->select($mailsql);
		if($mailposts[0]['member_count'] > 0){
			$common_data['total_male'] = $mailposts[0]['member_count'];
		}
		else{
			$common_data['total_male'] = '0';
		}
		
		$femailsql = "SELECT COUNT(fm.id) AS member_count From native n INNER JOIN family_registration fr ON fr.native = n.id INNER JOIN family_member fm ON fr.family_id = fm.family_id WHERE n.id='".$data['village_id']."' AND fm.gender = 'Female' GROUP BY n.id,n.native_name";
		$femailposts = $obj->select($femailsql);
		if($femailposts[0]['member_count'] > 0){
			$common_data['total_female'] = $femailposts[0]['member_count'];
		}
		else{
			$common_data['total_female'] = '0';
		}
		
		$common_data['total_count'] = $posts[0]['member_count'];
		
		$shakhesql = "SELECT sh.* ,COUNT(fm.id) AS member_count From shakhe sh INNER JOIN native n ON n.id= sh.native_id LEFT JOIN family_registration fr ON fr.shakhe = sh.id LEFT JOIN family_member fm ON fm.family_id = fr.family_id WHERE n.id='".$data['village_id']."' GROUP BY sh.id;";
		$shakheposts = $obj->select($shakhesql);
		$common_data['shakhe_data'] = $shakheposts;
		
        if(count($posts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member List.","data"=>$common_data);
        }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
        }   
      
    
	return $response;
}

?> 