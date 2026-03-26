<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'user_id',
        'path',
        'visit_date'
    ];

    /**
     * ความสัมพันธ์กับ User (ผู้ใช้หน้าเว็บ)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope สำหรับดึงข้อมูลเฉพาะวันนี้
     */
    public function scopeToday($query)
    {
        return $query->where('visit_date', now()->toDateString());
    }
}
