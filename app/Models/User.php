<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'avatar',
        'admin_lvl',
        'isBanned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage' . $this->avatar) : null;
    }
}
