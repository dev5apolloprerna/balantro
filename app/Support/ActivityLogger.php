<?php
// app/Support/ActivityLogger.php
namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class ActivityLogger
{
    /**
     * Log an action.
     *
     * ActivityLogger::log('assigned_managers', $user, ['old'=>$old, 'new'=>$new]);
     */
    public static function log(
        string $action,
        object|array|null $subject = null,
        array $changes = null
    ): ActivityLog {
        $authUser = Auth::user();

        // Normalize subject to type/id if it's a model
        $subjectType = null;
        $subjectId   = null;

        if (is_object($subject) && method_exists($subject, 'getKey')) {
            $subjectType = get_class($subject);
            $subjectId   = (string) $subject->getKey();
        } elseif (is_array($subject)) {
            // allow passing ['type'=>..., 'id'=>...]
            $subjectType = Arr::get($subject, 'type');
            $subjectId   = (string) Arr::get($subject, 'id');
        }

        $req = request();

        return ActivityLog::create([
            'user_id'    => $authUser?->id,
            'user_name'  => $authUser?->name,
            'action'     => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'changes'    => $changes,
            'route'      => $req?->route()?->getName() ?? $req?->path(),
            'method'     => $req?->method(),
            'ip'         => $req?->ip(),
            'user_agent' => substr((string) $req?->userAgent(), 0, 1000),
        ]);
    }
}
