<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'password'      => ['required', 'string', 'min:6'],
            'new_password'  => ['required', 'string', 'min:6', 'different:password', 'confirmed'],
        ];
    }
}
