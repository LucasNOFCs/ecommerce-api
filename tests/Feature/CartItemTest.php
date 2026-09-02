<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_item_to_cart(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'stock' => 10,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Item added to cart successfully.',
            'data' => null,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_unauthenticated_user_cannot_add_item_to_cart(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(401);
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'stock' => 10,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertStatus(201);

        $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertStatus(201);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_authenticated_user_can_get_cart_items(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/cart/items');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Cart items retrieved successfully.',
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_update_cart_item(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/v1/cart/items/{$cartItem->id}",
            [
                'quantity' => 5,
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_authenticated_user_cannot_edit_another_card_item(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $cartA = Cart::factory()->create([
            'user_id' => $userA->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cartA->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($userB);

        $response = $this->putJson(
            "/api/v1/cart/items/{$cartItem->id}",
            [
                'quantity' => 10,
            ]
        );

        $response->assertStatus(404);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'cart_id' => $cartA->id,
            'quantity' => 2,
        ]);
    }

    public function test_authenticated_user_can_delete_cart_item(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/cart/items/{$cartItem->id}"
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_authenticated_user_cannot_delete_another_card_item(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $cartA = Cart::factory()->create([
            'user_id' => $userA->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cartA->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($userB);

        $response = $this->deleteJson(
            "/api/v1/cart/items/{$cartItem->id}"
        );

        $response->assertStatus(404);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'cart_id' => $cartA->id,
            'quantity' => 2,
        ]);
    }
}
