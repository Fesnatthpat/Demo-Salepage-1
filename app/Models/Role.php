<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role_key',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * รายการเมนูทั้งหมดในระบบ
     */
    public static function getAvailablePermissions()
    {
        return [
            'dashboard'          => 'แดชบอร์ด',
            'orders'             => 'จัดการออเดอร์',
            'products'           => 'จัดการสินค้า',
            'customers'          => 'จัดการลูกค้า',
            'promotions'         => 'จัดการโปรโมชั่น',
            'birthday_promotions' => 'จัดการโปรโมชั่นวันเกิด',
            'content_management' => 'จัดการเนื้อหา (หน้าแรก/FAQ/ติดใจ)',
            'system_management'  => 'จัดการระบบ (แอดมิน/Log/ขนส่ง)',
            'popups'             => 'จัดการ Popup',
            'settings'           => 'ตั้งค่าเว็บไซต์',
        ];
    }

    /**
     * Get the admins for the role.
     */
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
