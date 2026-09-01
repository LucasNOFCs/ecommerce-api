<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_admin_can_create_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(201);
    }

    public function test_authenticated_user_non_admin_cannot_create_category(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_create_category(): void
    {
        $response = $this->postJson('/api/v1/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(401);
    }

    public function test_create_category_validation(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/categories', [
            'name' => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_create_category_without_name(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/categories', [
            'names' => 'New Category',
        ]);

        $response->assertStatus(422);
    }
}
