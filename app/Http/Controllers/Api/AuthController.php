<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Services\AuthService;
use App\Services\OAuthService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(AuthService $service, StoreUserRequest $request)
    {
        return $service->register($request->validated());
    }
    public function login(AuthService $service, LoginRequest $request)
    {
        return $service->login($request->validated());
    }
    public function logout(AuthService $service, Request $request)
    {
        return $service->logout($request->user());
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback(OAuthService $service)
    {
        return $service->handle();
    }
    public function verificationResend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Письмо с подтверждением отправлено повторно']);
    }
    public function verification(OAuthService $service, Request $request, $id, $hash)
    {
        return $service->verification($request, $id, $hash);
    }
}
