<?php
include_once("config/common.php");

$data   = json_decode(file_get_contents('php://input'), true);
$result = main($data);
echo json_encode($result);

function main($data)
{
    $obj = new MySQLCN();

    $besanaId   = (int)($data['besana_id'] ?? 0);
    $signatories = $data['signatories'] ?? [];

    // Validate
    if ($besanaId <= 0) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "Validation failed",
            "errors"  => ["besana_id" => "Besana_id is required"],
            "data"    => []
        ];
    }

    if (empty($signatories) || !is_array($signatories)) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "Validation failed",
            "errors"  => ["signatories" => "Signatories array is required and cannot be empty"],
            "data"    => []
        ];
    }

    // Validate each signatory
    $errors = [];
    foreach ($signatories as $index => $signatory) {
        if (empty(trim($signatory['signatory_name'] ?? ''))) {
            $errors["signatories[$index][signatory_name]"] = "Signatory name is required";
        }
        // if (empty(trim($signatory['signatory_relation'] ?? ''))) {
        //     $errors["signatories[$index][signatory_relation]"] = "Signatory relation is required";
        // }
    }

    if (!empty($errors)) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "Validation failed",
            "errors"  => $errors,
            "data"    => []
        ];
    }

    // Build bulk insert
    $rows = [];
    foreach ($signatories as $signatory) {
        $name     = addslashes(trim($signatory['signatory_name']));
        $relation = addslashes(trim($signatory['signatory_relation']));
        $rows[]   = "($besanaId, '$name', '$relation', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    }

    $delSql = "DELETE FROM `besana_signatories` WHERE besana_id = $besanaId";
    $obj->delete($delSql);

    $inssql = "INSERT INTO `besana_signatories` 
                (`besana_id`, `signatory_name`, `signatory_relation`, `createdon`, `updatedon`) 
                VALUES " . implode(", ", $rows);

    $save = $obj->insert($inssql);

    if ($save > 0) {
        $fetchSql     = "SELECT id, besana_id, signatory_name, signatory_relation, createdon FROM besana_signatories WHERE besana_id = $besanaId ORDER BY id ASC";
        $fetchSql     = preg_replace('/\s+/', ' ', trim($fetchSql));
        $insertedRows = $obj->select($fetchSql);

        return [
            "code"    => "200",
            "success" => "1",
            "message" => count($signatories) . " signatory(s) added successfully.",
            "data"    => [
                "besana_id"   => $besanaId,
                "inserted"    => count($signatories),
                "signatories" => $insertedRows ?? []
            ]
        ];
    }

    return [
        "code"    => "200",
        "success" => "0",
        "message" => "Database insert failed.",
        "data"    => $inssql
    ];
}
?>


