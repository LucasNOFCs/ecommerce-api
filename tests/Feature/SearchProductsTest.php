<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SearchProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_products_by_name(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Product::factory()->create([
            'name' => 'Gaming Mouse',
        ]);

        Product::factory()->create([
            'name' => 'Mechanical Keyboard',
        ]);

        $response = $this->getJson('/api/v1/products?search=Mouse');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment([
            'name' => 'Gaming Mouse',
        ]);
    }

    public function test_search_returns_empty_when_product_does_not_match(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Product::factory()->create([
            'name' => 'Gaming Mouse',
        ]);

        $response = $this->getJson('/api/v1/products?search=Notebook');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');
    }
}
