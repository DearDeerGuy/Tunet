<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class ReviewsRequest extends CustomRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
            'film_id' => ['required', 'integer', 'exists:films,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
