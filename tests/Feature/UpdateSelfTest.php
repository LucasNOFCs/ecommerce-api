<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateSelfTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_put_must_return_updated_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->PUT('api/v1/users/me', [
            'name' => 'Lucas',
            'email' => 'lucastest@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Lucas')
            ->assertJsonPath('data.email', 'lucastest@example.com');
    }
}
