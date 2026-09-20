<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavedLocationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by bootstrap/app.php ("api" routing) and are
| prefixed with /api. Authentication uses the custom bearer-token "api" guard.
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Public catalog data (locations are browsed before logging in too).
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/locations/search', [LocationController::class, 'search']);
Route::get('/locations/map', [LocationController::class, 'map']);
Route::get('/locations/{slug}', [LocationController::class, 'show']);

// Authenticated routes.
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [ProfileController::class, 'me']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/saved', [SavedLocationController::class, 'index']);
    Route::post('/saved', [SavedLocationController::class, 'store']);
    Route::delete('/saved/{locationId}', [SavedLocationController::class, 'destroy']);
});