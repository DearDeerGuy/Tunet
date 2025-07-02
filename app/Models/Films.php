<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 */
class Films extends Model
{
    protected $fillable = [
        'poster',
        'title',
        'description',
        'type',
        'release_date',
        'isVisible'
    ];
    public function files(): HasMany
    {
        return $this->hasMany(Files::class);
    }
    public function category(): BelongsToMany
    {
        return $this->belongsToMany(Categories::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Reviews::class);
    }
}
