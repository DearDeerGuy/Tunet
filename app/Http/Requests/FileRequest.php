<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class FileRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'film_id' => ['required', 'exists:films,id'],
            'file' => ['required', 'file', 'mimes:mp4,avi,mpeg,mov,mkv','max:10485760'], 
        ];
    }
}
