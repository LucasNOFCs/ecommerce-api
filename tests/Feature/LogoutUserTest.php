<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_logout(): void
    {
        $dummyData = [
            'name' => 'Lucas Teste',
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $this->post('/api/v1/auth/register', $dummyData);

        $userData = [
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
        ];

        $response = $this->post('/api/v1/auth/login', $userData);
        $token = $response->json('data.token');

        $this->withToken($token)->post('/api/v1/auth/logout');

        $response->assertStatus(200);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logout successful',
            'data' => null,
        ]);
    }

    public function test_revoked_token_is_not_in_database(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth-token')->plainTextToken;

        $this
            ->withToken($token)
            ->getJson('/api/v1/protected')
            ->assertStatus(200);

        $this
            ->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);

        Auth::forgetGuards();

        $this
            ->withToken($token)
            ->getJson('/api/v1/protected')
            ->assertStatus(401);
    }
}
