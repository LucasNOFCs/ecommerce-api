<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_authentication_must_been_has_admin_role(): void
    {
        $userData = [
            'name' => 'Lucas Teste',
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $this->post('/api/v1/auth/register', $userData);

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
        ]);

        $this->get('/api/v1/test/admin', [
            'Authorization' => 'Bearer '.$response->json('data.token'),
        ])->assertStatus(403);
    }

    public function test_user_authentication_must_been_has_any_role(): void
    {
        $userData = [
            'name' => 'Lucas Teste',
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $this->post('/api/v1/auth/register', $userData);

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
        ]);

        $this->get('/api/v1/test/anyrole', [
            'Authorization' => 'Bearer '.$response->json('data.token'),
        ])->assertStatus(200);
    }

    public function test_user_unnauthenticated_must_return_401(): void
    {
        $this->get('/api/v1/test/anyrole')->assertStatus(401);
        $this->get('/api/v1/test/admin')->assertStatus(401);
    }

    public function test_user_admin_must_return_200(): void
    {

        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $this->get('/api/v1/test/admin', [
            'Authorization' => 'Bearer '.$user->createToken('admin')->plainTextToken,
        ])->assertStatus(200);
    }
}
