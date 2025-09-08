<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class StoreTariffRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'duration_months' => ['required', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
