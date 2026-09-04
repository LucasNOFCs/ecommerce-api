<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_wishlist(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/wishlist');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'items',
            ],
        ]);
    }

    public function test_unauthenticated_user_cannot_view_wishlist(): void
    {
        $response = $this->getJson('/api/v1/wishlist');

        $response->assertStatus(401);
    }

    public function test_wishlist_is_created_for_user_when_viewed(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/wishlist')
            ->assertStatus(200);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_add_product_to_wishlist(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/wishlist/items', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('wishlist_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_add_product_to_wishlist(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/wishlist/items', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_cannot_add_nonexistent_product_to_wishlist(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/wishlist/items', [
            'product_id' => 999999,
        ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_add_same_product_twice(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/wishlist/items', [
            'product_id' => $product->id,
        ])->assertStatus(201);

        $response = $this->postJson('/api/v1/wishlist/items', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(422);

        $wishlist = Wishlist::where('user_id', $user->id)->first();

        $this->assertDatabaseCount('wishlist_items', 1);

        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_remove_product_from_wishlist(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $wishlist = Wishlist::factory()->create([
            'user_id' => $user->id,
        ]);

        $item = WishlistItem::factory()->create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/wishlist/items/{$item->id}"
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('wishlist_items', [
            'id' => $item->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_remove_wishlist_item(): void
    {
        $item = WishlistItem::factory()->create();

        $response = $this->deleteJson(
            "/api/v1/wishlist/items/{$item->id}"
        );

        $response->assertStatus(401);
    }

    public function test_user_cannot_remove_another_users_wishlist_item(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $wishlist = Wishlist::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $item = WishlistItem::factory()->create([
            'wishlist_id' => $wishlist->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/wishlist/items/{$item->id}"
        );

        $response->assertStatus(404);

        $this->assertDatabaseHas('wishlist_items', [
            'id' => $item->id,
        ]);
    }

    public function test_user_cannot_remove_nonexistent_wishlist_item(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            '/api/v1/wishlist/items/999999'
        );

        $response->assertStatus(404);
    }

    public function test_user_only_sees_own_wishlist_items(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $productA = Product::factory()->create();
        $productB = Product::factory()->create();

        $wishlistA = Wishlist::factory()->create([
            'user_id' => $user->id,
        ]);

        $wishlistB = Wishlist::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        WishlistItem::factory()->create([
            'wishlist_id' => $wishlistA->id,
            'product_id' => $productA->id,
        ]);

        WishlistItem::factory()->create([
            'wishlist_id' => $wishlistB->id,
            'product_id' => $productB->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/wishlist');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data.items');

        $response->assertJsonPath(
            'data.items.0.product_id',
            $productA->id
        );
    }
}
