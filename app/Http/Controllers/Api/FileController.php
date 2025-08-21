<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Http\Requests\SerialFileRequest;
use App\Models\Files;
use App\Models\Films;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'film_id' => ['required', 'exists:films,id'],
        ]);

        $filmId = $validated['film_id'];
        $film = Films::with('files')->find($filmId);
        $result = [];
        if ($film->type === 'film') {
            $file = $film->files->first();

            if ($file)
                $result = ['link' => $file->link];
            else
                $result = [];
        } else
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
                        'link' => $file->link,
                    ];
                });

        return response()->json($result);
    }
    public function getSerialInfo(Films $film)
    {
        if ($film->type != "serial")
            return response()->json(['message' => 'Не серіал']);

        $seasons = $film->files->groupBy('season_number');

        return response()->json($seasons);
    }

    public function store(FileRequest $request)
    {
        $validated = $request->validated();

        // Проверяем, есть ли уже файлы у фильма
        $existingFilesCount = Files::where('films_id', $validated['film_id'])->count();
        if ($existingFilesCount > 0)
            return response()->json(['message' => 'Файл для цього фільму вже завантажений. Видаліть чи оновіть існуючий'], 409);

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
    public function storeSerial(SerialFileRequest $request)
    {
        $validated = $request->validated();

        $serial = Films::find($validated['film_id']);
        if (!$serial || $serial->type != "serial")
            return response()->json('Не серіал', 400);

        $file = $request->file('file');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = "{$validated['film_id']}/{$validated['season_number']}/{$filename}";

        // Сохраняем файл по указанному пути
        $file->storeAs('films', $path, 'public');

        // Создаем запись файла с указанием сезона и эпизода
        $fileModel = Files::create([
            'films_id' => $validated['film_id'],
            'season_number' => $validated['season_number'],
            'episode_number' => $validated['episode_number'],
            'link' => $path,
        ]);

        return response()->json($fileModel, 201);
    }

    // Переделать
    public function update(FileRequest $request, Files $file)
    {
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
    // Переделать
    public function updateSerial(SerialFileRequest $request, Files $file)
    {
        // Удаляем старый файл, если он есть
        if ($file->link && Storage::disk('public')->exists($file->link))
            Storage::disk('public')->delete($file->link);

        // Сохраняем новый файл
        $uploaded = $request->file('file');
        $filename = uniqid() . '.' . $uploaded->getClientOriginalExtension();
        $path = "films/{$file->films_id}/{$file->season_number}/{$filename}";
        $uploaded->storeAs('', $path, 'public');

        // Обновляем путь
        $file->update([
            'link' => $path,
        ]);

        return response()->json($file);
    }
    // Проверить
    public function destroy(Files $file)
    {
        Storage::disk('public')->delete($file->link);
        $file->delete();

        Files::where('id', $file->id)->delete();

        return response()->json(null, 204);
    }


    public function stream(Request $request, $filename)
    {
        if (isset($request['path']) && !empty($request['path'])) {
            $filename = $request['path'];
        }
        $videoPath = storage_path('app/public/films/' . $filename);
        if (!file_exists($videoPath)) {
            //abort(404, 'Файл не найден');
            return response()->json(['message' => 'Файл не знайдено'], 404);
        }
        $fileSize = filesize($videoPath);
        $start = 0;
        $end = $fileSize - 1;

        if ($request->headers->has('Range')) {
            preg_match('/bytes=(\d+)-(\d*)/', $request->header('Range'), $matches);
            $start = intval($matches[1]);
            if (!empty($matches[2])) {
                $end = intval($matches[2]);
            }
        }

        $length = $end - $start + 1;

        $response = new StreamedResponse(function () use ($videoPath, $start, $length) {
            $handle = fopen($videoPath, 'rb');
            fseek($handle, $start);
            $bufferSize = 1024 * 8;

            while (!feof($handle) && $length > 0) {
                $read = min($bufferSize, $length);
                echo fread($handle, $read);
                flush();
                $length -= $read;
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'video/mp4');
        $response->headers->set('Content-Length', $length);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Range', "bytes $start-$end/$fileSize");
        $response->setStatusCode($request->headers->has('Range') ? 206 : 200);

        return $response;
    }

}
