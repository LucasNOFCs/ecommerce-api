<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetSelfTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_get_must_return_me(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/users/me');

        $response->assertStatus(200);
    }
}
