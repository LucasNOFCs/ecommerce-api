<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    public function test_authenticated_user_can_create_order(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active'
        ]);

        $product = Product::factory()->create([
            'name' => 'Keyboard',
            'price' => 50,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Order created successfully.',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => '100.00',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Keyboard',
            'unit_price' => '50.00',
            'quantity' => 2,
            'subtotal' => '100.00',
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => 'completed',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_order(): void
    {
        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(401);
    }

    public function test_user_cannot_create_order_from_empty_cart(): void
    {
        $user = User::factory()->create();

        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_user_cannot_create_order_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 1,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_item_stores_product_snapshot(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create([
            'name' => 'Keyboard',
            'price' => 50,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(201);

        $product->update([
            'name' => 'Mechanical Keyboard',
            'price' => 80,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Keyboard',
            'unit_price' => '50.00',
            'quantity' => 2,
            'subtotal' => '100.00',
        ]);
    }

    public function test_order_total_is_calculated_from_order_items(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $productA = Product::factory()->create([
            'name' => 'Keyboard',
            'price' => 50,
            'stock' => 10,
        ]);

        $productB = Product::factory()->create([
            'name' => 'Mouse',
            'price' => 30,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $productA->id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $productB->id,
            'quantity' => 3,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => '190.00',
        ]);
    }

    public function test_cart_is_completed_after_order_creation(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(201);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => 'completed',
        ]);
    }

    public function test_order_creation_rolls_back_when_stock_validation_fails(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $productA = Product::factory()->create([
            'name' => 'Keyboard',
            'price' => 50,
            'stock' => 10,
        ]);

        $productB = Product::factory()->create([
            'name' => 'Mouse',
            'price' => 30,
            'stock' => 1,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $productA->id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $productB->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => 'active',
        ]);
    }

    public function test_user_cannot_access_another_users_order(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $userA->id,
            'status' => 'pending',
            'total' => 100,
        ]);

        Sanctum::actingAs($userB);

        $response = $this->getJson(
            "/api/v1/orders/{$order->id}"
        );

        $response->assertStatus(404);
    }

    public function test_order_starts_with_pending_status(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }
}
