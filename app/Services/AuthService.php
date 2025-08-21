<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register($data)
    {
        $user = User::create($data);
        $user->sendEmailVerificationNotification();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login($credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Неправильні облікові дані']
            ]);
        }


        $user = Auth::user();
        if ($user->isBanned) {
            return response()->json([
                "message" => "Ваш обліковий запис заблоковано"
            ], 403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(User $user)
    {
        $user->currentAccessToken()?->delete();
        return response()->json(['message' => 'Ви вийшли із системи']);
    }
}
