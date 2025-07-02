<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFilmRequest;
use App\Models\Films;

class FilmController extends Controller
{
    //index
    public function index() {
        $search = request('search');
        $perPage = request('per_page', 10);
        $type = request('type');

        $query = Films::query();

        if ($type) {
            $query->where('type', $type);
        }
        if ($search) {
            $query->where('title', 'like', "%$search%");
        }
        return response()->json(
            $query->paginate($perPage)
        );
    }

    //store
    public function store(CreateFilmRequest $request){
        $validated = $request->validated();
        Films::create($validated);
        return response()->json(200);
    }

    //get one
    public function show(Films $film)
    {
        return response()->json($film);
    }

    //update
    public function update(Films  $film, CreateFilmRequest $request) {
        $validated = $request->validated();
        $film->update($validated);
        return response()->json($film);
    }

    //destroy
    public function destroy(Films  $film) {
        $film->delete();
        return response()->json(null, 204);
    }
}
