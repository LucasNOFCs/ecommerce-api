<?php

namespace App\Services;

use App\Models\User;

class CartItemService
{
    public function __construct(private CartService $cartService) {}

    public function getActiveCartItems(User $user)
    {
        $cart = $this->cartService->getOrCreateActiveCart($user);

        return $cart->items;
    }

    public function addItemToCart(User $user, int $productId, int $quantity)
    {
        $cart = $this->cartService->getOrCreateActiveCart($user);

        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();

            return $cartItem;
        } else {

            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);

            return $cartItem;
        }
    }

    public function updateCartItems(User $user, int $cartItemId, int $quantity)
    {
        $cart = $this->cartService->getOrCreateActiveCart($user);

        $cartItem = $cart->items()
            ->where('id', $cartItemId)
            ->firstOrFail();

        $cartItem->update([
            'quantity' => $quantity,
        ]);

        return $cartItem;
    }

    public function removeCartItem(
        User $user,
        int $cartItemId
    ) {
        $cart = $this->cartService->getOrCreateActiveCart($user);

        $cartItem = $cart->items()
            ->where('id', $cartItemId)
            ->firstOrFail();

        $cartItem->delete();
    }
}
