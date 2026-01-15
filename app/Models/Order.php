<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    // ชื่อตารางในฐานข้อมูล
    protected $table = 'orders';

    // ★ 1. แก้ไข Primary Key ให้เป็น 'id' ตามฐานข้อมูลใหม่
    // (ถ้าใส่เป็น ord_id จะเกิด error "Unknown column 'ord_id' in 'where clause'")
    protected $primaryKey = 'id'; 
    
    // เปิดใช้งาน timestamps (created_at, updated_at)
    public $timestamps = true; 

    // ★ 2. อนุญาตให้บันทึกข้อมูลลงฟิลด์เหล่านี้
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
        'slip_path',      // ที่เก็บรูปสลิป
        'transfer_date',  // (เผื่อมี) วันที่โอน
        'transfer_amount' // (เผื่อมี) ยอดที่โอนจริง
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'ord_date' => 'datetime',
        'transfer_date' => 'datetime',
    ];

    // ความสัมพันธ์กับตารางรายละเอียดออเดอร์ (One To Many)
    public function details()
    {
        // Foreign Key ในตารางลูกคือ 'ord_id', Local Key ในตารางแม่คือ 'id'
        return $this->hasMany(OrderDetail::class, 'ord_id', 'id');
    }

    // ความสัมพันธ์กับตาราง User (Many To One)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Helper: จัดรูปแบบวันที่
    public function getFormattedOrdDateAttribute()
    {
        return $this->ord_date ? Carbon::parse($this->ord_date)->format('d/m/Y H:i') : '-';
    }
}