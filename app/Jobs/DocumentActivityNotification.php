<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DocumentActivityNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $documentId;
    public string $action;
    public int $actorId;

    // Accept int|string for $actorId but store as int
    public function __construct(int $documentId, string $action, int|string $actorId)
    {
        $this->documentId = $documentId;
        $this->action     = $action;
        $this->actorId    = (int) $actorId; // normalize here
    }

    public function handle(): void
    {
        $doc   = Document::find($this->documentId);
        $actor = User::find($this->actorId);

        if (!$doc) {
            Log::warning('DocActivity: document not found', ['id' => $this->documentId]);
            return;
        }

        Log::info('Document activity', [
            'doc_id'   => $doc->id,
            'file'     => $doc->file,
            'action'   => $this->action,
            'actor_id' => $this->actorId,
            'time'     => now()->toDateTimeString(),
        ]);
    }
}
