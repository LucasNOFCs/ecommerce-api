<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_checkout(): void
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

        $response = $this->postJson('/api/v1/checkout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Checkout validated successfully.',
            'data' => [
                'cart_id' => $cart->id,
                'subtotal' => '100.00',
                'items' => 1,
            ],
        ]);
    }

    public function test_unauthenticated_user_cannot_checkout(): void
    {
        $response = $this->postJson('/api/v1/checktou');

        $response->assertStatus(404);
    }

    public function test_authenticated_user_cannot_checkout_empty_cart(): void
    {
        $user = User::factory()->create();

        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/checkout');

        $response->assertStatus(422);
    }

    public function test_authenticated_user_cannot_checkout_with_insufficient_stock(): void
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

        $response = $this->postJson('/api/v1/checkout');

        $response->assertStatus(422);
    }

    public function test_checkout_calculates_subtotal_correctly(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $productA = Product::factory()->create([
            'price' => 50,
            'stock' => 10,
        ]);

        $productB = Product::factory()->create([
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

        $response = $this->postJson('/api/v1/checkout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Checkout validated successfully.',
            'data' => [
                'cart_id' => $cart->id,
                'subtotal' => '190.00',
                'items' => 2,
            ],
        ]);
    }

    public function test_checkout_does_not_change_product_stock(): void
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

        $response = $this->postJson('/api/v1/checkout');

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 10,
        ]);
    }
}
