<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Неверные учетные данные'
            ], 401);
        }
        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вы вышли из системы']);
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $accessToken = $googleUser->token;

        $response = Http::withToken($accessToken)->get('https://people.googleapis.com/v1/people/me?personFields=birthdays');

        $birthdays = $response->json()['birthdays'] ?? null;

        $birthdate = null;

        if ($birthdays) {
            $birthdayData = collect($birthdays)->last();
            $birthdate = $birthdayData['date'] ?? null;
            if ($birthdate) {
                $birthdate = sprintf('%04d-%02d-%02d', $birthdate['year'] ?? 1900, $birthdate['month'], $birthdate['day']);
            }
        }
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(Str::random(16)),
                'date_of_birth' => $birthdate
            ]
        );
        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }
    public function verificationResend(Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Письмо с подтверждением отправлено повторно']);
    }
    public function verification(Request $request, $id, $hash) {
        $user = User::findOrFail($id);

        if (! URL::hasValidSignature($request))
            return response()->json(['message' => 'Недопустимая или просроченная ссылка'], 403);

        if ($user->hasVerifiedEmail())
            return redirect(config('app.frontend_url') . '/email-verified?status=already-verified');

        if (! hash_equals(sha1($user->email), $hash))
            return response()->json(['message' => 'Неверный хеш'], 403);

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect(config('app.frontend_url') . '/email-verified?status=success');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Ссылка для сброса пароля отправлена на почту.'])
            : response()->json(['message' => 'Ошибка отправки ссылки.'], 400);
    }
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Пароль успешно сброшен.'])
            : response()->json(['message' => 'Ошибка при сбросе пароля.'], 400);
    }
}
