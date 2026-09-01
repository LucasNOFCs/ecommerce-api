<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_products(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(
            [
                'role' => 'user',
            ]
        );

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_single_product(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(
            [
                'role' => 'user',
            ]
        );

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/products/{$product->id}");
        $response->assertStatus(200);
    }

    public function test_authenticated_user_cannot_view_nonexistent_product(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(
            [
                'role' => 'user',
            ]
        );

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/products/999');
        $response->assertStatus(404);
    }
}
