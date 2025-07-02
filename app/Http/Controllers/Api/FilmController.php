<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFilmRequest;
use App\Models\Films;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    //index
    public function index() {
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
    public function store(CreateFilmRequest $request){
        $validated = $request->validated();
        $file = $request->file('poster');

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('posters', $filename, 'public');

        $validated['poster'] = $path;

        $film = Films::create($validated);

        return response()->json($film);
    }

    //get one
    public function show(Films $film)
    {
        return response()->json($film);
    }

    //update
    public function update(Films  $film, CreateFilmRequest $request) {
        $validated = $request->validated();

        if ($film->poster && Storage::disk('public')->exists($film->poster)) {
            Storage::disk('public')->delete($film->poster);
        }

        $file = $request->file('poster');

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('posters', $filename, 'public');

        $validated['poster'] = $path;

        $film->update($validated);

        return response()->json($film);
    }

    //destroy
    public function destroy(Films $film) {
        if ($film->poster && Storage::disk('public/posters')->exists($film->poster))
            Storage::disk('public/posters')->delete($film->poster);

        $film->delete();

        return response()->json(null, 204);
    }
}
