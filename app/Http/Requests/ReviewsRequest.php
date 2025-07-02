<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ReviewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }
    public function rules(): array
    {
        return [
            'page' => ['integer', 'min:1'],
            'perPage' => ['integer', 'min:1'],
            'film_id'=> ['required', 'integer', 'exists:films,id'],
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
