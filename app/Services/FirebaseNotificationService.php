<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FirebaseNotificationService
{
    # private $messaging;
    protected $messaging;
    protected $projectId;

    // public function __construct()
    // {
    //     try {
    //         $serviceAccountPath = storage_path('app/firebase/service-account.json');

    //         $factory = (new Factory)
    //             ->withServiceAccount($serviceAccountPath);

    //         $this->messaging = $factory->createMessaging();
    //     } catch (\Exception $e) {
    //         Log::error('Firebase initialization failed: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }
    public function __construct()
    {
        try {
           // $credentialsPath = $this->resolveCredentialsPath();
            $credentialsPath = storage_path('app/firebase/service-account.json');
            
            Log::info('Firebase Service Initialization:', [
                'credentials_path' => $credentialsPath,
                'file_exists' => file_exists($credentialsPath),
                'project_id' => config('firebase.project_id')
            ]);
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
            }
            
            $factory = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri('https://' . config('firebase.project_id') . '.firebaseio.com');

            $this->messaging = $factory->createMessaging();
            $this->projectId = config('firebase.project_id');
            
            Log::info('Firebase service initialized successfully');

        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

     private function resolveCredentialsPath()
    {
        $path = config('firebase.credentials.file');
        
        // Handle both array and string configurations
        if (is_array($path)) {
            $path = $path['file'] ?? $path[0] ?? storage_path('app/firebase/service-account.json');
        }
        
        // Convert Windows paths if needed
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = str_replace('/', '\\', $path);
        }
        
        return $path;
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
                    
                $msg = $this->messaging->send($message);
                
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
    // public function sendToUsers(array $userIds, string $title, string $body, array $data = [])
    // {
    //     $allTokens = [];

    //     foreach ($userIds as $userId) {
    //         $tokens = $this->getUserDeviceTokens($userId);
    //         $allTokens = array_merge($allTokens, $tokens);
    //     }

    //     return $this->sendToTokens($allTokens, $title, $body, $data);
    // }

    // public function sendToUsers(array $userIds, string $title, string $body, array $data = [])
    // {
    //     $allTokens = [];

    //     foreach ($userIds as $userId) {
    //         $tokens = $this->getUserDeviceTokens($userId);
    //         $allTokens = array_merge($allTokens, $tokens);
    //     }

    //     if (empty($allTokens)) {
    //         Log::info("No active tokens found for users: " . implode(', ', $userIds));
    //         return ['success' => false, 'message' => 'No active tokens found for any user'];
    //     }

    //     return $this->sendToTokens($allTokens, $title, $body, $data);
    // }

    public function sendToUsers(array $userIds, string $title, string $body, array $data = [])
    {
        Log::info("sendToUsers called", [
            'user_ids' => $userIds,
            'user_count' => count($userIds),
            'title' => $title
        ]);

        $allTokens = [];
        $usersWithTokens = [];

        foreach ($userIds as $userId) {
            $tokens = $this->getUserDeviceTokens($userId);
            if (!empty($tokens)) {
                $allTokens = array_merge($allTokens, $tokens);
                $usersWithTokens[] = $userId;
            }
        }

        Log::info("Collected tokens", [
            'total_tokens' => count($allTokens),
            'users_with_tokens' => $usersWithTokens
        ]);

        if (empty($allTokens)) {
            Log::warning("No active tokens found for any user in: " . implode(', ', $userIds));
            return [
                'success' => false, 
                'message' => 'No active tokens found for any user',
                'user_ids' => $userIds
            ];
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
            //->where('is_active', 1)
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
            return [
                'success' => false, 
                'error' => $e->getMessage()
            ];
        }
    }

    public function testNotification(Request $request)
    {
        try {
            $userId = $request->input('user_id'); // Test with a specific user ID
            
            $results = $this->firebaseService->sendToUsers(
                [$userId], 
                'Test Notification', 
                'This is a test message from the API',
                ['test' => 'true', 'timestamp' => now()->toISOString()]
            );
            
            return response()->json([
                'ok' => true,
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
