<?php

use Illuminate\Support\Facades\Route;

Route::view('/auth', 'auth.page');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    //redirect to login page
})->name('login');
Route::get('/reset', function () {
    //redirect to reset page
})->name('password.reset');



