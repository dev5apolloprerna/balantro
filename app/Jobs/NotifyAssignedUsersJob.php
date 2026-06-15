<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;

class NotifyAssignedUsersJob extends ApplicationJob
{
    protected $assignerId;
    protected $recipient;
    protected $managerIds;
    protected $supervisorIds;
    protected $deoIds;

    public function __construct($assignerId, $recipient, $managerIds = [], $supervisorIds = [], $deoIds = [])
    {
        $this->assignerId = $assignerId;
        $this->recipient = $recipient;
        $this->managerIds = (array)$managerIds;
        $this->supervisorIds = (array)$supervisorIds;
        $this->deoIds = (array)$deoIds;
    }

    public function handle()
    {
        $assigner = User::find($this->assignerId);
        if (!$assigner || !$this->recipient) {
            return;
        }

        $this->notifyUsers($this->managerIds, $assigner);
        $this->notifyUsers($this->supervisorIds, $assigner);
        $this->notifyUsers($this->deoIds, $assigner);
    }

    protected function notifyUsers($userIds, $assigner)
    {
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                NotificationService::sendAssignmentNotification($assigner, $user, $this->recipient);
            }
        }
    }
}