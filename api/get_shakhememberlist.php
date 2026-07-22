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

    //$shakhesql = "SELECT sh.* ,COUNT(fm.id) AS member_count From shakhe sh INNER JOIN native n ON n.id= sh.native_id LEFT JOIN family_registration fr ON fr.shakhe = sh.id LEFT JOIN family_member fm ON fm.family_id = fr.family_id WHERE n.id='".$data['village_id']."' GROUP BY sh.id;";
	$shakhemembersql = "SELECT fm.id,fm.family_id,fm.surname,fm.name,lec.course_name FROM family_member fm INNER JOIN family_registration fr ON fr.family_id = fm.family_id inner join native n ON n.id = fr.native inner Join shakhe sh ON sh.id = fr.shakhe INNER JOIN form_educations fe ON fe.member_id = fm.id INNER JOIN list_education_course lec ON lec.id = fe.education_course   WHERE sh.native_id='".$data['village_id']."' and sh.id='".$data['shakhe_id']."'";
	
	$shakhememberposts = $obj->select($shakhemembersql);
	$common_data['shakhe_member_data'] = $shakhememberposts;
		
    if(count($shakhememberposts) > 0){
            $response = array("code"=>"200","success"=>"1","message" => "Member List.","data"=>$common_data);
    }else{
            $response = array("code"=>"200","success"=>"0","message" => "No result found.","data"=>[]);
    }   
      
    
	return $response;
}

?> 