<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:checkout'])->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store']);
});
