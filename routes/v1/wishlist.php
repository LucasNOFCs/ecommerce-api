<?php

use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'show']);

    Route::post('/wishlist/items', [
        WishlistController::class,
        'store',
    ]);

    Route::delete('/wishlist/items/{item}', [
        WishlistController::class,
        'destroy',
    ]);
});
