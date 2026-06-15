<?php

namespace App\Services\Firebase;

use Google\Client;
use Google\Service\FirebaseCloudMessaging;
use Google\Service\FirebaseCloudMessaging\Message;
use Google\Service\FirebaseCloudMessaging\Notification;
use Google\Service\FirebaseCloudMessaging\WebpushConfig;
use Google\Service\FirebaseCloudMessaging\WebpushNotification;
use Google\Service\FirebaseCloudMessaging\SendMessageRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class PushNotificationService
{
    protected $service;
    protected $projectId;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(config('services.firebase.service_account'));
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');

        $this->service = new FirebaseCloudMessaging($client);
        $this->projectId = config('services.firebase.project_id');
    }

    public function sendNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        // Stringify all data values
        $stringifiedData = array_map('strval', $data);

        // Get the icon URL dynamically
        $icon = URL::asset('images/small-logo.svg');
        
        // Get the base URL from config
        $baseUrl = config('app.url');
        
        // Use dynamic click_action if provided, otherwise fallback to root
        $path = $data['click_action'] ?? '/';
        $clickAction = $baseUrl . $path;

        // Create the notification message
        $message = new Message([
            'token' => $deviceToken,
            'notification' => new Notification([
                'title' => $title,
                'body' => $body,
                'image' => $icon
            ]),
            'data' => $stringifiedData,
            'webpush' => new WebpushConfig([
                'notification' => new WebpushNotification([
                    'title' => $title,
                    'body' => $body,
                    'icon' => $icon,
                    'click_action' => $clickAction
                ])
            ])
        ]);

        $request = new SendMessageRequest([
            'message' => $message
        ]);

        try {
            $response = $this->service->projects_messages->send(
                "projects/{$this->projectId}",
                $request
            );
            
            Log::info("Successfully sent message: {$response->getName()}");
            return $response;
        } catch (\Exception $e) {
            Log::error("Error sending message: {$e->getMessage()}");
            return null;
        }
    }
}