<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// ...
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable; // เพิ่ม HasApiTokens ที่นี่

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'line_id', // เพิ่ม line_id ที่นี่
        'avatar',  // เพิ่ม avatar ที่นี่
        'password',
        'phone',
        'gender',
        'date_of_birth',
        'age',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
    // ...
}
