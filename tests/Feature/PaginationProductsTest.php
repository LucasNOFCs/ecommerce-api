<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaginationProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_paginated_products(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $products = Product::factory()->count(100)->create();

        $response = $this->getJson('/api/v1/products?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
    }
}
