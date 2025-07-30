<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Models\Files;
use App\Models\Films;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request) {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
        ]);

        $filmId = $validated['film_id'];
        $film = Film::with('files')->find($filmId);
        
        if ($film->type === 'film') {
            $file = $film->files->first();

            if ($file)
                $result = ['link' => $file->link];
            else
                $result = [];
        }
        else
            // Для сериала возвращаем 'season_number' и 'episode_number'
            $result = $film->files
                ->sortBy([
                    ['season_number', 'asc'],
                    ['episode_number', 'asc'],
                    ])
                ->values()
                ->map(function ($file) {
                    return [
                        'season_number' => $file->season_number,
                        'episode_number' => $file->episode_number,
                    ];
                });

        return response()->json($result);
    }
    public function getSerialInfo(Films $film) {
        if($film->type!="serial")
            return response()->json(['message'=>'Is not a serial']);
        
        $seasons = $film->files->groupBy('season_number');

        return response()->json($seasons);
    }

    public function store(FileRequest $request) {
        $validated = $request->validated();
        
        // Проверяем, есть ли уже файлы у фильма
        $existingFilesCount = Files::where('films_id', $validated['film_id'])->count();
        if ($existingFilesCount > 0) 
            return response()->json(['message' => 'Файлы для этого фильма уже загружены.'], 409);

        $file = $request->file('file');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('films', $filename, 'public');

        $fileModel = Files::create([
            'films_id' => $validated['film_id'],
            'link' => $path,
        ]);

        return response()->json($fileModel, 201);
    }
    // Один запрос - одна запись файла
    public function storeSerial(SerialFileRequest $request) {
        $validated = $request->validated();

        $file = $request->file('file');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = "{$validated['film_id']}/{$validated['season_number']}/{$filename}";

        // Сохраняем файл по указанному пути
        $file->storeAs('serials', $path, 'public');

        // Создаем запись файла с указанием сезона и эпизода
        $fileModel = Files::create([
            'films_id' => $validated['film_id'],
            'season_number' => $validated['season_number'],
            'episode_number' => $validated['episode_number'],
            'link' => $path,
        ]);

        return response()->json($fileModel, 201);
    }

    public function update(FileRequest $request, Files $file) {
        $validated = $request->validated();
        // Удаляем старый файл, если существует
        if ($file->link && Storage::disk('public')->exists($file->link)) 
            Storage::disk('public')->delete($file->link);

        // Сохраняем новый файл
        $uploaded = $request->file('file');
        $filename = uniqid() . '.' . $uploaded->getClientOriginalExtension();
        $path = "films/{$file->films_id}/{$filename}";
        $uploaded->storeAs('', $path, 'public');

        // Обновляем запись
        $file->update([
            'link' => $path,
        ]);

        return response()->json($file);
    }

    public function updateSerial(SerialFileRequest $request, Files $file)
    {
        // Удаляем старый файл, если он есть
        if ($file->link && Storage::disk('public')->exists($file->link)) 
            Storage::disk('public')->delete($file->link);

        // Сохраняем новый файл
        $uploaded = $request->file('file');
        $filename = uniqid() . '.' . $uploaded->getClientOriginalExtension();
        $path = "serials/{$file->films_id}/{$file->season_number}/{$filename}";
        $uploaded->storeAs('', $path, 'public');

        // Обновляем путь
        $file->update([
            'link' => $path,
        ]);

        return response()->json($file);
    }
    public function destroy(Files $file) {
        //
        Storage::disk('public')->delete($file->link);
        $file->delete();
        return response()->json(null, 204);
    }

}
