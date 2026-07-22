<?php
include_once("config/common.php");

$json = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);
//echo $result;

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
    $userckeck = $obj->select("SELECT id FROM family_member WHERE mobile='" . $data['mobile_no'] . "' AND TIMESTAMPDIFF(YEAR, dob, CURDATE()) >= 18");
    if(count($userckeck[0]) <= 0){
        
        $familyckeck = $obj->select("SELECT id FROM family_registration WHERE mobile_no='" . $data['mobile_no'] . "'");
         
        if(count($familyckeck[0]) <= 0){
            $required = ['native', 'shakhe', 'gender', 'marital_status', 'surname', 'name', 'fatherName', 'grandFathername', 'dob'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['code' => '400', 'success' => '0', 'message' => "Missing field: $field"];
                }
            }

            if (!in_array($data['gender'], ['Male', 'Female', 'Other'])) {
                return ['code' => '400', 'success' => '0', 'message' => 'Invalid gender value'];
            }

            if (!in_array($data['live_abroad'], ['Yes', 'No'])) {
                return ['code' => '400', 'success' => '0', 'message' => 'Invalid live_abroad value'];
            }

            if ($data['live_abroad'] == 'No' && empty($data['mobile_no'])) {
                return ['code' => '400', 'success' => '0', 'message' => 'Mobile number required when live_abroad = No'];
            }

            if ($data['live_abroad'] == 'Yes' && empty($data['email'])) {
                return ['code' => '400', 'success' => '0', 'message' => 'Email required when live_abroad = Yes'];
            }

            // -------------------------
            // FETCH NATIVE & SHAKHE NAMES
            // -------------------------
            $nativeRow = $obj->select("SELECT native_name FROM native WHERE id='" . intval($data['native']) . "'");
            $shakheRow = $obj->select("SELECT shakhe_name FROM shakhe WHERE id='" . intval($data['shakhe']) . "'");

            $nativeName = $nativeRow[0]['native_name'] ?? '';
            $shakheName = $shakheRow[0]['shakhe_name'] ?? '';

            // -------------------------
            // GENERATE FAMILY ID
            // -------------------------
            $prefix = strtoupper(substr($nativeName, 0, 2) . substr($shakheName, 0, 2));
            $familyCode = $prefix . rand(10000, 99999);

            // -------------------------
            // ENCRYPT PASSWORD
            // -------------------------
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            // -------------------------
            // INSERT INTO family_registration
            // -------------------------
            $inssql = "INSERT INTO `family_registration`
                (`native`, `shakhe`, `live_abroad`, `gender`, `marital_status`, `surname`, `name`,
                `fatherName`, `grandFathername`, `email`, `mobile_no`, `password`, `family_id`, `status`)
                VALUES (
                    '" . intval($data['native']) . "',
                    '" . intval($data['shakhe']) . "',
                    '" . $obj->escape($data['live_abroad']) . "',
                    '" . $obj->escape($data['gender']) . "',
                    '" . intval($data['marital_status']) . "',
                    '" . $obj->escape($data['surname']) . "',
                    '" . $obj->escape($data['name']) . "',
                    '" . $obj->escape($data['fatherName']) . "',
                    '" . $obj->escape($data['grandFathername']) . "',
                    '" . $obj->escape($data['email'] ?? '') . "',
                    '" . $obj->escape($data['mobile_no'] ?? '') . "',
                    '" . $passwordHash . "',
                    '" . $familyCode . "',
                    0
                )";

            $insert = $obj->insert($inssql);

            // -------------------------
            // ON SUCCESSFUL FAMILY INSERT
            // -------------------------
            if ($insert) {
                    return [
                        'code' => '200',
                        'success' => '1',
                        'message' => 'Family created',
                        'error' => mysqli_error($obj->CONN),
                        'data' => array('family_id' => $familyCode), 
                        'query' => $insert
                    ];
                
            }

            // -------------------------
            // FAMILY REGISTRATION FAILED
            // -------------------------
            return [
                'code' => '500',
                'success' => '0',
                'message' => 'Family registration failed',
                'error' => mysqli_error($obj->CONN),
                'data' => '', 
                'query' => $inssql
            ];
        }else{
            return [
                'code' => '500',
                'success' => '0',
                'message' => 'Family already existed with same mobile no.',
                'error' => mysqli_error($obj->CONN),
                'data' => '', 
                'query' => $inssql
            ]; 
        }
    }else{
        return [
            'code' => '500',
            'success' => '0',
            'message' => 'Family member already existed with same mobile no.',
            'error' => mysqli_error($obj->CONN),
            'data' => '', 
            'query' => $inssql
        ]; 
    }
}
?>
