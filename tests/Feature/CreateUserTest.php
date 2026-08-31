<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user_must_return_201(): void
    {

        $userData = [
            'name' => 'Lucas Teste',
            'email' => 'lucasemail@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->post('/api/v1/auth/register', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'Lucas Teste',
            'email' => 'lucasemail@example.com',
        ]);
    }

    public function test_create_user_without_email_must_return_422(): void
    {

        $userData = [
            'name' => 'Lucas Teste',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->post('/api/v1/auth/register', $userData);

        $response->assertStatus(422);
    }
}
