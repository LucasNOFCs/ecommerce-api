<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_can_be_created(): void
    {
        $address = Address::factory()->create();

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'user_id' => $address->user_id,
        ]);
    }

    public function test_authenticated_user_can_create_address(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/addresses', [
            'label' => 'Home',
            'recipient_name' => 'Lucas Nobre',
            'street' => 'Main Street',
            'number' => '123',
            'complement' => 'Apt 10',
            'neighborhood' => 'Downtown',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'postal_code' => '60000-000',
            'country' => 'Brazil',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Address created successfully.',
        ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'label' => 'Home',
            'recipient_name' => 'Lucas Nobre',
            'street' => 'Main Street',
            'number' => '123',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'postal_code' => '60000-000',
            'is_default' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_address(): void
    {
        $response = $this->postJson('/api/v1/addresses', [
            'label' => 'Home',
            'recipient_name' => 'Lucas Nobre',
            'street' => 'Main Street',
            'number' => '123',
            'neighborhood' => 'Downtown',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'postal_code' => '60000-000',
            'country' => 'Brazil',
        ]);

        $response->assertStatus(401);
    }

    public function test_first_address_is_default_and_second_address_is_not_default(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/addresses', [
            'label' => 'Home',
            'recipient_name' => 'Lucas Nobre',
            'street' => 'Main Street',
            'number' => '123',
            'neighborhood' => 'Downtown',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'postal_code' => '60000-000',
            'country' => 'Brazil',
        ])->assertStatus(201);

        $this->postJson('/api/v1/addresses', [
            'label' => 'Work',
            'recipient_name' => 'Lucas Nobre',
            'street' => 'Second Street',
            'number' => '456',
            'neighborhood' => 'Centro',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'postal_code' => '60100-000',
            'country' => 'Brazil',
        ])->assertStatus(201);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'label' => 'Home',
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'label' => 'Work',
            'is_default' => false,
        ]);
    }
}
