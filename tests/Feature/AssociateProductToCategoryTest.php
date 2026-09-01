<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssociateProductToCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_admin_can_vinculate_product_to_category(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $response = $this->post("/api/v1/products/{$product->id}/categories/{$category->id}");

        $response->assertStatus(200);
    }

    public function test_authenticated_user_non_admin_cannot_vinculate_product_to_category(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user);

        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $response = $this->post("/api/v1/products/{$product->id}/categories/{$category->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_vinculate_product_to_category(): void
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();

        $response = $this->post("/api/v1/products/{$product->id}/categories/{$category->id}");

        $response->assertStatus(401);
    }

    public function test_inexistent_product_or_category_cannot_be_vinculated(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->post('/api/v1/products/999/categories/999');

        $response->assertStatus(404);
    }
}
