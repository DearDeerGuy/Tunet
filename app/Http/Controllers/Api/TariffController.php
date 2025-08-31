<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTariffRequest;
use App\Http\Requests\UpdateTariffRequest;
use App\Http\Util\ImageSaverUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tariff;
use App\Models\User;


class TariffController extends Controller
{
    public function index()
    {
        return response()->json(Tariff::all());
    }
    public function show($id)
    {
        return response()->json(Tariff::find($id));
    }
    public function store(StoreTariffRequest $request)
    {
        $validatedData = $request->validated();

        $path = null;
        if (isset($validatedData['image']))
            $path = ImageSaverUtil::save('tariffs', $request->file('image'));

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
    public function update(UpdateTariffRequest $request, Tariff $tariff)
    {
        $validatedData = $request->validated();

        $path = null;
        if (isset($validatedData['image']))
            $path = ImageSaverUtil::update($tariff->image, 'tariffs', $request->file('image'));

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
            if ($tariff->image)
                ImageSaverUtil::delete($tariff->image);

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
        ]);

        $user = Auth::user();
        $tariffId = $validatedData['tariff_id'] ?? null;



        if ($tariffId != null) {
            $tariff = Tariff::find($tariffId);

            $user->tariff_end_date = now()->addMonths($tariff->duration_months);

        } else {
            $user->tariff_end_date = null;
        }

        $user->tariff_id = $tariffId;
        $user->save();

        return response()->json(['message' => 'Тариф успішно призначено користувачу', 'user' => $user]);
    }
}
