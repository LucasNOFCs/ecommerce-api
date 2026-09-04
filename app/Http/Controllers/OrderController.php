<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderHistoryRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
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

    public function index(
        OrderHistoryRequest $request,
        OrderService $orderService
    ): JsonResponse {
        $orders = $orderService->getOrderHistory(
            $request->user(),
            $request->validated('status')
        );

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function show(
        Request $request,
        int $order,
        OrderService $orderService
    ): JsonResponse {
        $result = $orderService->getOrder(
            $request->user(),
            $order
        );

        return response()->json([
            'data' => $result,
        ]);
    }
}
