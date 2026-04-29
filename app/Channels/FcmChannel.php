<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        Log::info('FcmChannel: send() called for user ID: ' . ($notifiable->id ?? 'unknown'));

        if (!method_exists($notification, 'toFcm')) {
            Log::warning('FcmChannel: Notification does not have toFcm method');
            return;
        }

        $token = $notifiable->routeNotificationForFcm();
        if (!$token) {
            $token = $notifiable->fcm_token;
        }

        if (!$token) {
            Log::warning('FcmChannel: No FCM token found for user ' . $notifiable->id);
            return;
        }

        Log::info('FcmChannel: Token found for user ' . $notifiable->id . ': ' . substr($token, 0, 15) . '...');

        try {
            $message = $notification->toFcm($notifiable);
            if (!$message || !is_array($message)) {
                Log::warning('FcmChannel: toFcm returned invalid message');
                return;
            }

            Log::info('FcmChannel: Sending message', ['title' => $message['title'] ?? 'N/A', 'body' => $message['body'] ?? 'N/A']);

            $result = $this->firebaseService->sendToDevice(
                $token,
                $message['title'] ?? 'Notification',
                $message['body'] ?? '',
                $message['data'] ?? []
            );

            Log::info('FcmChannel: sendToDevice result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        } catch (\Exception $e) {
            Log::error('FcmChannel: Exception during send: ' . $e->getMessage());
        }
    }
}
