<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\FirefighterWebController;
use App\Http\Controllers\PersonalEquipmentController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\FireReportsController;
use App\Http\Controllers\FireReportsWebController;
use App\Http\Controllers\TeamWebController;
use App\Http\Controllers\FirefighterRankController;
use App\Http\Controllers\RealTimeFireReportWebController;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::middleware('auth')->group(function () {
    //Admin Dashboard Tab
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    Route::post('/fire-reports/{fireReport}/mark-contained', [FireReportsController::class, 'markAsContained'])->name('markAsContained'); 
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'showDashboard'])->name('admin.dashboard');
    Route::get('/fire-reports/{id}/initial', [RealTimeFireReportWebController::class, 'showInitial'])->name('fire-reports.initial.show');
    Route::get('/fire-reports/{id}/progress', [RealTimeFireReportWebController::class, 'createProgress'])->name('fire-reports.progress.create');
    Route::get('/fire-reports/{id}/final', [RealTimeFireReportWebController::class, 'createFinal'])->name('fire-reports.final.create');
    Route::post('/fire-reports/{id}/initial', [RealTimeFireReportWebController::class, 'createInitial'])->name('fire-reports.initial');
    Route::post('/fire-reports/{id}/progress', [RealTimeFireReportWebController::class, 'createProgress'])->name('fire-reports.progress');
    Route::post('/fire-reports/{id}/final', [RealTimeFireReportWebController::class, 'createFinal'])->name('fire-reports.final');
    
    //Admin Firefighters tab
    Route::get('/admin/firefighters', [FirefighterWebController::class, 'index'])->name('admin.firefighters');
    Route::get('admin/firefighters/register-form', [FirefighterWebController::class, 'showRegistrationForm'])
        ->name('admin.firefighters.register.form');
    Route::post('admin/firefighters/register', [FirefighterWebController::class, 'registerFirefighter'])
        ->name('admin.firefighters.register');

    // Admin Teams tab
    Route::get('/admin/teams', [TeamWebController::class, 'index'])->name('admin.teams');
    Route::patch('/teams/{team}/remove-firefighter/{firefighter}', [TeamWebController::class, 'removeFirefighter'])->name('teams.removeFirefighter');
    Route::post('/teams/{team}/assign/{firefighter}', [TeamWebController::class, 'assignFirefighter'])->name('teams.assign');

    // Admin Equipments Tab
    Route::get('/admin/equipment', [PersonalEquipmentController::class, 'index'])->name('admin.equipment.list');
    Route::get('/admin/equipment/create', [PersonalEquipmentController::class, 'create'])->name('admin.equipment.create');
    Route::post('/admin/equipment', [PersonalEquipmentController::class, 'store'])->name('admin.equipment.store');
    Route::get('/admin/equipment/{id}/edit', [PersonalEquipmentController::class, 'edit'])->name('admin.equipment.edit');
    Route::put('/admin/equipment/{id}', [PersonalEquipmentController::class, 'update'])->name('admin.equipment.update');
    Route::delete('/admin/equipment/{id}', [PersonalEquipmentController::class, 'destroy'])->name('admin.equipment.destroy');

    // Admin Fire Reports Tab
    Route::get('/admin/fire-reports', [FireReportsWebController::class, 'index'])->name('admin.fire_reports');
    Route::get('/admin/fire-reports/{report}', [FireReportsWebController::class, 'show'])->name('admin.fire_reports.show');

    // Admin Firefighter Ranks
    Route::resource('admin/firefighterRanks', FirefighterRankController::class);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth_pages.login');
    })->name('login');
    
    Route::post('/login', [AuthWebController::class, 'login'])->name('login.submit');
});


// Route::get('/admin/firefighters', function () {
//     return view('admin_pages.firefighters');
// })->name('admin.firefighters');

// Route::get('/admin/dashboard', function () {
//     return view('admin_pages.dashboard');
// })->middleware('auth')->name('admin.dashboard');

// Dispatch Routes
Route::post('/dispatch/team/{teamId}', [AdminDashboardController::class, 'dispatchToIncident'])->name('admin.dispatch');
// Route::post('/dispatch/incident/{incidentId}', [AdminDashboardController::class, 'assignTeamToIncident'])->name('admin.dispatch.incident');



