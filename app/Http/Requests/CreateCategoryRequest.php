<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class CreateCategoryRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories'],
        ];
    }
}
