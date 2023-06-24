<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User Auth
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::middleware('auth:api')->group(function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('user-profile', 'AuthController@userProfile');
});
// Dashboard
Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getUserDetails']);
});

// Patient Records
Route::middleware(['auth:api', 'permission:view_records'])->group(function () {
    Route::get('/patient-records', [PatientRecordController::class, 'index']);
    Route::get('/patient-records/{id}', [PatientRecordController::class, 'show']);
    Route::post('/patient-records', [PatientRecordController::class, 'store']);
    Route::put('/patient-records/{id}', [PatientRecordController::class, 'update']);
    Route::delete('/patient-records/{id}', [PatientRecordController::class, 'destroy']);
});

// Appointments
Route::middleware(['auth:api', 'permission:create-appointment'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
});

// Routes that require an active subscription here
Route::group(['middleware' => 'check-subscription'], function () {
    // Place routes that require an active subscription here
});

Route::post('/create-subscription', [PaymentController::class, 'createSubscription'])->middleware('auth:api');

// Email verification routes
Route::get('/email/verify', function () {
    return response(['message' => 'Please verify your email address.']);
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response(['message' => 'Email verified!']);
})->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response(['message' => 'Verification link sent!']);
})->middleware(['auth:api'])->name('verification.resend');
