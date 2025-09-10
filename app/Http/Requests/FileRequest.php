<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;
use App\Models\Films;

class FileRequest extends CustomRequest
{
    public function rules(): array
    {
        $isUpdate = $this->routeIs('file.update');
        $rules = [
            'file' => ['required', 'file', 'mimes:mp4,avi,mpeg,mov,mkv', 'max:10485760'],
        ];
        if (!$isUpdate) {
            $rules['films_id'] = ['required', 'exists:films,id'];
        }
        $filmId = $isUpdate ?
            $this->route('file')->films_id :
            $this->films_id;
        $film = Films::find($filmId);


        if ($film->type === "serial") {
            if ($isUpdate) {
                $currentFileId = $this->route('file')->id;
                $season = $this->input('season_number', $this->route('file')->season_number);
                $episode = $this->input('episode_number', $this->route('file')->episode_number);

                if ($this->has('season_number') || $this->has('episode_number')) {
                    $rules['episode_number'] = [ 'integer', 'unique:files,episode_number,' . $currentFileId . ',id,season_number,' . $season   ];
                    $rules['season_number'] = [ 'integer','unique:files,season_number,' . $currentFileId . ',id,episode_number,' . $episode  ];
                }
            } else {
                $rules['season_number'] = ['required', 'integer'];
                $rules['episode_number'] = ['required', 'integer'];
            }

        }
        return $rules;
    }
}
