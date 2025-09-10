<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
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
                $result =
                    [
                        'id' => $file->id,
                        'link' => $file->link
                    ];
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
                        'id' => $file->id,
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
        $film_id = $validated['films_id'];

        $query = Files::where('films_id', $film_id);

        $film = Films::find($film_id);
        $season = $validated['season_number'] ?? null;
        $episode = $validated['episode_number'] ?? null;

        if ($film['type'] === 'serial')
            $query->where('season_number', $season)->where('episode_number', $episode);

        $existingFilesCount = $query->count();
        if ($existingFilesCount > 0)
            return response()->json(['message' => 'Файл вже завантажений. Видаліть чи оновіть існуючий'], 409);

        $file = $request->file('file');

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('films', $filename, 'public');

        $validated['link'] = $filename;
        $fileModel = Files::create($validated);

        return response()->json($fileModel, 201);
    }

    public function update(FileRequest $request, Files $file)
    {
        $validated = $request->validated();
        // Удаляем старый файл, если существует
        if ($file->link && Storage::disk('public')->exists('films/' . $file->link))
            Storage::disk('public')->delete('films/' . $file->link);

        // Сохраняем новый файл
        $uploaded = $request->file('file');
        $filename = uniqid() . '.' . $uploaded->getClientOriginalExtension();
        $uploaded->storeAs('films', $filename, 'public');
        $validated['link'] = $filename;        // Обновляем запись
        $file->update($validated);

        return response()->json($file);
    }



    public function destroy(Files $file)
    {
        Storage::disk('public')->delete('films/' . $file->link);
        $file->delete();

        return response()->json(null, 204);
    }
    public function stream(Request $request, $filename)
    {

        $videoPath = storage_path('app/public/films/' . $filename);
        if (!file_exists($videoPath)) {
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
