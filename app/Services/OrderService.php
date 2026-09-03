<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function createOrder(User $user): Order
    {
        $cart = $this->cartService->getOrCreateActiveCart($user);

        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Cart is empty.'
            ]);
        }

        return DB::transaction(function () use ($cart) {
            $total = 0;

            $order = Order::create([
                'user_id' => $cart->user_id,
                'status' => 'pending',
                'total' => 0
            ]);

            foreach($cart->items as $item) {
                $product = $item->product;

                if (!$product) {
                    throw ValidationException::withMessages([
                        'cart' => 'One of the products is no longer available.'
                    ]);
                }
                
                if ($item->quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for product {$product->name}."
                    ]);
                }

                $subtotal = $product->price * $item->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            $order->update([
                'total' => $total
            ]);

            $cart->update([
                'status' => 'completed'
            ]);

            return $order->load('items');
        });

        
    }
}
