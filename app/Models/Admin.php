<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes, HasFactory; // Add HasFactory here

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'username', 'password', 'role', 'admin_code',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
