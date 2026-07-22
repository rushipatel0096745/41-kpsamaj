<?php
include_once("config/common.php");

$data = $_REQUEST;
$file = $_FILES;
//$file = isset($_FILES['media_url']) ? $_FILES['media_url'] : null;

// Base URL
$baseurl = "https://api.41kpsamaj-foundation.org/"; // change this as needed
$result = dynamicInsert("test_form", $data, $file, $baseurl);
echo json_encode($result);
function dynamicInsert($table, $formData, $filesData, $baseurl) {

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