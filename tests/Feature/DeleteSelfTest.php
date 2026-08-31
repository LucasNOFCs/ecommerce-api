<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteSelfTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_delete_must_delete_authenticated_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->delete('/api/v1/users/me');

        $response->assertStatus(200);
    }
}
