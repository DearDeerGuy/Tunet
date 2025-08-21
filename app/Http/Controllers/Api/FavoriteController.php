<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Films;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function show(Favorite $favorite)
    {
        return response()->json($favorite);
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
            return response()->json(['message' => 'Фільм вже в обраних'], 409);

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
            return response()->json(['message' => 'Фільм не знайдено в обраних'], 404);

        return response()->json(['message' => 'Видалено з обраних.']);
    }
}
