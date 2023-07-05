<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientRecordController;
use App\Http\Controllers\AppointmentController;
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
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user-profile', [AuthController::class, 'userProfile']);
    Route::get('dashboard', [DashboardController::class, 'getUserDetails']);
    Route::post('create-subscription', [PaymentController::class, 'createSubscription']);
});

// Patient Records
Route::middleware(['auth:api', 'permission:view_records'])->group(function () {
    // Route::middleware(['auth:api'])->group(function () {
    Route::get('patient-records', [PatientRecordController::class, 'index']);
    Route::get('patient-records/{id}', [PatientRecordController::class, 'show']);
    Route::post('patient-records', [PatientRecordController::class, 'store']);
    Route::put('patient-records/{id}', [PatientRecordController::class, 'update']);
    Route::delete('patient-records/{id}', [PatientRecordController::class, 'destroy']);
});

// Appointments
Route::middleware(['auth:api', 'permission:create-appointment'])->group(function () {
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::get('appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::put('appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy']);
});

// Routes that require an active subscription here
Route::group(['middleware' => 'check-subscription'], function () {
    // Place routes that require an active subscription here
});

// Email verification routes
Route::get('email/verify', function () {
    return response(['message' => 'Please verify your email address.']);
})->name('verification.notice');

Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response(['message' => 'Email verified!']);
})->name('verification.verify');

Route::post('email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response(['message' => 'Verification link sent!']);
})->middleware(['auth:api'])->name('verification.resend');

// Handle Stripe Webhook
Route::post('stripe/webhook', [PaymentController::class, 'handleWebhook']);
