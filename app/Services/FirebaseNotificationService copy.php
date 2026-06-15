<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService_old
{
    private $messaging;

    public function __construct()
    {
        try {
            $serviceAccountPath = storage_path('app/firebase/service-account.json');

            $factory = (new Factory)
                ->withServiceAccount($serviceAccountPath);

            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send notification to specific FCM tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No tokens provided'];
        }

        // Remove duplicates and empty tokens
        $tokens = array_filter(array_unique($tokens));

        $results = [
            'success_count' => 0,
            'failure_count' => 0,
            'errors' => []
        ];

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData(array_merge([
                        'type' => 'chat_message',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ], $data));

                $this->messaging->send($message);
                $results['success_count']++;
            } catch (MessagingException $e) {
                $results['failure_count']++;
                $results['errors'][$token] = $e->getMessage();

                // If token is invalid, mark it as inactive
                if (
                    str_contains($e->getMessage(), 'not registered') ||
                    str_contains($e->getMessage(), 'invalid')
                ) {
                    $this->deactivateToken($token);
                }

                Log::warning("FCM send failed for token {$token}: " . $e->getMessage());
            } catch (\Exception $e) {
                $results['failure_count']++;
                $results['errors'][$token] = $e->getMessage();
                Log::error("FCM error for token {$token}: " . $e->getMessage());
            }
        }

        Log::info('FCM Send Results', $results);
        return $results;
    }

    /**
     * Send notification to a specific user by their user_id
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = [])
    {
        $tokens = $this->getUserDeviceTokens($userId);

        if (empty($tokens)) {
            Log::info("No active tokens found for user {$userId}");
            return ['success' => false, 'message' => 'No active tokens found'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = [])
    {
        $allTokens = [];

        foreach ($userIds as $userId) {
            $tokens = $this->getUserDeviceTokens($userId);
            $allTokens = array_merge($allTokens, $tokens);
        }

        return $this->sendToTokens($allTokens, $title, $body, $data);
    }

    /**
     * Send to topic (useful for broadcasting)
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData(array_merge([
                    'type' => 'chat_message',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ], $data));

            $this->messaging->send($message);

            Log::info("Message sent to topic: {$topic}");
            return ['success' => true];
        } catch (MessagingException $e) {
            Log::error("Failed to send to topic {$topic}: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get active device tokens for a user
     */
    private function getUserDeviceTokens(int $userId): array
    {
        return \DB::table('user_devices')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->pluck('device_token')
            ->toArray();
    }

    /**
     * Deactivate invalid token
     */
    private function deactivateToken(string $token): void
    {
        try {
            \DB::table('user_devices')
                ->where('device_token', $token)
                ->update([
                    'is_active' => 0,
                    'updated_at' => now()
                ]);

            Log::info("Deactivated invalid token: " . substr($token, 0, 20) . '...');
        } catch (\Exception $e) {
            Log::error("Failed to deactivate token: " . $e->getMessage());
        }
    }

    /**
     * Subscribe user to topic
     */
    public function subscribeToTopic($tokens, string $topic)
    {
        try {
            if (is_string($tokens)) {
                $tokens = [$tokens];
            }

            $this->messaging->subscribeToTopic($topic, $tokens);
            return ['success' => true];
        } catch (MessagingException $e) {
            Log::error("Topic subscription failed: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
