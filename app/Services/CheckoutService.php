<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function checkout(User $user): array
    {
        $cart = $this->cartService->getOrCreateActiveCart($user);

        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'The cart is empty.',
            ]);
        }

        $subtotal = 0;

        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product) {
                throw ValidationException::withMessages([
                    'cart' => 'One of the products is no longer available.',
                ]);
            }

            if ($item->quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'cart' => "Insufficient stock for product {$product->id}.",
                ]);
            }

            $subtotal += $product->price * $item->quantity;
        }

        return [
            'cart_id' => $cart->id,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'items' => $cart->items->count(),
        ];
    }
}
