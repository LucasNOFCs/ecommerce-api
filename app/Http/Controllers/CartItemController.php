<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartItemService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function index(Request $request, CartItemService $cartItemService)
    {
        $user = $request->user();

        $cartItems = $cartItemService->getActiveCartItems($user);

        return response()->json([
            'message' => 'Cart items retrieved successfully.',
            'data' => $cartItems,
        ]);
    }

    public function store(StoreCartItemRequest $request, CartItemService $cartItemService)
    {
        $user = $request->user();

        $cartItem = $cartItemService->addItemToCart(
            $user,
            $request->input('product_id'),
            $request->input('quantity')
        );

        return response()->json([
            'message' => 'Item added to cart successfully.',
            'data' => $cartItem,
        ], 201);
    }

    public function update(UpdateCartItemRequest $request, CartItemService $cartItemService, int $id)
    {
        $cartItem = $cartItemService->updateCartItems(
            $request->user(),
            $id,
            $request->integer('quantity')
        );

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'data' => $cartItem,
        ]);
    }

    public function delete(Request $request, CartItemService $cartItemService, int $id)
    {
        $cartItemService->removeCartItem(
            $request->user(),
            $id
        );

        return response()->json([
            'message' => 'Cart item removed successfully.',
            'data' => null,
        ]);
    }
}
