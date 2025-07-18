<?php
$data = file_get_contents('php://input');
$logFile = "mpesa_response.json";

file_put_contents($logFile, $data, FILE_APPEND);

// Optional: Insert into DB
$response = json_decode($data, true);
if (isset($response['Body']['stkCallback']['ResultCode']) && $response['Body']['stkCallback']['ResultCode'] == 0) {
    // Payment successful - extract transaction details and store
    //new branch
}
?>
