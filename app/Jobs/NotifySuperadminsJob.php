<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;

class NotifySuperadminsJob extends ApplicationJob
{
    public $queue = 'notifications';

    protected $clientId;

    public function __construct($clientId)
    {
        $this->clientId = $clientId;
    }

    public function handle()
    {
        $client = User::find($this->clientId);
        if (!$client) {
            return;
        }

        NotificationService::sendSuperadminOnClientCreate($client);
    }
}