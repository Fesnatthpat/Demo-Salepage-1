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
        // 🛠️ แก้ไข: ตรวจสอบสิทธิ์ Admin (ยอมให้ผ่านได้ถ้าเป็นการบันทึก failed_login)
        if (! Auth::guard('admin')->check() && $action !== 'failed_login') {
            return;
        }

        $changes = null;

        // 2. ใช้ฟังก์ชัน filterSensitiveData กรองข้อมูลก่อนเก็บลงตัวแปร $changes
        if ($action === 'created') {
            $changes = ['new' => $this->filterSensitiveData($model->toArray())];
        } elseif ($action === 'updated' || $action === 'updated_settings') {
            $changes = [
                'original' => $this->filterSensitiveData($originalData ?? []),
                'new' => $this->filterSensitiveData($newData ?? []),
            ];
        } elseif ($action === 'deleted') {
            $changes = ['original' => $this->filterSensitiveData($model->toArray())];
        } elseif ($action === 'failed_login') {
            // 🛠️ แก้ไข: สำหรับ Login ล้มเหลว ให้เก็บข้อมูล (Username, IP) ลงใน array 'new'
            $changes = ['new' => $this->filterSensitiveData($newData ?? [])];
        } else {
            // สำหรับ Action อื่นๆ ที่ไม่ใช่มาตรฐาน
            $changes = [
                'original' => $this->filterSensitiveData($originalData ?? []),
                'new' => $this->filterSensitiveData($newData ?? []),
            ];
        }

        ActivityLog::create([
            // 🛠️ แก้ไข: ถ้าล็อกอินไม่ผ่าน ID จะเป็น null
            'admin_id' => Auth::guard('admin')->id(),
            // 🛠️ แก้ไข: ป้องกัน Database Error กรณีส่ง Model ว่างๆ มา (ไม่มี ID) ให้บังคับใส่เป็น 0 แทน
            'loggable_id' => $model->getKey() ?? 0, 
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