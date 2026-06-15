<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Firebase\PushNotificationService;

class FirebasePushNotificationJob extends ApplicationJob
{
    public $queue = 'notifications';

    protected $userId;
    protected $title;
    protected $body;
    protected $data;

    public function __construct($userId, $title, $body, $data = [])
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function handle()
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        $deviceTokens = $user->userDevices()->pluck('device_token')->toArray();
        if (empty($deviceTokens)) {
            return;
        }

        $service = new PushNotificationService();

        foreach ($deviceTokens as $token) {
            $service->sendNotification($token, $this->title, $this->body, $this->data);
        }
    }
}