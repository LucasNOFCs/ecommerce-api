<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_category(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_get_category(): void
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_category_by_id(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $product = Category::factory()->create();

        $response = $this->getJson('/api/v1/categories/1');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_get_category_by_id(): void
    {
        $response = $this->getJson('/api/v1/categories/1');

        $response->assertStatus(401);
    }
}
