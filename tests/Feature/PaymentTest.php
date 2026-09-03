<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_payment(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        $response = $this->postJson(
            "/api/v1/orders/{$order->id}/payment",
            [
                'method' => 'pix',
            ]
        );

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_payment(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/orders/{$order->id}/payment",
            [
                'method' => 'pix',
            ]
        );

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Payment created successfully.',
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => '190.00',
            'method' => 'pix',
            'status' => 'pending',
        ]);
    }

    public function test_payment_amount_is_taken_from_order_total(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/orders/{$order->id}/payment",
            [
                'method' => 'pix',
                'amount' => 1,
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount' => '190.00',
        ]);
    }

    public function test_payment_rejects_invalid_method(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/orders/{$order->id}/payment",
            [
                'method' => 'crypto',
            ]
        );

        $response->assertStatus(422);
    }

    public function test_order_cannot_have_multiple_payments(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 190,
            'method' => 'pix',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/orders/{$order->id}/payment",
            [
                'method' => 'pix',
            ]
        );

        $response->assertStatus(422);

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_user_cannot_create_payment_for_another_users_order(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Sanctum::actingAs($attacker);

        $response = $this->postJson(
            "/api/v1/orders/{$order->id}/payment",
            [
                'method' => 'pix',
            ]
        );

        $response->assertStatus(422);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_authenticated_user_can_view_payment_for_owned_order(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 190,
            'method' => 'pix',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/v1/orders/{$order->id}/payment"
        );

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Payment retrieved successfully.',
        ]);
    }

    public function test_user_cannot_view_payment_for_another_users_order(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
        ]);

        Sanctum::actingAs($attacker);

        $response = $this->getJson(
            "/api/v1/orders/{$order->id}/payment"
        );

        $response->assertStatus(422);
    }

    public function test_payment_status_can_follow_valid_transition(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'pending',
        ]);

        $paymentStatusService = app(PaymentStatusService::class);

        $paymentStatusService->transition(
            $payment,
            'authorized'
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'authorized',
        ]);
    }

    public function test_payment_status_rejects_invalid_transition(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'paid',
        ]);

        $this->expectException(
            ValidationException::class
        );

        $paymentStatusService = app(PaymentStatusService::class);

        $paymentStatusService->transition(
            $payment,
            'pending'
        );
    }

    public function test_payment_can_reach_paid_status_through_valid_transitions(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'pending',
        ]);

        $paymentStatusService = app(PaymentStatusService::class);

        $paymentStatusService->transition(
            $payment,
            'authorized'
        );

        $paymentStatusService->transition(
            $payment,
            'paid'
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
    }
}
