<?php
include_once("config/common.php");

$data = $_REQUEST;
$file = isset($_FILES['media_url']) ? $_FILES['media_url'] : null;

// Base URL
$baseurl = "https://api.41kpsamaj-foundation.org/"; // change this as needed

$result = main($data, $file, $baseurl);
echo json_encode($result);


function main($data, $file, $baseurl)
{
  
    $obj = new MySQLCN();

    $columns = [];
    $values  = [];
    $updatedData = [];

    // ---------- FILE UPLOAD ----------
    if (!empty($file) && isset($file['name']) && $file['error'] === 0) {

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $valid_exts = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $valid_exts)) {

            $filename = 'v1-' . time() . '.' . $ext;
            $upload_dir = "../assets/posts/";

            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $upload_path = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $columns[] = 'media_url';
                $values[]  = "'" . $baseurl . "assets/posts/" . $filename . "'";
                $updatedData[] = "media_url = '" . $baseurl . "assets/posts/" . $filename . "'";
            } else {
                return ["code"=>"200","success"=>"0","message"=>"File upload failed.","data"=>''];
            }

        } else {
            return ["code"=>"200","success"=>"0","message"=>"Invalid file type.","data"=>''];
        }
    }

    // ---------- VALIDATE & PREPARE DATA ----------
    if (!is_array($data) || count($data) === 0) {
        return ["code"=>"200","success"=>"0","message"=>"No input data received.","data"=>""];
    }

    foreach ($data as $column => $value) {
        if ($column === 'id' && empty($value)) continue; // skip empty id
        if ($value === '') continue; // skip empty values

        $column = trim($column);
        $safeValue = addslashes($value);

        $columns[] = "`$column`";
        $values[] = "'$safeValue'";
        $updatedData[] = "`$column` = '$safeValue'";
    }

    // ---------- VALIDATE QUERY PARTS ----------
    if (empty($columns) || empty($values)) {
        return [
            "code" => "200",
            "success" => "0",
            "message" => "No valid data to insert. Please check your form fields.",
            "data" => $data
        ];
    }

    if (empty($updatedData)) {
        // fallback to at least set updatedon = CURRENT_TIMESTAMP
        $updatedData[] = "updatedon = CURRENT_TIMESTAMP()";
    }

    // ---------- BUILD SQL QUERY ----------
    $inssql = "INSERT INTO `posts` (" . implode(", ", $columns) . ") 
               VALUES (" . implode(", ", $values) . ") 
               ON DUPLICATE KEY UPDATE " . implode(", ", $updatedData) . ";";

    // --- Optional debug log for testing ---
    // file_put_contents("debug_log.txt", $inssql.PHP_EOL, FILE_APPEND);

    $save = $obj->insert($inssql);

    if ($save > 0) {
        $data['id'] = $save;
        return ["code"=>"200","success"=>"1","message"=>"Post successfully saved.","data"=>$data];
    } else {
        return ["code"=>"200","success"=>"0","message"=>"Database insert failed.","data"=>$inssql];
    }
}
?>
