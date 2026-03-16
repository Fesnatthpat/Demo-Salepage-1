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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
