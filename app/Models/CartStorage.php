<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartStorage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cart_data'];

    // ★★★ [สำคัญมาก] บรรทัดนี้ต้องมี เพื่อแปลงข้อมูลตะกร้าเป็น JSON อัตโนมัติ ★★★
    protected $casts = [
        'cart_data' => 'array',
    ];
}
