<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class MakeAdminRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'admin_lvl' => ['required', 'integer', 'between:0,1,2,3'],
        ];
    }
}
