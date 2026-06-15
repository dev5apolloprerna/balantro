<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentActivityController extends Controller
{
    public function index(Document $document, Request $request)
    {
        // Basic authz (optional)
        // abort_unless(auth()->user()->role === User::ROLES['supervisor'], 403);

        // Pull PaperTrail-style rows
        $versions = DB::table('versions') // has whodunnit,item_id,item_type,event,object_changes,created_at
            ->where('item_type', Document::class)
            ->where('item_id', $document->id)
            ->orderByDesc('created_at')
            ->get();

        // Pull comments (e.g., Query Raised text)
        $comments = DocumentComment::where('document_id', $document->id)
            ->orderByDesc('created_at')
            ->get();

        // Build timeline rows
        $rows = [];

        foreach ($versions as $v) {
            $rows[] = [
                'time'    => $v->created_at,
                'event'   => ucfirst((string)$v->event), // create/update
                'who'     => $this->displayUser($v->whodunnit),
                'changes' => $this->formatChanges($v->object_changes),
            ];
        }

        foreach ($comments as $c) {
            $rows[] = [
                'time'    => $c->created_at,
                'event'   => $this->labelCommentEvent($c->comment_type),
                'who'     => $this->displayUser($c->commented_by_id),
                'changes' => $c->description ?: '—',
            ];
        }

        // Ensure initial upload is visible even if no version row
        $rows[] = [
            'time'    => $document->created_at,
            'event'   => 'Create',
            'who'     => 'Client',
            'changes' => 'Document was Uploaded by Client.',
        ];

        // Sort desc by time
        usort($rows, fn($a, $b) => strcmp((string)$b['time'], (string)$a['time']));

        return view('client.documents.activities', compact('document', 'rows'));
    }

    private function displayUser($userIdOrNull): string
    {
        if (!$userIdOrNull) return 'System';
        $u = User::find($userIdOrNull);
        return $u?->name ? "{$u->name}" : "User #{$userIdOrNull}";
    }

    private function formatChanges($objectChangesJson): string
    {
        if (!$objectChangesJson) return 'No changes recorded.';
        $changes = json_decode($objectChangesJson, true);
        if (!is_array($changes)) return 'No changes recorded.';

        // Highlight status changes specially
        if (isset($changes['status']) && is_array($changes['status']) && count($changes['status']) === 2) {
            [$from, $to] = $changes['status'];
            return 'Status has been changed from '
                . "\"{$from}\" to \"{$to}\"";
        }

        // Generic fallback: list changed keys
        $parts = [];
        foreach ($changes as $k => $pair) {
            if (is_array($pair) && count($pair) === 2) {
                $parts[] = ucfirst(str_replace('_', ' ', $k)) . " changed";
            }
        }
        return $parts ? implode('; ', $parts) : 'No changes recorded.';
    }

    private function labelCommentEvent(int $type): string
    {
        // Adjust these codes to your app’s convention
        return match ($type) {
            2       => 'Query Raised',
            3       => 'Query Resolved',
            default => 'Comment',
        };
    }
}
