<?php
include_once("config/common.php");

$json   = file_get_contents('php://input');
$result = main($json);
echo json_encode($result);

function main($json)
{
    $obj  = new MySQLCN();
    $data = json_decode($json, true);

    $id        = (int)($data['id']        ?? 0);
    $memberId  = (int)($data['member_id'] ?? 0);
    $requestId = (int)($data['request_id'] ?? 0);
    $action    = trim($data['action'] ?? '');

    // Validate
    $errors = [];
    if ($id        <= 0)                              $errors['id']         = "Id is required";
    if ($memberId  <= 0)                              $errors['member_id']  = "Member_id is required";
    if ($requestId <= 0)                              $errors['request_id'] = "Request_id is required";
    if (!in_array($action, ['accept', 'decline']))    $errors['action']     = "Action must be accept or decline";

    if (!empty($errors)) {
        return [
            "code"    => "422",
            "success" => "0",
            "message" => "Validation failed",
            "data"    => "",
            "errors"  => $errors
        ];
    }

    // Check the request exists and belongs to this member
    $checkSql = "SELECT id FROM submit_matrimonial_requests 
                 WHERE id = $id 
                 AND member_id  = $memberId 
                 AND request_id = $requestId 
                 LIMIT 1";
    $existing = $obj->select($checkSql);

    if ($existing === false || $existing === null) {
        return [
            "code"    => "500",
            "success" => "0",
            "message" => "Query failed.",
            "data"    => []
        ];
    }

    if (empty($existing)) {
        return [
            "code"    => "404",
            "success" => "0",
            "message" => "Request not found.",
            "data"    => []
        ];
    }

    // Update action
    $updSql = "UPDATE submit_matrimonial_requests 
               SET action     = '$action', 
                   updatedon  = CURRENT_TIMESTAMP 
               WHERE id         = $id 
               AND member_id  = $memberId 
               AND request_id = $requestId";

    $updated = $obj->edit($updSql);

    if ($updated !== false) {
        $message = $action === 'accept'
            ? "Interest accepted successfully."
            : "Interest declined successfully.";

        return [
            "code"    => "200",
            "success" => "1",
            "message" => $message,
            "data"    => []
        ];
    }

    return [
        "code"    => "200",
        "success" => "0",
        "message" => "Failed to update request.",
        "data"    => []
    ];
}
?>