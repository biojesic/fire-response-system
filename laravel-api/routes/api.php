<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FireReportsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirefighterController;
use App\Http\Controllers\FireStationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\FirefighterReportsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FirefighterPositionController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Models\FireReports;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('firereports', FireReportsController::class)->middleware('auth:sanctum');
Route::get('/firereports/{reported_by}/user', [FireReportsController::class, 'getUserReports'])->middleware('auth:sanctum');
Route::post('/quick-report', [FireReportsController::class, 'quickReport'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::put('/user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::post('/fire_stations', [FireStationController::class, 'store']);
    Route::put('/fire_stations/{id}', [FireStationController::class, 'update']);
    Route::delete('/fire_stations/{id}', [FireStationController::class, 'destroy']);
});
Route::get('/fire_stations', [FireStationController::class, 'index']);
Route::get('/fire_stations/{id}', [FireStationController::class, 'show']);
Route::post('/fire_stations', [FireStationController::class, 'store']);

Route::resource('teams', TeamController::class);
Route::put('/teams/{id}/status', [TeamController::class, 'updateStatus']);

Route::get('/firefighters/{id}/status', [FirefighterController::class, 'getStatus']); //firefighter id 
Route::put('/shifts/update/{id}', [ShiftController::class, 'updateShift']);
Route::get('/shifts/auto-update', [ShiftController::class, 'autoUpdateStatus']);
Route::get('/firefighters/{id}/details', [FirefighterController::class, 'getDetails']); // user id
Route::resource('firefighters', FirefighterController::class);
// Route::post('/update-status', [FirefighterController::class, 'updateStatus']);
Route::post('/register-firefighter', [FirefighterController::class, 'registerFirefighter'])->middleware('auth:sanctum');

Route::get('/firefighter-positions', [FirefighterPositionController::class, 'index']);
Route::post('/firefighter-positions', [FirefighterPositionController::class, 'store']);
Route::put('/firefighter-positions/{id}', [FirefighterPositionController::class, 'update']);
Route::delete('/firefighter-positions/{id}', [FirefighterPositionController::class, 'destroy']);

Route::resource('firefighter_reports', FirefighterReportsController::class);

Route::resource('notifications', NotificationsController::class);

Route::post('/update-location', [LocationController::class, 'updateLocation']);
Route::get('/get-locations', [LocationController::class, 'getLocations']);











// Route::get('/test', function () {
//     return response()->json(['message' => 'API is working!']);
// });
