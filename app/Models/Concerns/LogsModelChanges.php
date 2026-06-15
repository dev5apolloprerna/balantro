<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LogsModelChanges
{
    public static function bootLogsModelChanges(): void
    {
        static::created(function ($model) {
            $model->writeActivity('created');
        });

        static::updated(function ($model) {
            $model->writeActivity('updated', $model->getActivityChanges());
        });

        static::deleted(function ($model) {
            $model->writeActivity('deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->writeActivity('restored');
            });
        }
    }

    protected function getActivityChanges(): array
    {
        $ignore = property_exists($this, 'activityLogIgnore') ? (array) $this->activityLogIgnore : [];
        $new = collect($this->getDirty())->except($ignore)->all();
        $old = [];
        foreach ($new as $k => $v) {
            $old[$k] = $this->getOriginal($k);
        }
        return ['old' => $old, 'new' => $new];
    }

    protected function writeActivity(string $action, array $changes = []): void
    {
        // don't recurse on the log model itself
        if ($this instanceof \App\Models\ActivityLog) return;

        try {
            $user = Auth::user();

            DB::table('activity_logs')->insert([
                'user_id'      => $user->id ?? null,
                'user_name'    => $user->name ?? null,
                'action'       => $action,
                'subject_type' => get_class($this),
                'subject_id'   => (string) $this->getKey(),
                'changes'      => $changes ? json_encode($changes, JSON_UNESCAPED_UNICODE) : null,
                'route'        => request()->route()?->getName() ?? request()->path(),
                'method'       => request()->method(),
                'ip'           => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } catch (\Throwable $e) {
            logger()->warning('Failed to write activity log', ['error' => $e->getMessage()]);
        }
    }

    /** For ad-hoc logs: $user->logActivity('assigned_managers', ['ids' => [1,2]]) */
    public function logActivity(string $action, array $payload = []): void
    {
        $this->writeActivity($action, ['payload' => $payload]);
    }
}
