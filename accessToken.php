function getAccessToken() {
    include_once __DIR__ . '/config.php';

    $consumerKey = CONSUMER_KEY;
    $consumerSecret = CONSUMER_SECRET;
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic ' . $credentials
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false // TEMPORARY for local testing only
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'cURL Error: ' . curl_error($curl);
        curl_close($curl);
        return false;
    }

    curl_close($curl);

    // Trim extra whitespace, just in case
    $response = trim($response);

    // Debug print the raw response
    // echo "DEBUG RAW RESPONSE: [$response]\n";

    $result = json_decode($response);

    if (!$result || !isset($result->access_token)) {
        echo "❌ Failed to decode response or missing access_token\n";
        echo "🔍 Raw response:\n" . $response;
        return false;
    }

    return $result->access_token;
}
