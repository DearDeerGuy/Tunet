<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
/**
 * @mixin Builder
 */
class Files extends Model
{
    protected $fillable = [
        'film_id',
        'season_number',
        'episode_number',
        'link',
    ];
    public function film(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Films::class);
    }
}
