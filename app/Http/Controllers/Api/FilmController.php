<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Util\ImageSaverUtil;
use App\Models\Films;

class FilmController extends Controller
{
    //index
    public function index()
    {
        $search = request('search');
        $perPage = request('per_page', 10);
        $type = request('type');
        $categoriesParam = request('categories');

        $query = Films::query();

        if ($type)
            $query->where('type', $type);

        if ($search)
            $query->where('title', 'like', "%$search%");

        if ($categoriesParam) {
            $categories = explode(',', $categoriesParam);
            $categories = array_filter($categories);

            if (!empty($categories)) {
                foreach ($categories as $categorySlug) {
                    $query->whereHas('category', function ($q) use ($categorySlug) {
                        $q->where('slug', $categorySlug);
                    });
                }
            }
        }

        return response()->json(
            $query->paginate($perPage)
        );
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

    //get one
    public function show(Films $film)
    {
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
