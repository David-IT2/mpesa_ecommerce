<?php
// Consumer credentials
$consumer_key = 'wXeEGEpAM1H9wbKZS0wAV39ZOkkGWf5PfjMjsIHP30mfBUha';
$consumer_secret = 'nCRaCpe3Q1DGIhSODBXxAMgFSkrLqHTM8AqC5vYqohg9S55tcmLIOzLngvVZAUb3';

// Encode consumer credentials
$credentials = base64_encode($consumer_key . ':' . $consumer_secret);

// Step 1: Get access token
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
    CURLOPT_HTTPHEADER => ['Authorization: Basic ' . $credentials],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,  // 🚫 Disable cert verification
    CURLOPT_SSL_VERIFYHOST => false   // 🚫 Disable host verification
]);

$response = curl_exec($curl);
if (curl_errno($curl)) {
    echo "Token error: " . curl_error($curl);
    exit;
}
curl_close($curl);

// Extract access token
$token_data = json_decode($response, true);
$access_token = $token_data['access_token'];

// Step 2: Prepare STK Push
$timestamp = date("YmdHis");
$shortcode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$password = base64_encode($shortcode . $passkey . $timestamp);

$payload = [
    'BusinessShortCode' => $shortcode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => 1000,
    'PartyA' => '254795574929',
    'PartyB' => $shortcode,
    'PhoneNumber' => '254795574929',
    'CallBackURL' => 'https://yourdomain.com/callback.php',
    'AccountReference' => 'Hello',
    'TransactionDesc' => 'dave'
];

// Step 3: Send STK Push
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,  // 🚫 Disable cert verification
    CURLOPT_SSL_VERIFYHOST => false,  // 🚫 Disable host verification
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($curl);
if (curl_errno($curl)) {
    echo 'cURL error: ' . curl_error($curl);
} else {
    echo 'STK Push response: ' . $response;
}

curl_close($curl);
?>
