<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
class UserController extends Controller
{
    public function sendResetLinkEmail(UserService $service, ForgotPasswordRequest $request)
    {
        return $service->sendResetLinkEmail($request);
    }
    public function reset(UserService $service, ResetPasswordRequest $request)
    {
        return $service->reset($request);
    }
    public function changePassword(UserService $service, ChangePasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        return $service->changePassword($request);
    }
    public function updateProfile(UserService $service, UpdateProfileRequest $request): \Illuminate\Http\JsonResponse
    {
        return $service->updateProfile($request);
    }
    public function show(User $user): \Illuminate\Http\JsonResponse{
        return response()->json($user);
    }
}
