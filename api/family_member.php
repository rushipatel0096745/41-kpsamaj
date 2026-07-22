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
    $required = ['family_id', 'native', 'shakhe', 'gender', 'marital_status', 'surname', 'name', 'fatherName', 'grandFathername', 'dob'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['code' => '400', 'success' => '0', 'message' => "Missing field: $field"];
        }
    }

    if (!in_array($data['gender'], ['Male', 'Female', 'Other'])) {
        return ['code' => '400', 'success' => '0', 'message' => 'Invalid gender value'];
    }

  

    // -------------------------
    // FETCH NATIVE & SHAKHE NAMES
    // -------------------------
    $nativeRow = $obj->select("SELECT native_name FROM native WHERE id='" . intval($data['native']) . "'");
    $shakheRow = $obj->select("SELECT shakhe_name FROM shakhe WHERE id='" . intval($data['shakhe']) . "'");

    $nativeName = $nativeRow[0]['native_name'] ?? '';
    $shakheName = $shakheRow[0]['shakhe_name'] ?? '';

   
    // -------------------------
    // INSERT INTO family_registration
    // -------------------------
    $sql_query = "INSERT INTO family_member (
        id, family_id, surname, name, fathername, grand_father, email, mobile, gender, marital_status, dob, primary_head
    ) VALUES (
        " . (isset($data['id']) ? "'" . mysqli_real_escape_string($obj->CONN, $data['id']) . "'" : "NULL") . ",
        '" . mysqli_real_escape_string($obj->CONN, $data['family_id']) . "',
        '" . mysqli_real_escape_string($obj->CONN, $data['surname']) . "',
        '" . mysqli_real_escape_string($obj->CONN, $data['name']) . "',
        '" . mysqli_real_escape_string($obj->CONN, $data['fatherName']) . "',
        " . (empty($data['grandFathername']) ? "NULL" : "'" . mysqli_real_escape_string($obj->CONN, $data['grandFathername']) . "'") . ",
        " . (empty($data['email']) ? "NULL" : "'" . mysqli_real_escape_string($obj->CONN, $data['email']) . "'") . ",
        " . (empty($data['mobile_no']) ? "NULL" : "'" . mysqli_real_escape_string($obj->CONN, $data['mobile_no']) . "'") . ",
        '" . mysqli_real_escape_string($obj->CONN, $data['gender']) . "',
        '" . mysqli_real_escape_string($obj->CONN, $data['marital_status']) . "',
        '" . mysqli_real_escape_string($obj->CONN, $data['dob']) . "',
        'Y'
    )
    ON DUPLICATE KEY UPDATE
        surname = VALUES(surname),
        name = VALUES(name),
        fathername = VALUES(fathername),
        grand_father = VALUES(grand_father),
        email = VALUES(email),
        mobile = VALUES(mobile),
        gender = VALUES(gender),
        marital_status = VALUES(marital_status),
        primary_head = VALUES(primary_head)";


    $insert = $obj->insert($sql_query);
    // -------------------------
    // ON SUCCESSFUL FAMILY INSERT
    // -------------------------
  
    if ($insert > 0) {
        
        // prepare member insert query
        $userdata = $obj->select("SELECT * FROM family_member WHERE id='" . $insert . "'");
        
			
            return [
                'code' => '200',
                'success' => '1',
                'message' => 'Member registered',
                'error' => mysqli_error($obj->CONN),
                'data' => $userdata[0],
                'query' => ''
            ];
        
    }

    // -------------------------
    // FAMILY REGISTRATION FAILED
    // -------------------------
    return [
        'code' => '500',
        'success' => '0',
        'message' => 'Member registration failed',
        'error' => mysqli_error($obj->CONN),
        'data' => '',
        'query' => ''
    ];
}
?>
