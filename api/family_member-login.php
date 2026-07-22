<?php
include_once("config/common.php");

$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

function main($json)
{
    $obj = new MySQLCN();
    $data = json_decode($json, true);

    if (empty($data)) {
        return ['code' => '400', 'success' => '0', 'message' => 'Invalid or empty request'];
    }

    // -------------------------
    // VALIDATION
    // -------------------------
    $required = ['username'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['code' => '400', 'success' => '0', 'message' => "Missing field: $field"];
        }
    }


    $userckeck = $obj->select("SELECT * FROM family_member WHERE mobile='" . $data['username'] . "' AND TIMESTAMPDIFF(YEAR, dob, CURDATE()) >= 18");


    if(count($userckeck[0]) > 0){
            $otp = rand(100000, 999999);
            $msgtxt = "Your OTP for Mobile Application is: ".$otp." Valid for 5 minutes. Do not share this OTP with anyone. - EKTALIS GAM PATIDAR DEVELOPMENT FOUNDATION";
            $smssend = $obj->sendOtpSms($data['username'], $msgtxt, '1707178256710427753');

           return [
                'code' => '200',
                'success' => '1',
                'message' => 'OTP sent to registered Mobile No',
                'error' => mysqli_error($obj->CONN),
                'data' => $userckeck[0],
                'otp' => $otp,
                'query' => ''
            ];

    }else{
         return [
            'code' => '500',
            'success' => '0',
            'message' => 'Member not found',
            'error' => mysqli_error($obj->CONN),
            'data' => '',
            'otp' => '',
            'query' => ''
        ];
    }
   
  
}
?>
