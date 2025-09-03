<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\TariffController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReviewsController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\FavoriteController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('film', FilmController::class)->only('index', 'show');
Route::apiResource('category', CategoriesController::class)->only('index', 'show');
Route::apiResource('reviews', ReviewsController::class)->only('index');
Route::post('/user/{user}', [UserController::class, 'show']);

Route::apiResource('tariff', TariffController::class)->only('index', 'show');

Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [AuthController::class, 'verificationResend'])->name('verification.send');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::apiResource('favorite', FavoriteController::class)->except('update');
    Route::get('/stream/{filename}', [FileController::class, 'stream']);
    Route::get('/stream', [FileController::class, 'stream']);
    Route::apiResource('reviews', ReviewsController::class)->except('index');
    Route::middleware('admin:1')->group(function () {
        Route::apiResource('film', FilmController::class)->except('index', 'show', 'update');
        Route::post('/film/{film}', [FilmController::class, 'update']);
        Route::post('/file', [FileController::class, 'store']);
        Route::post('/file/serial', [FileController::class, 'storeSerial']);
        Route::get('/file', [FileController::class, 'index']);
        Route::post('/file/update/{file}', [FileController::class, 'update']);
        Route::post('/file/update/serial/{file}', [FileController::class, 'updateSerial']);
        Route::delete('/file/delete/{file}', [FileController::class, 'destroy']);
    });

    Route::middleware('admin:2')->group(function () {
        Route::apiResource('category', CategoriesController::class)->except('index', 'show');

    });
    Route::middleware('admin:3')->group(function () {
        Route::apiResource('tariff', TariffController::class)->except('index', 'show', 'update');
        Route::post('/tariff/{tariff}', [TariffController::class, 'update']);


    });
    Route::prefix('admin')->group(function () {
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
    Route::post('/settariff', [TariffController::class, 'setTariffToUser']);

});

Route::post('/forgot-password', [UserController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [UserController::class, 'reset']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verification'])->middleware(['throttle:6,1'])->name('verification.verify');
