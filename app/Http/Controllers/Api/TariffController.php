<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use App\Models\Tariff;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class TariffController extends Controller
{
    public function index()
    {
        Tariff::all();
        return response()->json(Tariff::all());
    }
    public function show($id)
    {
        Tariff::find($id);
        return response()->json(Tariff::find($id));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'duration_months' => ['required', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Handle image upload if provided
        $path = null;
        if (isset($validatedData['image'])) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tariffs', $filename, 'public');
        }

        $tariff = Tariff::create(
            [
                'name' => $validatedData['name'],
                'price' => $validatedData['price'],
                'description' => $validatedData['description'] ?? null,
                'duration_months' => $validatedData['duration_months'],
                'image' => $path ?? null,
            ]
        );

        return response()->json($tariff, 201);
    }
    public function update(Request $request, Tariff $tariff)
    {
        $validatedData = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'duration_months' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);
        // Handle image upload if provided
        $path = null;
        if (isset($validatedData['image'])) {
            if ($tariff->image && Storage::disk('public')->exists($tariff->image))
                Storage::disk('public')->delete($tariff->image);
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tariffs', $filename, 'public');
        }

        $tariff->update(
            [
                'name' => $validatedData['name'] ?? $tariff->name,
                'price' => $validatedData['price'] ?? $tariff->price,
                'description' => $validatedData['description'] ?? $tariff->description,
                'duration_months' => $validatedData['duration_months'] ?? $tariff->duration_months,
                'image' => $path ?? $tariff->image,
            ]
        );

        return response()->json($tariff);

    }
    public function destroy($id)
    {
        $tariff = Tariff::find($id);
        if ($tariff) {
            if ($tariff->image && Storage::disk('public')->exists($tariff->image)) {
                Storage::disk('public')->delete($tariff->image);
            }
            $tariff->delete();
            return response()->json(['message' => 'Тариф успішно видалено']);
        } else {
            return response()->json(['message' => 'Тариф не знайдено'], 404);
        }
    }
    public function setTariffToUser(Request $request)
    {
        $validatedData = $request->validate([
            'tariff_id' => ['nullable', 'integer', 'exists:tariffs,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $userId = $validatedData['user_id'];
        $tariffId = $validatedData['tariff_id'] ?? null;

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Користувача не знайдено'], 404);
        }

        if ($tariffId != null) {
            $tariff = Tariff::find($tariffId);
            if (!$tariff) {
                return response()->json(['message' => 'Тариф не знайдено'], 404);
            }
            $user->tariff_start_date = now();
        } else {
            $user->tariff_start_date = null;
        }

        $user->tariff_id = $tariffId;
        $user->save();

        return response()->json(['message' => 'Тариф успішно призначено користувачу', 'user' => $user]);
    }
}
