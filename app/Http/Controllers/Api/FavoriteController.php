<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Получить все избранные фильмы текущего пользователя
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $user = Auth::user();
        $favorites = $user->favorites()
                      ->with('film')
                      ->paginate($perPage);

        return response()->json($favorites);
    }

    // Добавить фильм в избранное
    public function store(Request $request)
    {
        $request->validate([
            'film_id' => ['required', 'integer', 'exists:films,id'],
        ]);
        $film_id = $request->input('film_id');
        $user = Auth::user();

        // Проверка на дублирование
        $alreadyExists = Favorite::where('user_id', $user->id)
                                 ->where('film_id', $film_id)
                                 ->exists();
        if ($alreadyExists) 
            return response()->json(['message' => 'Фильм уже в избранном.'], 409);

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'film_id' => $film_id,
        ]);

        return response()->json($favorite, 201);
    }

    // Удалить фильм из избранного по его ID
    public function destroy(int $film_id)
    {
        $user = Auth::user();

        $deleted = Favorite::where('user_id', $user->id)
                           ->where('film_id', $film_id)
                           ->delete();
        if (!$deleted)
            return response()->json(['message' => 'Фильм не найден в избранном.'], 404);
        
        return response()->json(['message' => 'Удалено из избранного.']);
    }
}
