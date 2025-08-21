<?php

namespace App\Services;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function sendResetLinkEmail($request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Посилання для скидання пароля надіслано на пошту.'])
            : response()->json(['message' => 'Помилка надсилання посилання.'], 400);
    }
    public function reset($request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->tokens()->delete();
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Пароль успішно скинутий.'])
            : response()->json(['message' => 'Помилка під час скидання пароля.'], 400);
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Неправильний поточний пароль.'
            ], 403);
        }

        $user->password = Hash::make($request->new_password);

        $user->save();

        return response()->json([
            'message' => 'Пароль успішно оновлено.'
        ]);
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->fill($request->validated());

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $path;
        }

        $user->save();


        return response()->json([
            'message' => 'Дані успішно оновлено.',
            'user' => $user,
        ]);
    }
}
