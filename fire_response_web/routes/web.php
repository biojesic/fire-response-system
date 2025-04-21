<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\FirefighterWebController;
use App\Http\Controllers\PersonalEquipmentController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/firefighters/register', [FirefighterWebController::class, 'showRegistrationForm'])
        ->name('admin.firefighters.register.form');

    Route::post('/firefighters/register', [FirefighterWebController::class, 'registerFirefighter'])
        ->name('admin.firefighters.register');
        Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {

    Route::get('/login', function () {
        return view('auth_pages.login');
    })->name('login');
    
    Route::post('/login', [AuthWebController::class, 'login'])->name('login.submit');

});

Route::get('/admin/dashboard', [AdminDashboardController::class, 'showDashboard'])->name('admin.dashboard');

Route::get('/admin/firefighters', function () {
    return view('admin_pages.firefighters');
})->name('admin.firefighters');

// Route::get('/admin/dashboard', function () {
//     return view('admin_pages.dashboard');
// })->middleware('auth')->name('admin.dashboard');

// Dispatch Routes
Route::post('/dispatch/team/{teamId}', [AdminDashboardController::class, 'dispatchToIncident'])->name('admin.dispatch');
Route::post('/dispatch/incident/{incidentId}', [AdminDashboardController::class, 'assignTeamToIncident'])->name('admin.dispatch.incident');


Route::middleware('auth')->group(function () {
    Route::get('/admin/equipment', [PersonalEquipmentController::class, 'index'])->name('admin.equipment.list');
    Route::get('/admin/equipment/create', [PersonalEquipmentController::class, 'create'])->name('admin.equipment.create');
    Route::post('/admin/equipment', [PersonalEquipmentController::class, 'store'])->name('admin.equipment.store');
    Route::get('/admin/equipment/{id}/edit', [PersonalEquipmentController::class, 'edit'])->name('admin.equipment.edit');
    Route::put('/admin/equipment/{id}', [PersonalEquipmentController::class, 'update'])->name('admin.equipment.update');
    Route::delete('/admin/equipment/{id}', [PersonalEquipmentController::class, 'destroy'])->name('admin.equipment.destroy');
});