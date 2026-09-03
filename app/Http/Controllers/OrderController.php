<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(
        Request $request,
        OrderService $orderService
    ) {
        $order = $orderService->createOrder(
            $request->user()
        );

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }
}
