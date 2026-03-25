<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'condition_type',
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'usage_limit',
        'used_count',
        'is_discount_code',
        'is_free_shipping',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'float',
        'min_order_value' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_discount_code' => 'boolean',
        'is_free_shipping' => 'boolean',
    ];

    /**
     * ความสัมพันธ์กับกฎเกณฑ์ (เงื่อนไขการซื้อ)
     */
    public function rules()
    {
        return $this->hasMany(PromotionRule::class);
    }

    /**
     * ความสัมพันธ์กับการกระทำ (ของแถม)
     */
    public function actions()
    {
        return $this->hasMany(PromotionAction::class);
    }

    /**
     * ความสัมพันธ์กับประวัติการใช้งาน
     */
    public function usageLogs()
    {
        return $this->hasMany(PromotionUsageLog::class);
    }

    public function birthdayPromotion()
    {
        return $this->hasOne(BirthdayPromotion::class);
    }

    protected function isBirthday(): Attribute
    {
        return Attribute::get(fn () => $this->birthdayPromotion()->exists());
    }

    /**
     * จำนวนครั้งที่ใช้ (จาก Log จริง)
     */
    protected function usageCount(): Attribute
    {
        return Attribute::get(fn () => $this->usageLogs()->count());
    }

    /**
     * จำนวนคนที่ใช้ (นับ User แบบไม่ซ้ำ)
     */
    protected function uniqueUsersCount(): Attribute
    {
        return Attribute::get(fn () => $this->usageLogs()->distinct('user_id')->count('user_id'));
    }

    /**
     * ตรวจสอบว่าโปรโมชั่นอยู่ในช่วงเวลาที่กำหนดหรือไม่
     */
    public function isWithinDateRange(): bool
    {
        $now = now();
        $startMatch = is_null($this->start_date) || $this->start_date <= $now;
        $endMatch = is_null($this->end_date) || $this->end_date >= $now;

        return $startMatch && $endMatch;
    }

    /**
     * ดึงข้อความสถานะเวลา (เช่น "เหลืออีก 2 วัน", "สิ้นสุดแล้ว")
     */
    protected function timeRemaining(): Attribute
    {
        return Attribute::get(function () {
            $now = now();

            if ($this->start_date && $this->start_date > $now) {
                return 'เริ่มในอีก ' . $now->diffForHumans($this->start_date, true);
            }

            if ($this->end_date) {
                if ($this->end_date < $now) {
                    return 'สิ้นสุดแล้ว';
                }
                return 'เหลืออีก ' . $now->diffForHumans($this->end_date, true);
            }

            return 'ใช้งานได้เรื่อยๆ';
        });
    }

    /**
     * ตรวจสอบว่าใกล้หมดเวลาหรือยัง (น้อยกว่า 24 ชม.)
     */
    /**
     * ดึงข้อความสถานะเวลาแบบละเอียด (เช่น "เหลืออีก 2 ชม. 15 นาที")
     */
    protected function timeRemainingDetailed(): Attribute
    {
        return Attribute::get(function () {
            $now = now();
            if (!$this->end_date || $this->end_date < $now) return 'สิ้นสุดแล้ว';

            $diff = $this->end_date->diff($now);

            if ($diff->days > 0) {
                return 'เหลืออีก ' . $diff->days . ' วัน ' . $diff->h . ' ชม.';
            }

            if ($diff->h > 0) {
                return 'เหลืออีก ' . $diff->h . ' ชม. ' . $diff->i . ' นาที';
            }

            return 'เหลือเพียง ' . $diff->i . ' นาที ' . $diff->s . ' วินาที';
        });
    }
}
