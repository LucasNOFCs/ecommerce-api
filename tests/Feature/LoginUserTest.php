<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_must_return_200(): void
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

        $response->assertStatus(200);
    }
}
