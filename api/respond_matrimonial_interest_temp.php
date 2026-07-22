<?php
include_once("config/common.php");

$json = file_get_contents('php://input');

$file = $_FILES;
//$file = isset($_FILES['media_url']) ? $_FILES['media_url'] : null;

// Base URL
$baseurl = "https://api.41kpsamaj-foundation.org/"; // change this as needed


$validation = [
    'id'  => ['required' => true],
    'member_id'  => ['required' => true],
    'request_id'  => ['required' => true],
    'action'  => ['required' => true]
];

$result = dynamicInsert("submit_matrimonial_requests", $json, $file, $baseurl, $validation);
echo json_encode($result);


function dynamicInsert($table, $json, $filesData, $baseurl, $validation = []) {
$formData = json_decode($json, true);
    /* field validation code start */
    $errors = [];

    foreach ($validation as $field => $rules) {

        // Required field validation
        if (!empty($rules['required'])) {

            if (
                (!isset($formData[$field]) || trim($formData[$field]) == '') &&
                (!isset($filesData[$field]) || $filesData[$field]['error'] != 0)
            ) {
                $errors[$field] = ucfirst($field) . " is required";
                continue;
            }
        }

        // Email validation
        if (!empty($rules['email']) && !empty($formData[$field])) {

            if (!filter_var($formData[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Invalid email address";
            }
        }

        // Numeric validation
        if (!empty($rules['numeric']) && !empty($formData[$field])) {

            if (!is_numeric($formData[$field])) {
                $errors[$field] = ucfirst($field) . " must be numeric";
            }
        }

        // Length validation
        if (!empty($rules['length']) && !empty($formData[$field])) {

            if (strlen($formData[$field]) != $rules['length']) {
                $errors[$field] = ucfirst($field) . " must be {$rules['length']} digits";
            }
        }

        // File extension validation
        if (
            !empty($rules['allowed'])
            && isset($filesData[$field])
            && $filesData[$field]['error'] == 0
        ) {

            $ext = strtolower(pathinfo($filesData[$field]['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $rules['allowed'])) {
                $errors[$field] = "Allowed file types: " . implode(', ', $rules['allowed']);
            }
        }
    }

    if (!empty($errors)) {

        return [
            "code" => "422",
            "success" => "0",
            "message" => "Validation failed",
            "data" => "",
            "errors" => $errors
        ];
    }
    /* field validation code end */


    $obj = new MySQLCN();

    $columns = [];
    $values = [];

    // Handle normal form fields
    foreach ($formData as $key => $value) {

        // Skip submit buttons
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $columns[] = "`" . $key . "`";
        $values[] = "'" . addslashes(trim($value)) . "'";
    }

    // Handle file uploads
    foreach ($filesData as $field => $file) {

        if ($file['error'] == 0) {

            $uploadDir = "../assets/uploads/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                 $filename  = $baseurl . "assets/uploads/" . $filename;
                $columns[] = "`" . $field . "`";
                $values[] = "'" . addslashes($filename) . "'";
            }
        }
    }

        //$inssql = "INSERT INTO `" . $table . "` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";

        $updatedData = [];

        foreach ($columns as $column) {
            $colName = str_replace("`", "", $column);
            $updatedData[] = "`{$colName}` = VALUES(`{$colName}`)";
        }

        $inssql = "INSERT INTO `" . $table . "` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ") ON DUPLICATE KEY UPDATE " . implode(", ", $updatedData);

    // Debug
    // echo $inssql; die;

    $save = $obj->insert($inssql);

    if ($save > 0) {

        $formData['id'] = $save;

        return [
            "code" => "200",
            "success" => "1",
            "message" => "Record successfully saved.",
            "data" => $formData
        ];

    } else {

        return [
            "code" => "200",
            "success" => "0",
            "message" => "Database insert failed.",
            "data" => $inssql
        ];
    }
}
?>