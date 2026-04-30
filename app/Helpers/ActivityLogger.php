<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Log a custom activity.
     *
     * @param string $action
     * @param string $description
     * @param Model|null $subject
     * @param int|null $branchIdOverride Use this to override the user's branch
     * @return ActivityLog
     */
    public static function log(string $action, string $description, ?Model $subject = null, ?int $branchIdOverride = null)
    {
        $user = auth()->user();
        
        $branchId = $branchIdOverride ?? ($user ? $user->branch_id : null);

        // For Superadmin, if no branch override is provided, we might want to log it as global (null)
        // or specifically tied to the action they are performing.

        $logData = [
            'user_id' => $user ? $user->id : null,
            'branch_id' => $branchId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($subject) {
            $logData['subject_type'] = get_class($subject);
            $logData['subject_id'] = $subject->id;
        }

        return ActivityLog::create($logData);
    }
}
