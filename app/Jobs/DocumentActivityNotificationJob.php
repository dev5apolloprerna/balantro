<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\User;
use App\Services\DocumentNotificationService;

class DocumentActivityNotificationJob extends ApplicationJob
{
    public $queue = 'notifications';

    protected $documentId;
    protected $actionById;
    protected $event;

    public function __construct($documentId, $actionById, $event)
    {
        $this->documentId = $documentId;
        $this->actionById = $actionById;
        $this->event = $event;
    }

    public function handle()
    {
        $document = Document::find($this->documentId);
        $actionBy = User::find($this->actionById);
        
        if (!$document || !$actionBy) {
            return;
        }

        (new DocumentNotificationService($document, $actionBy))
            ->sendActivityNotification($this->event);
    }
}