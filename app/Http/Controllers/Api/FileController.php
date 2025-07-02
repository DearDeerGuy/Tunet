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

    }
    public function getSerialInfo(Films $film) {
        if($film->type!="serial"){
            return response()->json(['message'=>'Is not a serial']);
        }
        $seasons = $film->files->groupBy('season_number');
        return response()->json($seasons);
    }

    public function store(FileRequest $request) {

    }
    public function storeSerial(Request $request) {
        $validated = $request->validate([
            'film_id' => ['exists:films,id', 'required'],
            'episode_number' => ['integer', 'required'],
            'season_number' => ['integer', 'required'],
            'file' => ['required', 'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/x-matroska', 'max:10240000'],
        ]);
    }
    public function update(FileRequest $request, Files $file) {

    }
    public function destroy(Files $file) {

    }
}
