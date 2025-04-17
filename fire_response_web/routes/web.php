<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\FirefighterWebController;

// Route::get('/', function () {
//     return view('welcome');  // This returns the view located in resources/views/home.blade.php
// });

Route::get('/', function () {
    return view('index');
})->name('home');

// Route::get('/register', function () {
//     return view('admin_pages.register_firefighter');
// })->name('register');
Route::middleware('auth')->group(function () {
    Route::get('/firefighters/register', [FirefighterWebController::class, 'showRegistrationForm'])
        ->name('admin.firefighters.register.form');

    Route::post('/firefighters/register', [FirefighterWebController::class, 'registerFirefighter'])
        ->name('admin.firefighters.register');
});

Route::get('/login', function () {
    return view('auth_pages.login');
})->name('login');

Route::post('/login', [AuthWebController::class, 'login'])->name('login.submit');

Route::get('/admin/firefighters', function () {
    return view('admin_pages.firefighters');
})->name('admin.firefighters');

Route::get('/admin/dashboard', function () {
    return view('admin_pages.dashboard');
})->name('admin.dashboard');