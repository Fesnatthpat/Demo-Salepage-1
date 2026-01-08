<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    // ★ 1. ระบุ Primary Key ให้ชัดเจน
    protected $primaryKey = 'ord_id'; 
    
    // ★ 2. ปิด timestamps ถ้าในตารางไม่ได้ใช้ created_at/updated_at มาตรฐาน (แต่ถ้าใช้ก็ลบบรรทัดนี้ทิ้งได้)
    // public $timestamps = false; 

    // ★ 3. อนุญาตให้บันทึกข้อมูลลงฟิลด์เหล่านี้
    protected $fillable = [
        'ord_code',
        'user_id',
        'total_price',
        'shipping_cost',
        'total_discount',
        'net_amount',
        'ord_date',
        'status_id',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'slip_path', // <--- เพิ่มตัวนี้
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'ord_date' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'ord_id', 'ord_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedOrdDateAttribute()
    {
        return Carbon::parse($this->ord_date)->format('d/m/Y H:i');
    }
}