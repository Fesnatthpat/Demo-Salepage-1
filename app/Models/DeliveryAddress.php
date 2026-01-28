<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'delivery_addresses';

    protected $fillable = [
        'user_id', // [สำคัญ] เพิ่มบรรทัดนี้ เพื่อให้บันทึกว่าเป็นของใคร
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

    // --- เพิ่ม Relation ผูกกับ User ---
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- Relation เดิมสำหรับดึงชื่อจังหวัด/อำเภอ/ตำบล ---
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function amphure()
    {
        return $this->belongsTo(Amphure::class, 'amphure_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
