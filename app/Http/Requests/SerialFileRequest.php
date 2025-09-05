<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

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
            'file' => ['required', 'file', 'mimes:mp4,avi,mpeg,mov,mkv','max:10485760'],
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
