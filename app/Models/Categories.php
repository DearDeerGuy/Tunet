<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
/**
 * @mixin Builder
 */
class Categories extends Model
{
    protected $fillable = [
        'slug',
        'name',
    ];
    public function film(): BelongsToMany
    {
        return $this->belongsToMany(Films::class);
    }
}
