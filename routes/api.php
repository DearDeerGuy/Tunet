<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideoStreamController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [AuthController::class, 'verificationResend'])->name('verification.send');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    Route::prefix('admin')->group(function () {
        // default page
        Route::middleware('admin:1')->group(function () {

        });
        Route::middleware('admin:2')->group(function () {
            Route::post('/ban', [AdminController::class, 'ban'])->name('admin.ban');

        });
        Route::middleware('admin:3')->group(function () {
            Route::post('/unban', [AdminController::class, 'unban'])->name('admin.unban');

            Route::post('/make', [AdminController::class, 'makeAdmin'])->name('admin.makeAdmin');


        });
    });

});
Route::get('/video/{filename}', [VideoStreamController::class, 'stream']);

Route::post('/forgot-password', [UserController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [UserController::class, 'reset']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verification'])->middleware(['throttle:6,1'])->name('verification.verify');
