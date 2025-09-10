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
        if ($this->type === 'film') {
            return $this->files->first()->link ?? null;
        }

        $video = [];
        foreach ($this->files as $f) {
            $video[$f->season_number][$f->episode_number] = $this->getUrlByName($f->link);
        }

        ksort($video); 
        foreach ($video as &$episodes) {
            ksort($episodes); 
        }

        return $video;
    }
    private function getUrlByName($name)
    {
        if (!$name)
            return null;
        return env('APP_URL', 'SET_ENV_PLS') . '/api/stream/' . $name;
    }
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
