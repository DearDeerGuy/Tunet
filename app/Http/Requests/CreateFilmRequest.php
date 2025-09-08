<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class CreateFilmRequest extends CustomRequest
{
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:1000'],
            'release_date' => ['required', 'date'],
            'type' => ['required', 'in:film,serial'],
            'country' => ['required', 'string', 'max:255'],
            'producer' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'actors' => ['required', 'string', 'max:255'],
        ];
        // При обновлении фильма отправка файла постера не обязательна
        $rules['poster'] = $this->routeIs('film.index') ?
            ['required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'] :
            ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'];

        return $rules;
    }

}
