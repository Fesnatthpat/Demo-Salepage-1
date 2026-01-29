<?php

namespace App\Http\Controllers\Admin\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Log an activity for a given model.
     *
     * @param Model $model
     * @param string $action
     * @param array|null $changes
     */
    protected function logActivity(Model $model, string $action, ?array $changes = null): void
    {
        Log::info('[LogsActivity] Trait method entered.');

        if (!Auth::guard('admin')->check()) {
            Log::warning('[LogsActivity] Auth guard "admin" check failed. User is not authenticated.');
            return;
        }
        Log::info('[LogsActivity] Auth guard "admin" check passed.');

        $data = [
            'admin_id' => Auth::guard('admin')->id(),
            'loggable_id' => $model->getKey(),
            'loggable_type' => get_class($model),
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ];

        Log::info('[LogsActivity] Data prepared for logging:', $data);

        try {
            ActivityLog::create($data);
            Log::info('[LogsActivity] ActivityLog::create a Succeeded.');
        } catch (\Exception $e) {
            Log::error('[LogsActivity] FAILED to create ActivityLog. Exception: ' . $e->getMessage());
        }
    }
}
