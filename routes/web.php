<?php

use Illuminate\Support\Facades\Route;

Route::view('/auth', 'auth.page');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    //redirect to login page
})->name('login');


