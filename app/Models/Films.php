<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use function PHPUnit\Framework\isNull;

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
        'isVisible',
        'country',
        'producer',
        'actors',
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
    public function toArray()
    {
        $array = parent::toArray();

        $firstFile = $this->files()->first();

        if ($this->type === 'film' && $firstFile !== null && $firstFile->link !== null) {
            $array['url'] = $firstFile->link;
        }
        $array['poster'] = env('APP_URL') . "/storage/{$this->poster}";
        $array['categories'] = $this->category()
            ->get(['categories.id', 'categories.slug', 'categories.name'])
            ->toArray();

        return $array;
    }
}
