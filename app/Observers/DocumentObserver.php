<?php

namespace App\Observers;

use App\Models\Document;
use App\Support\ActivityLogger;

class DocumentObserver
{
    public function created(Document $doc): void
    {
        ActivityLogger::log(
            'documents.created',
            $doc,
            [
                'by'      => auth()->id(),
                'changes' => [
                    'status' => [null, $doc->status],
                ],
            ]
        );
    }

    public function updated(Document $doc): void
    {
        // Only care about a subset of fields
        $watched = ['status', 'rejection_reason', 'file'];

        // Laravel keeps originals; getChanges() has the new values.
        $changed = array_intersect_key($doc->getChanges(), array_flip($watched));

        if (empty($changed)) {
            return;
        }

        $changes = [];
        foreach ($changed as $key => $newVal) {
            $oldVal = $doc->getOriginal($key);
            // normalize to strings to avoid serialization surprises in logs
            $changes[$key] = [
                $oldVal === null ? null : (string) $oldVal,
                $newVal === null ? null : (string) $newVal,
            ];
        }

        ActivityLogger::log(
            'documents.updated',
            $doc,                     // ✅ pass the model instance (object)
            ['by' => auth()->id(), 'changes' => $changes]
        );
    }
}
