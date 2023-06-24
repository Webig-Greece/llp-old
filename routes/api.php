<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/patient-records', 'PatientRecordController@index')->middleware('permission:view_records');
