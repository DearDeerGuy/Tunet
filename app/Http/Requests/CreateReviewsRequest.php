<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class CreateReviewsRequest extends CustomRequest
{
    public function rules(): array
    {
        $rules = [
            'mark' => ['required', 'numeric', 'min:1', 'max:10'],
            'comment' => ['nullable', 'string']
        
        ];
        if($this->isMethod('POST')){
            $rules['user_id'] =  ['required', 'exists:users,id', 'unique:reviews,user_id,NULL,id,film_id,' . $this->film_id,];
            $rules['film_id'] =  ['required', 'exists:films,id'];

        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }

}