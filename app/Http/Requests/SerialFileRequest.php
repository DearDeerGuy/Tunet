<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SerialFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'film_id' => ['required', 'exists:films,id'],
            'file' => ['required', 'file', 'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/x-matroska', 'max:10240000'],
            'season_number' => ['required', 'integer'],
            'episode_number' => ['required', 'integer'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
