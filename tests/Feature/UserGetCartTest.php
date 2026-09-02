<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserGetCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_authenticated_can_get_their_cart(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/cart');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Active cart retrieved successfully.',
        ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }

    public function test_user_unauthenticated_cannot_get_cart(): void
    {
        $response = $this->getJson('/api/v1/cart');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_reuses_existing_active_cart(): void
    {
        $user = User::factory()->create();

        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/cart');

        $response->assertStatus(200);

        $this->assertDatabaseCount('carts', 1);
    }
}
