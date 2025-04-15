<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');  // This returns the view located in resources/views/home.blade.php
// });

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/register', function () {
    return view('auth_pages.register');
})->name('register');

Route::get('/login', function () {
    return view('auth_pages.login');
})->name('login');
