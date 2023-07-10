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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/upgrade', [AuthController::class, 'upgrade']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/user-profile', [AuthController::class, 'userProfile']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'getUserDetails'])
        ->middleware('permission:view_analytics');

    // Payment and Subscription
    Route::post('create-subscription', [PaymentController::class, 'createSubscription'])
        ->middleware('permission:manage_subscriptions');
});

// Patient Records
Route::middleware(['auth:api'])->group(function () {
    Route::get('patient-records', [PatientRecordController::class, 'index'])
        ->middleware('permission:view_own_records');
    Route::get('patient-records/{id}', [PatientRecordController::class, 'show'])
        ->middleware('permission:view_own_records');
    Route::post('patient-records', [PatientRecordController::class, 'store'])
        ->middleware('permission:create_records');
    Route::put('patient-records/{id}', [PatientRecordController::class, 'update'])
        ->middleware('permission:edit_own_records');
    Route::delete('patient-records/{id}', [PatientRecordController::class, 'destroy'])
        ->middleware('permission:edit_own_records');
});

// Appointments
Route::middleware(['auth:api'])->group(function () {
    Route::get('appointments', [AppointmentController::class, 'index'])
        ->middleware('permission:manage_own_appointments');
    Route::get('appointments/{id}', [AppointmentController::class, 'show'])
        ->middleware('permission:manage_own_appointments');
    Route::post('appointments', [AppointmentController::class, 'store'])
        ->middleware('permission:manage_own_appointments');
    Route::put('appointments/{id}', [AppointmentController::class, 'update'])
        ->middleware('permission:manage_own_appointments');
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy'])
        ->middleware('permission:manage_own_appointments');
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
