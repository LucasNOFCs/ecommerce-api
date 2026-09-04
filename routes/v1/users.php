<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);

        Route::put('/me', [AuthController::class, 'updateMe']);

        Route::delete('/me', [AuthController::class, 'deleteMe']);
    });
});

Route::prefix('auth')->group(function () {
    Route::middleware(['throttle:auth'])->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
