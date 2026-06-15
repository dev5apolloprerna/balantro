<?php

namespace App\Services;

use App\Jobs\FirebasePushNotificationJob;
use App\Models\Document;
use App\Models\User;

class DocumentNotificationService
{
    protected $document;
    protected $actionBy;
    protected $client;

    public function __construct(Document $document, User $actionBy)
    {
        $this->document = $document;
        $this->actionBy = $actionBy;
        $this->client = $document->user;
    }

    public function sendActivityNotification(string $event)
    {
        switch ($event) {
            case 'create':
            case 'update':
            case 'delete':
                $this->notifyDeosAndSupervisors(
                    __("clients.notification.documents.headline", ['action' => ucfirst($event)]),
                    __("clients.notification.documents.text", [
                        'user_name' => $this->actionBy->name,
                        'event' => $event
                    ])
                );
                break;
            case 'status_change':
                $this->notifyBasedOnStatus($this->document->status);
                break;
        }
    }

    protected function notifyDeosAndSupervisors(string $title, string $body)
    {
        $deoIds = $this->client->dataEntryOperators()->pluck('id')->toArray();
        $supervisorIds = User::whereHas('supervisors', function($query) use ($deoIds) {
            $query->whereIn('data_entry_operators.id', $deoIds);
        })->pluck('id')->toArray();

        $userIds = array_unique(array_merge($deoIds, $supervisorIds));

        foreach ($userIds as $userId) {
            $this->notify($userId, $title, $body);
        }
    }

    protected function notifyBasedOnStatus(string $status)
    {
        $title = __("clients.notification.documents.status_headline", ['action' => ucfirst($status)]);
        $body = __("clients.notification.documents.status_text", [
            'user_name' => $this->actionBy->name,
            'action' => ucfirst($status)
        ]);

        switch ($status) {
            case 'rejected':
                $this->notify($this->client->id, $title, $body);
                break;
            case 'query_raised':
            case 'approved':
                foreach ($this->client->dataEntryOperators as $user) {
                    $this->notify($user->id, $title, $body);
                }
                break;
            case 'accepted':
            case 'data_entry_in_progress':
            case 'data_entry_completed':
            case 'query_resolved':
                $supervisors = $this->client->dataEntryOperators()
                    ->with('supervisors')
                    ->get()
                    ->pluck('supervisors')
                    ->flatten()
                    ->unique('id');

                foreach ($supervisors as $user) {
                    $this->notify($user->id, $title, $body);
                }
                break;
            case 'uploaded':
                $this->notifyDeosAndSupervisors($title, $body);
                break;
        }
    }

    protected function notify(int $userId, string $title, string $body)
    {
        $user = User::find($userId);
        if (!$user) return;

        $rolePathMap = [
            'client' => 'documents',
            'data_entry_operator' => 'data_entry_operators/documents',
            'supervisor' => 'supervisors/documents',
            'manager' => 'managers/documents',
            'super_admin' => 'super_admins/documents'
        ];

        $clickActionUrl = $rolePathMap[$user->role] ?? 'users/documents';
        $clickActionUrl .= "?status={$this->document->status}";

        FirebasePushNotificationJob::dispatch(
            $userId,
            $title,
            $body,
            [
                'document_id' => $this->document->id,
                'click_action' => $clickActionUrl
            ]
        );
    }
}