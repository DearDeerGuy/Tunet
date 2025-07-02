<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Reviews extends Model
{
    protected $fillable = [
        'film_id',
        'user_id',
        'comment',
        'mark',
    ];
    public function film(): BelongsTo
    {
        return $this->belongsTo(Films::class);
    }
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
