<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthService
{
    private function getAvatar($googleUser){
        if ($googleUser->getAvatar()) {
            $avatarContents = file_get_contents($googleUser->getAvatar());
            $filename = 'avatars/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $avatarContents);
            return $filename;
        }
        return null;
    }

    private function getBirthday($googleUser)
    {
        $accessToken = $googleUser->token;

        $response = Http::withToken($accessToken)->get('https://people.googleapis.com/v1/people/me?personFields=birthdays');

        $birthdays = $response->json()['birthdays'] ?? null;
        if ($birthdays) {
            $birthdayData = collect($birthdays)->last();
            $birthdate = $birthdayData['date'] ?? null;
            if ($birthdate) {
                $birthdate = sprintf('%04d-%02d-%02d', $birthdate['year'] ?? 1900, $birthdate['month'], $birthdate['day']);

            }
            return $birthdate;
        }
        return null;
    }
    function handle()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $birthdate = $this->getBirthday($googleUser);

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(Str::random(16)),
                'date_of_birth' => $birthdate
            ]
        );
        $user->sendEmailVerificationNotification();
        if (!$user->avatar) {
            $filename = $this->getAvatar($googleUser);
            $user->avatar = $filename;
            $user->save();
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }
    public function verification($request, $id, $hash) {
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
}


