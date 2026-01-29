<?php

namespace App\Http\Controllers\Admin\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity for a given model.
     *
     * @param Model $model
     * @param string $action ('created', 'updated', 'deleted')
     * @param array|null $originalData For 'updated' action, the original values of changed attributes.
     * @param array|null $newData For 'updated' action, the new values of changed attributes.
     */
    protected function logActivity(Model $model, string $action, ?array $originalData = null, ?array $newData = null): void
    {
        if (!Auth::guard('admin')->check()) {
            return;
        }

        $changes = null;
        if ($action === 'created') {
            $changes = ['new' => $model->toArray()];
        } elseif ($action === 'updated') {
            $changes = ['original' => $originalData, 'new' => $newData];
        } elseif ($action === 'deleted') {
            $changes = ['original' => $model->toArray()];
        }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'loggable_id' => $model->getKey(),
            'loggable_type' => get_class($model),
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
