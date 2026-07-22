<?php
include_once("config/common.php");

$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

function main($json) {
    $obj = new MySQLCN();
    

    $data = json_decode($json, true);

    // Validate input
    if (empty($data['username'])) {
        return array("code" => "400", "success" => "0", "message" => "Username (mobile or email) is required");
    }

    // Sanitize input
    $username = mysqli_real_escape_string($obj->CONN, $data['username']);

    // Query
    $query = "SELECT id FROM family_registration WHERE mobile_no = '$username' OR email = '$username'";
    $listData = $obj->select($query);

    if (!empty($listData) && count($listData) > 0) {
        // User already exists
        $response = array(
            "code" => "200",
            "success" => "0",
            "message" => "Family already created with the same mobile number",
            "data" => ""
        );
    } else {
        // Generate 6-digit OTP
       
        $otp = rand(100000, 999999);
        $msgtxt = "Your Registration OTP:".$otp." - 41kpsamaj";
        $smssend = $obj->sendOtpSms($username, $msgtxt, '1707161728692911461');

        $response = array(
            "code" => "200",
            "success" => "1",
            "message" => "OTP generated successfully",
            "otp" => $otp
        );
    }

    return $response;
}
?>
