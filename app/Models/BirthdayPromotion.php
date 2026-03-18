<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'image_path',
        'link_url',
        'promotion_id',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
