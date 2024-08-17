<?php
// Define URL
$url = "https://api.mercadolibre.com/oauth/token";

// Function to renew token
function renew_token($last_refresh_token) {
    global $url;

    $data = array(
        "grant_type" => "refresh_token",
        "client_id" => "5829758725953784",
        "client_secret" => "k2fLgpMWljTTJoHSQs9eMeg1lTgm1JOq",
        "refresh_token" => $last_refresh_token
    );

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session
    $response = curl_exec($ch);

    // Get HTTP status code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close cURL session
    curl_close($ch);

    if ($http_code == 200) {
        $token_info = json_decode($response, true);
        $access_token = $token_info['access_token'];
        $refresh_token = $token_info['refresh_token'];

        // Save the new access_token and refresh_token to a file
        file_put_contents('tokens.json', json_encode($token_info));

        return array($access_token, $refresh_token);
    } else {
        echo "Failed to renew token: " . $http_code . " " . $response;
        return array(null, null);
    }
}

// Function to read the tokens from file
function get_tokens() {
    if (file_exists('tokens.json')) {
        $token_info = json_decode(file_get_contents('tokens.json'), true);
        return array($token_info['access_token'], $token_info['refresh_token']);
    } else {
        return renew_token("TG-6660b78d4ec9f800013c51a1-1204465713"); // Initial refresh token
    }
}

// Get the access token and refresh token
list($access_token, $refresh_token) = get_tokens();

// If tokens are obtained successfully, ensure future renewals use the latest refresh token
if ($access_token && $refresh_token) {
    list($access_token, $refresh_token) = renew_token($refresh_token);
}

// Print the access token and refresh token
echo "Access Token: " . $access_token . "\n";
echo "Refresh Token: " . $refresh_token . "\n";
?>
