<?php

namespace App\Http\Requests;


use App\Http\Requests\CustomRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'date_of_birth' => ['nullable', 'date'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
