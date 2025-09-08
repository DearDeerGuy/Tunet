<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;

class GetFilmsRequest extends CustomRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'type' => ['nullable', 'in:film,serial'],
            'categories' => ['nullable', 'string'],
            'of' => ['nullable', 'in:title,release_date,country,rating,producer,actors,type'],
            'ot' => ['nullable', 'in:asc,desc'],
        ];
    }

}
