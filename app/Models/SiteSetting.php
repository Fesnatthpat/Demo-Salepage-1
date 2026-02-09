<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    // ระบุชื่อตารางให้ชัดเจน
    protected $table = 'site_settings';

    // อนุญาตให้บันทึกค่าได้ใน 2 คอลัมน์นี้
    protected $fillable = ['key', 'value'];

    // ★★★ แก้ไข Error: Unknown column 'updated_at' ★★★
    // สั่งให้ Model ไม่ต้องพยายามบันทึกเวลา created_at / updated_at
    public $timestamps = false;

    // Helper: ฟังก์ชันสำหรับดึงค่า (Get)
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    // Helper: ฟังก์ชันสำหรับบันทึกค่า (Set)
    public static function set($key, $value)
    {
        // ถ้าค่าที่ส่งมาเป็น Array หรือ Object ให้แปลงเป็น JSON String ก่อนบันทึก
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
