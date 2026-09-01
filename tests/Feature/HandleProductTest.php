<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HandleProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_admin_can_edit_product(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/products/{$product->id}", [
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 99.99,
            'stock' => 50,
        ]);

        $response->assertStatus(200);
    }

    public function test_authenticated_user_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200);
    }

    public function test_authenticated_user_non_admin_cannot_edit_product(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/products/{$product->id}", [
            'name' => 'Updated Product Name',
            'description' => 'Updated Product Description',
            'price' => 99.99,
            'stock' => 50,
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_non_admin_cannot_delete_product(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(403);
    }
}
