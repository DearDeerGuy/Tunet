<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GetFilmsRequest;
use App\Models\Films;

class FavoriteController extends Controller
{
    // Получить все избранные фильмы текущего пользователя
    public function index(GetFilmsRequest $request)
    {
        $validated = $request->validated();

        $search = $validated['search'] ?? null;
        $perPage = $validated['per_page'] ?? 10;
        $type = $validated['type'] ?? null;
        $categoriesParam = $validated['categories'] ?? null;
        $of = $validated['of'] ?? null;
        $ot = $validated['ot'] ?? null;

        $user = Auth::user();
        
        $query = Films::query()
            ->whereHas('favorites', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

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

        $query->withAvg('reviews as rating', 'mark');

        if ($of && $ot) {
            $query->orderBy($of, $ot);
        }
        $films = $query->paginate($perPage);

        return response()->json($films);

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

        // Проверка на дублирование                                                   Аркадий - Танк
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
