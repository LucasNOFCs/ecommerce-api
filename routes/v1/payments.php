<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post(
        '/orders/{order}/payment',
        [PaymentController::class, 'store']
    );

    Route::get(
        'orders/{order}/payment',
        [PaymentController::class, 'show']
    );
});

Route::post(
    '/payments/webhook',
    PaymentWebhookController::class
);
