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
        return [
            'poster' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:1000'],
            'release_date'=> ['required','date'],
            'type'=>['required', 'in:film,serial'],
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
