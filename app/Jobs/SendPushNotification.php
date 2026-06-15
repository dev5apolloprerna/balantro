<?php

namespace App\Jobs;

use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $deviceToken;
    protected $title;
    protected $body;
    protected $data;

    public function __construct($deviceToken, $title, $body, $data = [])
    {
        $this->deviceToken = $deviceToken;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function handle(FirebaseNotificationService $notificationService)
    {
        $notificationService->sendToDevice(
            $this->deviceToken,
            $this->title,
            $this->body,
            $this->data
        );
    }
}

// Usage in controller
SendPushNotification::dispatch($deviceToken, 'Title', 'Body message');
