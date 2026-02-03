<?php

namespace App\Http\Controllers\Admin\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * 1. กำหนดรายชื่อฟิลด์ที่ห้ามบันทึกลง Log เด็ดขาด
     * เพื่อป้องกันข้อมูลหลุด (Data Leakage)
     */
    protected array $hiddenInLogs = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'api_token',
        'card_number',
        'cvv',
        'deleted_at',
    ];

    /**
     * Log an activity for a given model.
     *
     * @param  string  $action  ('created', 'updated', 'deleted')
     * @param  array|null  $originalData  For 'updated' action.
     * @param  array|null  $newData  For 'updated' action.
     */
    protected function logActivity(Model $model, string $action, ?array $originalData = null, ?array $newData = null): void
    {
        // ตรวจสอบสิทธิ์ Admin ก่อนทำงาน
        if (! Auth::guard('admin')->check()) {
            return;
        }

        $changes = null;

        // 2. ใช้ฟังก์ชัน filterSensitiveData กรองข้อมูลก่อนเก็บลงตัวแปร $changes
        if ($action === 'created') {
            $changes = ['new' => $this->filterSensitiveData($model->toArray())];
        } elseif ($action === 'updated') {
            $changes = [
                'original' => $this->filterSensitiveData($originalData ?? []),
                'new' => $this->filterSensitiveData($newData ?? []),
            ];
        } elseif ($action === 'deleted') {
            $changes = ['original' => $this->filterSensitiveData($model->toArray())];
        }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'loggable_id' => $model->getKey(),
            'loggable_type' => get_class($model),
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(), // เก็บข้อมูล Browser เพิ่มเติม
        ]);
    }

    /**
     * 3. ฟังก์ชันสำหรับตัดข้อมูลที่เป็นความลับออกจาก Array
     */
    private function filterSensitiveData(array $data): array
    {
        return array_diff_key($data, array_flip($this->hiddenInLogs));
    }
}
