<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        if($this->avatar!=null)
            $array['avatar'] = env('APP_URL') . "/storage/{$this->avatar}";
        return $array;
    }
    protected $model = \App\Models\User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'date_of_birth' => $this->faker->date(),
            'avatar' => null,
            'admin_lvl' => 0,
            'isBanned' => false,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            // Установить уровни админа вручную
            static $adminLevels = [1, 2, 3];
            if ($user->id <= 3) {
                $user->admin_lvl = $adminLevels[$user->id - 1];
                $user->save();
            }
        });
    }
}
