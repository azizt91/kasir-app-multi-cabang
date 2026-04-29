<?php

// Load Laravel Application (Minimal for Helpers)
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// --- DEBUG SCRIPT FOR FCM ---
echo "<h1>FCM DEEP DEBUGGER</h1>";
echo "<pre>";

$path = base_path('service-account-file.json');
echo "[CHECK] Service Account Path: " . $path . "\n";

if (!file_exists($path)) {
    echo "[FAIL] File NOT found!\n";
    die();
}

$credentials = json_decode(file_get_contents($path), true);
if (!$credentials) {
    echo "[FAIL] Invalid JSON in credentials file.\n";
    die();
}

echo "[INFO] Project ID: " . ($credentials['project_id'] ?? 'MISSING') . "\n";
echo "[INFO] Client Email: " . ($credentials['client_email'] ?? 'MISSING') . "\n";

// 1. GENERATE JWT
echo "\n--- STEP 1: JWT GENERATION ---\n";
try {
    $now = time();
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $payload = [
        'iss' => $credentials['client_email'],
        'sub' => $credentials['client_email'],
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
    ];

    function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    $base64Header = base64UrlEncode(json_encode($header));
    $base64Payload = base64UrlEncode(json_encode($payload));
    $signatureInput = $base64Header . '.' . $base64Payload;
    
    $privateKey = $credentials['private_key'];
    echo "[INFO] Private Key Length: " . strlen($privateKey) . "\n";

    if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        echo "[FAIL] OpenSSL Sign Failed: " . openssl_error_string() . "\n";
        die();
    }
    
    $base64Signature = base64UrlEncode($signature);
    $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    
    echo "[PASS] JWT Generated: " . substr($jwt, 0, 20) . "...\n";

} catch (Exception $e) {
    echo "[FAIL] Exception during JWT: " . $e->getMessage() . "\n";
    die();
}

// 2. GET ACCESS TOKEN
echo "\n--- STEP 2: GET ACCESS TOKEN ---\n";
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "[INFO] HTTP " . $httpCode . "\n";
    echo "[INFO] Response: " . $response . "\n";

    $tokenData = json_decode($response, true);
    if (!isset($tokenData['access_token'])) {
        echo "[FAIL] No access_token in response!\n";
        die();
    }

    $accessToken = $tokenData['access_token'];
    echo "[PASS] Access Token Obtained.\n";

} catch (Exception $e) {
    echo "[FAIL] Exception during Access Token: " . $e->getMessage() . "\n";
    die();
}

// 3. SEND TO DEVICE
echo "\n--- STEP 3: SEND TO FCM ---\n";
// Get User
$user = \App\Models\User::whereNotNull('fcm_token')->orderBy('updated_at', 'desc')->first();
if (!$user) {
    echo "[FAIL] No User found for testing.\n";
    die();
}

$token = $user->routeNotificationForFcm() ?? $user->fcm_token;
echo "[INFO] Target Token: " . substr($token, 0, 20) . "...\n";

$url = "https://fcm.googleapis.com/v1/projects/" . $credentials['project_id'] . "/messages:send";
echo "[INFO] URL: $url\n";

$message = [
    'message' => [
        'token' => $token,
        'notification' => [
            'title' => 'Deep Debug Test',
            'body' => 'Checking detailed response.',
        ],
        'data' => [
            'type' => 'debug',
            'time' => (string)time(),
        ],
        'android' => [
            'priority' => 'high',
            'notification' => [
                'channel_id' => 'high_importance_channel',
                'sound' => 'default',
            ],
        ],
    ],
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Enable VERBOSE for cURL?
// curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "[INFO] HTTP " . $httpCode . "\n";
echo "[INFO] Response Body:\n" . $response . "\n";

if ($httpCode == 200) {
    echo "\n[SUCCESS] Notification Sent Successfully!\n";
} else {
    echo "\n[FAIL] FCM API Error.\n";
}

echo "</pre>";
