<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(
        StorePaymentRequest $request,
        Order $order,
        PaymentService $paymentService
    ) {
        $payment = $paymentService->createPayment(
            $request->user(),
            $order,
            $request->string('method')->toString()
        );

        return response()->json([
            'message' => 'Payment created successfully.',
            'data' => $payment,
        ], 201);
    }

    public function show(
        Request $request,
        Order $order,
        PaymentService $paymentService
    ) {
        $payment = $paymentService->getPayment(
            $request->user(),
            $order
        );

        return response()->json([
            'message' => 'Payment retrieved successfully.',
            'data' => $payment,
        ]);
    }
}
