<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class CreateFilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:1000'],
            'release_date'=> ['required','date'],
            'type' => ['required', 'in:film,serial'],
            'country' => ['required', 'string', 'max:255'],
            'producer' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'actors' => ['required', 'string', 'max:255'],
        ];
        // При обновлении фильма отправка файла постера не обязательна
        $rules['poster'] = $this->isMethod('post') ?
            ['required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'] :
            ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:4096'];

        return $rules;
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
