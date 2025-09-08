<?php
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
class CustomRequest extends FormRequest
{
  public function authorize(): bool
  {
    return Auth::check();
  }

  public function failedValidation(Validator $validator)
  {
    throw new HttpResponseException(response()->json([
      'status' => 'error',
      'errors' => $validator->errors(),
    ], 422));
  }
}