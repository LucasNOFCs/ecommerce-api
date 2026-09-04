<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Validation\ValidationException;

class WishlistService
{
    public function getOrCreate(User $user): Wishlist
    {
        return $user->wishlist()->firstOrCreate([]);
    }

    public function getWishlist(User $user): Wishlist
    {
        return $this->getOrCreate($user)
            ->load('items.product');
    }

    public function addItem(
        User $user,
        int $productId
    ): WishlistItem {
        $wishlist = $this->getOrCreate($user);

        $exists = $wishlist->items()
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'product_id' => [
                    'Product is already in the wishlist.',
                ],
            ]);
        }

        return $wishlist->items()->create([
            'product_id' => $productId,
        ]);
    }

    public function removeItem(
        User $user,
        int $itemId
    ): void {
        $wishlist = $this->getOrCreate($user);

        $item = $wishlist->items()
            ->findOrFail($itemId);

        $item->delete();
    }
}
