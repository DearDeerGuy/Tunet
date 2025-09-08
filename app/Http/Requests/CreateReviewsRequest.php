<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class CreateReviewsRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'film_id' => ['required', 'exists:films,id'],
            'mark' => ['required', 'numeric', 'min:1', 'max:10'],
            'comment' => ['required', 'string', 'nullable'],
        ];
    }

}