<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test/admin', function () {
        return response()->json(
            [
                'message' => 'OK',
                'data' => null,
            ],
            200,
        );
    })->middleware('role:admin');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test/anyrole', function () {
        return response()->json(
            [
                'message' => 'OK',
                'data' => null,
            ],
            200,
        );
    })->middleware('role:user,role:admin');

});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/protected', function () {
        return response()->json([
            'message' => 'OK',
            'data' => null,
        ], 200);
    });
});
