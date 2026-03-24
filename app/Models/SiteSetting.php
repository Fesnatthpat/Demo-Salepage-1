<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    // ระบุชื่อตารางให้ชัดเจน
    protected $table = 'site_settings';

    // กำหนด Primary Key เป็น 'key' เนื่องจากไม่มีคอลัมน์ 'id'
    protected $primaryKey = 'key';

    // ระบุว่า Primary Key ไม่ได้เป็นตัวเลขที่เพิ่มขึ้นเอง (auto-increment)
    public $incrementing = false;

    // ระบุว่า Primary Key เป็น String
    protected $keyType = 'string';

    // อนุญาตให้บันทึกค่าได้ใน 2 คอลัมน์นี้
    protected $fillable = ['key', 'value'];

    // ★★★ แก้ไข Error: Unknown column 'updated_at' ★★★
    // สั่งให้ Model ไม่ต้องพยายามบันทึกเวลา created_at / updated_at
    public $timestamps = false;

    // Helper: ฟังก์ชันสำหรับดึงค่า (Get)
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) return $default;

        $value = $setting->value;
        
        // ตรวจสอบว่าเป็น JSON หรือไม่ (เช่น ["..."] หรือ {"..."} หรือ "...")
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $value;
    }

    // Helper: ฟังก์ชันสำหรับบันทึกค่า (Set)
    public static function set($key, $value)
    {
        // ถ้าค่าที่ส่งมาเป็น Array หรือ Object ให้แปลงเป็น JSON String ก่อนบันทึก
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        // ใช้ updateOrCreate เพื่อป้องกัน Error เรื่อง missing ID
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * ดึงค่าการตั้งค่าทั้งหมดมาเป็น Array ในรูปแบบ ['key' => 'value']
     * ช่วยลดปัญหา N+1 Query และจัดการ JSON decoding ให้อัตโนมัติ
     */
    public static function getAllSettings(): array
    {
        return self::all()->mapWithKeys(function ($setting) {
            $value = $setting->value;
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
            return [$setting->key => $value];
        })->toArray();
    }
}
