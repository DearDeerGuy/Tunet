<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
    protected $hidden = ['files'];
    protected $appends = ['url'];


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
        return $this->hasMany(Reviews::class, 'film_id');
    }
    protected function rating(): Attribute
    {
        return Attribute::get(
            fn($value) => $value !== null ? round($value, 1) : 0
        );
    }
    public function getUrlAttribute()
    {
        $result = [];
        if ($this->type === 'film') {
            $file = $this->files()->first();

            if ($file)
                $result =
                    [
                        'id' => $file->id,
                        'link' => $file->link
                    ];
            else
                $result = [];
        } else
            // Для сериала возвращаем 'season_number' и 'episode_number'
            $result = $this->files
                ->sortBy([
                    ['season_number', 'asc'],
                    ['episode_number', 'asc'],
                ])
                ->values()
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'season_number' => $file->season_number,
                        'episode_number' => $file->episode_number,
                        'link' => $file->link,
                    ];
                });
        return $result;
    }
    /*private function getUrlByName($name)
    {
        if (!$name)
            return null;
        return env('APP_URL', 'SET_ENV_PLS') . '/api/stream/' . $name;
    } */
    public function toArray()
    {
        $array = parent::toArray();
        $array['poster'] = env('APP_URL') . "/storage/{$this->poster}";
        $array['categories'] = $this->category()
            ->get(['categories.id', 'categories.slug', 'categories.name'])
            ->toArray();


        return $array;
    }
}
