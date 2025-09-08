<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class UpdateTariffRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'duration_months' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}