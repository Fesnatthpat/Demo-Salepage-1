<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // เรียกใช้งาน Traits ที่จำเป็นสำหรับ User Model
    use HasApiTokens, HasFactory, Notifiable; 

    /**
     * กำหนดฟิลด์ที่อนุญาตให้บันทึกข้อมูลแบบ Mass Assignment ได้
     * (รวมถึง line_id และ avatar สำหรับระบบ LINE Login)
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'line_id', 
        'avatar',  
        'password',
        'phone',
        'gender',
        'date_of_birth',
        'age',
    ];

    /**
     * กำหนดฟิลด์ที่ต้องการซ่อน (ไม่ให้แสดงเมื่อดึงข้อมูลเป็น Array หรือ JSON)
     * เพื่อความปลอดภัยของข้อมูล
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * กำหนดการแปลงชนิดข้อมูล (Casting)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'password' => 'hashed', // แนะนำให้ใส่เพิ่มไว้ เพื่อให้ Laravel เข้ารหัส/ถอดรหัสผ่านให้อัตโนมัติ (สำหรับ Laravel เวอร์ชั่นใหม่)
    ];

    /**
     * ความสัมพันธ์ (Relationship): User มีหลาย Orders (1-to-Many)
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
}