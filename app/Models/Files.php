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
        'films_id',
        'season_number',
        'episode_number',
        'link',
    ];
    public function films(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Films::class);
    }
}
