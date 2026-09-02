<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;

class CartService
{
    public function getOrCreateActiveCart(User $user): Cart
    {
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        return $cart;
    }
}
