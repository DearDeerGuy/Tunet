<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'film_id',
    ];

    // Связь с пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь с фильмом/сериалом
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'film_id');
    }
}
