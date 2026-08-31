<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_must_returns_200(): void
    {

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/protected');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_must_return_401(): void
    {
        $response = $this->get('/api/v1/protected');

        $response->assertStatus(401);
    }
}
