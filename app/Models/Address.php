<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    // [แก้ไขตรงนี้] เปลี่ยนจาก 'addresses' เป็น 'delivery_addresses' ให้ตรงกับ Database ของคุณ
    protected $table = 'delivery_addresses';

    // ตรวจสอบชื่อฟิลด์ใน $fillable ให้ตรงกับภาพที่คุณส่งมา (ดูจากภาพแล้วตรงกันครับ)
    protected $fillable = [
        'user_id',
        'fullname',
        'phone',
        'address_line1',
        'address_line2',
        'province_id',
        'amphure_id',
        'district_id',
        'zipcode',
        'note',
    ];

    // --- ความสัมพันธ์ (Relationships) ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function amphure()
    {
        return $this->belongsTo(Amphure::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
