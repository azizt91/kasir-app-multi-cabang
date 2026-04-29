<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FcmDebugController extends Controller
{
    public function diagnose(Request $request)
    {
        $results = [];

        // 1. Check .env values
        $results['env'] = [
            'FIREBASE_PROJECT_ID' => config('services.firebase.project_id') ?: '❌ NOT SET',
            'FIREBASE_CREDENTIALS' => config('services.firebase.credentials') ?: '❌ NOT SET',
            'QUEUE_CONNECTION' => config('queue.default'),
        ];

        // 2. Check service-account-file.json
        $credPath = config('services.firebase.credentials', 'service-account-file.json');
        if (!file_exists($credPath)) {
            $credPath = base_path($credPath);
        }
        $results['credentials_file'] = [
            'path' => $credPath,
            'exists' => file_exists($credPath) ? '✅ YES' : '❌ NO',
        ];

        if (file_exists($credPath)) {
            $creds = json_decode(file_get_contents($credPath), true);
            $results['credentials_file']['project_id'] = $creds['project_id'] ?? '❌ MISSING';
            $results['credentials_file']['client_email'] = $creds['client_email'] ?? '❌ MISSING';
            $results['credentials_file']['has_private_key'] = !empty($creds['private_key']) ? '✅ YES' : '❌ NO';
        }

        // 3. Check FirebaseNotificationService class exists
        $results['service_class'] = class_exists(\App\Services\FirebaseNotificationService::class) 
            ? '✅ Class exists' 
            : '❌ Class NOT FOUND - run composer dump-autoload';

        // 4. Check users with FCM tokens
        $usersWithToken = User::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->select('id', 'name', 'fcm_token')
            ->get();
        
        $results['users_with_fcm_token'] = $usersWithToken->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'token_preview' => substr($u->fcm_token, 0, 25) . '...',
                'token_length' => strlen($u->fcm_token),
            ];
        });

        // 5. Check FcmChannel class exists
        $results['fcm_channel'] = class_exists(\App\Channels\FcmChannel::class) 
            ? '✅ FcmChannel exists' 
            : '❌ FcmChannel NOT FOUND';

        // 6. Check OrderCreated notification
        $results['order_notification'] = [
            'class_exists' => class_exists(\App\Notifications\OrderCreated::class) ? '✅ YES' : '❌ NO',
        ];

        // 7. Try to get access token (tests JWT + Google OAuth)
        if (class_exists(\App\Services\FirebaseNotificationService::class)) {
            try {
                $service = new \App\Services\FirebaseNotificationService();
                
                // Use reflection to test getAccessToken
                $reflection = new \ReflectionClass($service);
                $method = $reflection->getMethod('getAccessToken');
                $method->setAccessible(true);
                $token = $method->invoke($service);
                
                $results['access_token'] = $token 
                    ? '✅ Got access token: ' . substr($token, 0, 20) . '...'
                    : '❌ Failed to get access token - check laravel.log';
            } catch (\Exception $e) {
                $results['access_token'] = '❌ Error: ' . $e->getMessage();
            }
        }

        // 8. Send test notification if requested
        if ($request->has('send_test') && $usersWithToken->count() > 0) {
            try {
                $service = new \App\Services\FirebaseNotificationService();
                $testUser = $usersWithToken->first();
                
                $sent = $service->sendToDevice(
                    $testUser->fcm_token,
                    '🔔 Test Notifikasi',
                    'Jika Anda melihat ini, FCM berfungsi!',
                    ['type' => 'test']
                );
                
                $results['test_send'] = $sent 
                    ? '✅ Test notification sent successfully to ' . $testUser->name
                    : '❌ Failed to send test notification - check laravel.log';
            } catch (\Exception $e) {
                $results['test_send'] = '❌ Error: ' . $e->getMessage();
            }
        } else {
            $results['test_send'] = 'Add ?send_test=1 to URL to send a test notification';
        }

        // 9. Check recent laravel.log for FCM entries
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            $fcmLines = array_filter($lines, function ($line) {
                return stripos($line, 'firebase') !== false 
                    || stripos($line, 'fcm') !== false
                    || stripos($line, 'FcmChannel') !== false;
            });
            $results['recent_fcm_logs'] = array_values(array_slice($fcmLines, -10));
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
