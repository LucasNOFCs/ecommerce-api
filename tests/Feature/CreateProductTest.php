<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_admin_can_create_product(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'name' => 'Product 1',
            'description' => 'Description of Product 1',
            'price' => 9.99,
            'stock' => 100,
        ]);

        $response->assertStatus(201);
    }

    public function test_authenticated_user_non_admin_cannot_create_product(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'name' => 'Product 1',
            'description' => 'Description of Product 1',
            'price' => 9.99,
            'stock' => 100,
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $response = $this->postJson('/api/v1/products', [
            'name' => 'Product 1',
            'description' => 'Description of Product 1',
            'price' => 9.99,
            'stock' => 100,
        ]);

        $response->assertStatus(401);
    }
}
