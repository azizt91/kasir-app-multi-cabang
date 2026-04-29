<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FirebaseNotificationService
{
    private $projectId;
    private $credentialsPath;
    private $credentials;

    public function __construct()
    {
        // Read project_id from config (which reads from .env)
        $this->projectId = config('services.firebase.project_id');
        $credentialsConfig = config('services.firebase.credentials', 'service-account-file.json');
        
        // Handle relative or absolute path
        if (file_exists($credentialsConfig)) {
            $this->credentialsPath = $credentialsConfig;
        } else {
            $this->credentialsPath = base_path($credentialsConfig);
        }
        
        if (!file_exists($this->credentialsPath)) {
             Log::error('Firebase Credentials not found at: ' . $this->credentialsPath);
             return;
        }
        
        $this->loadCredentials();
        
        // Fallback: if project_id not in .env, try from credentials file
        if (!$this->projectId && $this->credentials) {
            $this->projectId = $this->credentials['project_id'] ?? null;
        }
        
        Log::info('Firebase: Initialized with project_id: ' . ($this->projectId ?? 'NULL'));
    }

    /**
     * Load Firebase credentials from JSON file
     */
    private function loadCredentials(): void
    {
        if (file_exists($this->credentialsPath)) {
            $this->credentials = json_decode(file_get_contents($this->credentialsPath), true);
        }
    }

    /**
     * Generate JWT token for Firebase authentication
     */
    private function generateJwt(): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $now = time();
        $payload = [
            'iss' => $this->credentials['client_email'],
            'sub' => $this->credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
        ];

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));

        $signatureInput = $base64Header . '.' . $base64Payload;
        
        // Use openssl_pkey_get_private() to properly parse the PEM key
        $privateKey = openssl_pkey_get_private($this->credentials['private_key']);
        openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        
        $base64Signature = $this->base64UrlEncode($signature);

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Get OAuth2 access token for Firebase Cloud Messaging v1 API
     */
    private function getAccessToken(): ?string
    {
        // Check cache first
        $cachedToken = Cache::get('firebase_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            if (!$this->credentials) {
                Log::error('Firebase: Credentials not loaded');
                return null;
            }

            $jwt = $this->generateJwt();

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;
                
                // Cache token for slightly less than expiry time
                Cache::put('firebase_access_token', $accessToken, $expiresIn - 60);
                
                Log::info('Firebase: Access token obtained successfully');
                return $accessToken;
            }

            Log::error('Firebase: Failed to get access token', $response->json());
            return null;
        } catch (\Exception $e) {
            Log::error('Firebase: Error getting access token - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('Firebase: Cannot send - no access token');
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'high_importance_channel',
                        'sound' => 'default',
                    ],
                ],
            ],
        ];

        try {
            Log::info('Firebase: Sending to URL: ' . $url);
            
            $response = Http::withToken($accessToken)
                ->post($url, $message);

            if ($response->successful()) {
                Log::info('Firebase: Notification sent successfully', ['token' => substr($fcmToken, 0, 20) . '...']);
                return true;
            }

            Log::error('Firebase: Failed to send notification', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Firebase: Error sending notification - ' . $e->getMessage());
            return false;
        }
    }
}
