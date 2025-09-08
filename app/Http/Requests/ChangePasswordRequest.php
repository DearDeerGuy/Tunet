<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class ChangePasswordRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'password'      => ['required', 'string', 'min:6'],
            'new_password'  => ['required', 'string', 'min:6', 'different:password', 'confirmed'],
        ];
    }
}
