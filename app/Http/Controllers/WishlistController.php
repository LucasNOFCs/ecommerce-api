<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistItemRequest;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function show(
        Request $request,
        WishlistService $wishlistService
    ): JsonResponse {
        $wishlist = $wishlistService->getWishlist(
            $request->user()
        );

        return response()->json([
            'data' => $wishlist,
        ]);
    }

    public function store(
        StoreWishlistItemRequest $request,
        WishlistService $wishlistService
    ): JsonResponse {
        $item = $wishlistService->addItem(
            $request->user(),
            $request->validated('product_id')
        );

        return response()->json([
            'message' => 'Product added to wishlist successfully.',
            'data' => $item->load('product'),
        ], 201);
    }

    public function destroy(
        Request $request,
        int $item,
        WishlistService $wishlistService
    ): JsonResponse {
        $wishlistService->removeItem(
            $request->user(),
            $item
        );

        return response()->json([
            'message' => 'Wishlist item removed successfully.',
        ]);
    }
}
