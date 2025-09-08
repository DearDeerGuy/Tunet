<?php

namespace App\Http\Requests;
use App\Http\Requests\CustomRequest;

class BannedRequest extends CustomRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

}
