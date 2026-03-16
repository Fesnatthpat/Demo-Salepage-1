<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // ต้องมีบรรทัดนี้ ระบบถึงจะยอมให้บันทึกชื่อรูปลงฐานข้อมูลได้
    protected $fillable = ['key', 'value'];

    // ...
}
