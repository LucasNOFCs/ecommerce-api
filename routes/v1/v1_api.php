<?php

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

    require __DIR__.'/products.php';
    require __DIR__.'/users.php';
    require __DIR__.'/categories.php';
    require __DIR__.'/tests.php';
    require __DIR__.'/carts.php';
    require __DIR__.'/checkout.php';
    require __DIR__.'/orders.php';
});
