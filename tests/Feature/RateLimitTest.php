<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', $credentials)
                ->assertStatus(200);
        }

        $response = $this->postJson(
            '/api/v1/auth/login',
            $credentials
        );

        $response->assertStatus(429);
    }

    public function test_authenticated_api_is_rate_limited(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/users/me')
                ->assertStatus(200);
        }

        $response = $this->getJson('/api/v1/users/me');

        $response->assertStatus(429);
    }

    public function test_authenticated_rate_limit_is_isolated_between_users(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Sanctum::actingAs($userA);

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/users/me')
                ->assertStatus(200);
        }

        $this->getJson('/api/v1/users/me')
            ->assertStatus(429);

        Sanctum::actingAs($userB);

        $this->getJson('/api/v1/users/me')
            ->assertStatus(200);
    }

    public function test_payment_endpoint_is_rate_limited(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $orders = Order::factory()
            ->count(11)
            ->create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

        foreach ($orders->take(10) as $order) {
            $this->postJson(
                "/api/v1/orders/{$order->id}/payment",
                [
                    'method' => 'pix',
                ]
            );
        }

        $response = $this->postJson(
            "/api/v1/orders/{$orders[10]->id}/payment",
            [
                'method' => 'pix',
            ]
        );

        $response->assertStatus(429);
    }

    public function test_checkout_is_rate_limited(): void
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $product = Product::factory()->create([
            'price' => 10,
            'stock' => 100,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Sanctum::actingAs($user);

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/checkout')
                ->assertStatus(200);
        }

        $response = $this->postJson('/api/v1/checkout');

        $response->assertStatus(429);
    }
}
