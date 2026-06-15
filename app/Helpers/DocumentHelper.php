<?php

namespace App\Helpers;

use Carbon\Carbon;

class DocumentHelper
{
    public static function documentStatusClasses($status)
    {
        switch ($status) {
            case 'uploaded':
                return 'bg-neutral-100 dark:bg-neutral-600/50 text-neutral-500 dark:!text-neutral-400';
            case 'accepted':
                return 'bg-primary-100 dark:bg-primary-600/25 text-primary-600 dark:!text-primary-400';
            case 'rejected':
                return 'bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:!text-danger-400';
            case 'data_entry_in_progress':
                return 'bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:!text-warning-400';
            case 'data_entry_completed':
                return 'bg-purple-100 dark:bg-purple-600/25 text-purple-600 dark:!text-purple-400';
            case 'query_raised':
                return 'bg-pink-100 dark:bg-pink-600/25 text-pink-600 dark:!text-pink-400';
            case 'query_resolved':
                return 'bg-teal-100 dark:bg-teal-600/25 text-teal-600 dark:!text-teal-400';
            case 'approved':
                return 'bg-success-100 dark:bg-success-600/25 text-success-600 dark:!text-success-400';
            default:
                return 'bg-gray-100 dark:bg-gray-600/25 text-gray-600 dark:!text-gray-400';
        }
    }

    public static function formattedActivityChanges($activity, $document, $user)
    {
        if ($activity->event == 'create') {
            $createdTime = Carbon::parse($activity->created_at)->format('d M, Y at h:i A');
            $userName = $user ? ucfirst($user->name) : 'System';
            return "Document was Uploaded by <strong>{$userName}</strong> on {$createdTime}.";
        }

        return self::formatActivityChangesDefault($activity);
    }

    public static function formatActivityChangesDefault($activity)
    {
        if (empty($activity->object_changes)) {
            return 'No changes recorded.';
        }

        $changes = json_decode($activity->object_changes, true);
        $html = [];

        foreach ($changes as $attr => $values) {
            if ($attr == 'created_at' || $attr == 'updated_at') {
                continue;
            }

            $fromValue = $values[0] ?? '-';
            $toValue = $values[1] ?? '-';

            if ($attr == 'status') {
                $fromValue = Document::STATUSES[$values[0]] ?? $values[0];
                $toValue = Document::STATUSES[$values[1]] ?? $values[1];
            }

            $html[] = "<p><strong>" . ucfirst($attr) . "</strong> has been changed from " .
                     "<strong>\"{$fromValue}\"</strong> to " .
                     "<strong>\"{$toValue}\"</strong> on " .
                     Carbon::parse($activity->created_at)->format('d M, Y at h:i A') . ".</p>";
        }

        return implode('', $html);
    }

    public static function clientDocumentStatus($document)
    {
        $baseClasses = 'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';

        switch ($document->status) {
            case 'uploaded':
                return '<span class="' . $baseClasses . ' bg-neutral-100 dark:bg-neutral-600/50 text-neutral-500 dark:!text-neutral-400">Uploaded</span>';
            case 'rejected':
                return '<span class="' . $baseClasses . ' bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:!text-danger-400">Rejected</span>';
            case 'approved':
                return '<span class="' . $baseClasses . ' bg-success-100 dark:bg-success-600/25 text-success-600 dark:!text-success-400">Completed</span>';
            case 'accepted':
            case 'data_entry_in_progress':
            case 'data_entry_completed':
            case 'query_raised':
            case 'query_resolved':
                return '<span class="' . $baseClasses . ' bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:!text-warning-400">In-progress</span>';
            default:
                return '<span class="' . $baseClasses . ' bg-gray-100 dark:bg-gray-600/25 text-gray-600 dark:!text-gray-400">Unknown</span>';
        }
    }

    public static function clientStatusFilterOptions()
    {
        return [
            ['Uploaded', 'uploaded'],
            ['In-progress', 'in_progress'],
            ['Rejected', 'rejected'],
            ['Completed', 'approved']
        ];
    }
}