ini<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'accessToken.php';
include 'config.php';

if (empty($_POST['phone']) || empty($_POST['amount'])) {
    die(json_encode(['error' => 'Phone number and amount are required.']));
}

$phone = preg_replace('/\D/', '', $_POST['phone']);
$amount = (int)$_POST['amount'];
$accessToken = getAccessToken();

if (!$accessToken) {
    die(json_encode(['error' => 'Failed to get access token']));
}

$timestamp = date('YmdHis');
$password = base64_encode(BUSINESS_SHORTCODE . PASSKEY . $timestamp);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'BusinessShortCode' => BUSINESS_SHORTCODE,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => BUSINESS_SHORTCODE,
        'PhoneNumber' => $phone,
        'CallBackURL' => CALLBACK_URL,
        'AccountReference' => 'Ecommerce123',
        'TransactionDesc' => 'Buying Product'
    ])
]);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo json_encode(['error' => curl_error($curl)]);
} else {
    echo $response;
}

curl_close($curl);
?>
