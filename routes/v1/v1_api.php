<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return response()->json(['status' => 'ok']);
    });

    Route::get('/health', function () {
        return response()->json(
            [
                'message' => 'Application is running ok.',
                'data' => null,
            ],
            200,
        );
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/protected', function () {
            return response()->json(
                [
                    'message' => 'OK',
                    'data' => null,
                ],
                200,
            );
        });
    });
});
