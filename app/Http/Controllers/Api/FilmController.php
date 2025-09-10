<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\GetFilmsRequest;
use App\Http\Util\ImageSaverUtil;
use App\Models\Films;


class FilmController extends Controller
{
    private function getUrlByName($name)
    {
        if (!$name)
            return null;
        return env('APP_URL', 'SET_ENV_PLS') . '/api/stream/' . $name;
    }
    //index
    public function index(GetFilmsRequest $request)
    {
        $validated = $request->validated();

        $search = $validated['search'] ?? null;
        $perPage = $validated['per_page'] ?? 10;
        $type = $validated['type'] ?? null;
        $categoriesParam = $validated['categories'] ?? null;
        $of = $validated['of'] ?? null;
        $ot = $validated['ot'] ?? null;

        $query = Films::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where('title', 'like', "%$search%");
        }

        if ($categoriesParam) {
            $categories = array_filter(explode(',', $categoriesParam));
            foreach ($categories as $categorySlug) {
                $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
            }
        }

        $query->with('files')->withAvg('reviews as rating', 'mark');

        if ($of && $ot) {
            $query->orderBy($of, $ot);
        }
        $films = $query->paginate($perPage);

        $films->getCollection()->transform(function ($film) {
            if ($film->type === 'film') {
                $film->url = $film->files->first()->link ?? null;

            } else {
                $video = [];
                foreach ($film->files as $f) {
                    $video[$f->season_number][$f->episode_number] = $this->getUrlByName($f->link);
                }
                ksort($video);
                foreach ($video as &$episodes) {
                    ksort($episodes);
                }
                $film->url = $video;
            }
            return $film;
        });
        return response()->json($films);

    }
    public function show(Films $film)
    {
        $film->load('files')->loadAvg('reviews as rating', 'mark');

        if ($film->type === 'film') {
            $film->url = $film->files->first()->link ?? null;
        } else {
            $video = [];
            foreach ($film->files as $f) {
                $video[$f->season_number][$f->episode_number] = $this->getUrlByName($f->link);
            }

            ksort($video);
            foreach ($video as &$episodes) {
                ksort($episodes);
            }

            $film->url = $video;
        }
        return response()->json($film);
    }
    //store
    public function store(CreateFilmRequest $request)
    {
        $validated = $request->validated();

        $validated['poster'] = ImageSaverUtil::save('posters', $request->file('poster'));
        $film = Films::create($validated);
        $validated['category'] = explode(',', $validated['category']);
        foreach ($validated['category'] as $category) {
            $film->category()->attach($category);
        }

        return response()->json($film);
    }

    //update
    public function update(Films $film, CreateFilmRequest $request)
    {
        $validated = $request->validated();

        // Если есть файл постера, удаляем старый и сохраняем новый
        if ($request->hasFile('poster'))
            $validated['poster'] = ImageSaverUtil::update($film->poster, 'posters', $request->file('poster'));

        $film->update($validated);

        return response()->json($film);
    }

    //destroy
    public function destroy(Films $film)
    {
        if ($film->poster)
            ImageSaverUtil::delete($film->poster);

        $film->delete();

        return response()->json(null, 204);
    }
}
