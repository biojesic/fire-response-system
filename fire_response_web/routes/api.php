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
use App\Http\Controllers\AssignedIncidentController;
use App\Models\FireReports;
use Illuminate\Auth\Passwords\PasswordBroker;
use App\Http\Controllers\RouteApiController;
use App\Http\Controllers\RealTimeFireReportController;
use App\Http\Controllers\BarangayFireAidController;
use App\Http\Controllers\CivilianController;
use App\Http\Controllers\LGUController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// FIRE REPORTS
Route::apiResource('firereports', FireReportsController::class);
Route::get('/firereports/{reported_by}/user', [FireReportsController::class, 'getUserReports'])->middleware('auth:sanctum');
Route::post('/quick-report', [FireReportsController::class, 'quickReport'])->middleware('auth:sanctum');
Route::put('/fire-reports/{fireReport}/mark-contained', [RealTimeFireReportController::class, 'markAsContained'])->middleware('auth:sanctum');

// REAL TIME FIRE REPORTS
// Route::middleware('auth:sanctum')->post('/fire-report/{id}/final', [RealTimeFireReportApiController::class, 'createFinal']);
Route::post('/fire-report/{id}/final', [RealTimeFireReportController::class, 'createFinal'])->middleware('auth:sanctum');

// AUTHENTICATION
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::put('/user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

//USER CONTROLLER
Route::post('/save-fcm-token', [UserController::class, 'saveFcmToken'])->middleware('auth:sanctum');


// FIRE STATIONS
Route::middleware(['auth:sanctum'])->group(function () {
    // Route::post('/fire_stations', [FireStationController::class, 'store']);
    Route::put('/fire_stations/{id}', [FireStationController::class, 'update']);
    Route::delete('/fire_stations/{id}', [FireStationController::class, 'destroy']);
});
Route::get('/fire_stations', [FireStationController::class, 'index']);
Route::get('/fire_stations/{id}', [FireStationController::class, 'show']);
Route::post('/fire_stations', [FireStationController::class, 'store']);

// TEAMS
Route::resource('teams', TeamController::class);
Route::put('/teams/{id}/status', [TeamController::class, 'updateStatus']);

// FIRE FIGHTERS
Route::middleware('auth:sanctum')->get('/firefighter-id', [UserController::class, 'getMyFirefighterID']); // fetch fire fighter ID
Route::get('/firefighters/{id}/status', [FirefighterController::class, 'getStatus']); //firefighter id 
Route::put('/shifts/update/{id}', [ShiftController::class, 'updateShift']);
Route::get('/shifts/auto-update', [ShiftController::class, 'autoUpdateStatus']);
Route::get('/firefighters/{id}/details', [FirefighterController::class, 'getDetails']); // user id
Route::resource('firefighters', FirefighterController::class);
// Route::post('/update-status', [FirefighterController::class, 'updateStatus']);
Route::post('/register-firefighter', [FirefighterController::class, 'registerFirefighter'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/assigned-fire-reports', [FirefighterController::class, 'getAssignedFireReports']);

// FIRE FIGHTER POSITIONS
Route::get('/firefighter-positions', [FirefighterPositionController::class, 'index']);
Route::post('/firefighter-positions', [FirefighterPositionController::class, 'store']);
Route::put('/firefighter-positions/{id}', [FirefighterPositionController::class, 'update']);
Route::delete('/firefighter-positions/{id}', [FirefighterPositionController::class, 'destroy']);

Route::resource('firefighter_reports', FirefighterReportsController::class);

Route::get('/assigned-incident/{firefighterId}', [AssignedIncidentController::class, 'showAssignedIncidentByFirefighter']);

// Route::resource('notifications', NotificationsController::class);

Route::post('/update-location', [LocationController::class, 'updateLocation']);
Route::get('/get-locations', [LocationController::class, 'getLocations']);

Route::post('/get-route-eta', [RouteApiController::class, 'getRouteAndETA']);
Route::get('/eta-for-civilians', [RouteApiController::class, 'getETAForCivilians']);
Route::post('/update-eta', [RouteApiController::class, 'storeETA']);

// PUBLIC AWARENESS MODULE
Route::get('/ongoing-incidents', [FireReportsController::class, 'getOngoingIncidents']);

// BRGY FIRE AID
Route::put('/fire-aid/{id}', [BarangayFireAidController::class, 'update'])->middleware('auth:sanctum');
Route::post('/register/fire-aid', [BarangayFireAidController::class, 'store']);
Route::get('/fire-aid', [BarangayFireAidController::class, 'show'])->middleware('auth:sanctum');
Route::post('/fire-aid/{id}/mark-false-alarm', [BarangayFireAidController::class, 'markAsFalseAlarm'])->middleware('auth:sanctum');
Route::get('/barangay/{barangay_id}/fire-reports', [BarangayFireAidController::class, 'showBarangayReportsForFireAids'])->middleware('auth:sanctum');

// NOTIF
Route::post('/send-notification', [NotificationsController::class, 'sendPushNotification'])->middleware('auth:sanctum');

// CIVILIANS
Route::get('/civilian/rejected', [CivilianController::class, 'getRejectedApplication']);
Route::post('/civilian/reapply', [CivilianController::class, 'reapply']);

// LGU
Route::apiResource('lgus', LGUController::class);



// Route::post('/test-email', function(Request $request) {
//     Mail::raw('This is a test email from Laravel using Mailtrap.', function ($message) use ($request) {
//         $message->to($request->email)  // Use the email passed in the body
//                 ->subject('Test Email');
//     });

//     return response()->json(['message' => 'Test email sent successfully!']);
// });




// Route::get('/test', function () {
//     return response()->json(['message' => 'API is working!']);
// });
