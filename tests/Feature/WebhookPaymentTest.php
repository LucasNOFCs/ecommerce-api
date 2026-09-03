<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookPaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_payment_webhook_updates_payment_status(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 190,
            'status' => 'authorized',
            'transaction_id' => 'TX-123',
        ]);

        $response = $this->postJson('/api/v1/payments/webhook', [
            'event_id' => 'EVT-001',
            'transaction_id' => 'TX-123',
            'status' => 'paid',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
            'provider_event_id' => 'EVT-001',
        ]);
    }

    public function test_duplicate_webhook_is_idempotent(): void
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
            'status' => 'authorized',
            'transaction_id' => 'TX-123',
        ]);

        $payload = [
            'event_id' => 'EVT-001',
            'transaction_id' => 'TX-123',
            'status' => 'paid',
        ];

        $this->postJson(
            '/api/v1/payments/webhook',
            $payload
        )->assertStatus(200);

        $this->postJson(
            '/api/v1/payments/webhook',
            $payload
        )->assertStatus(200);

        $this->assertDatabaseCount('payments', 1);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TX-123',
            'provider_event_id' => 'EVT-001',
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_invalid_webhook_event_is_rejected(): void
    {
        $response = $this->postJson('/api/v1/payments/webhook', [
            'event_id' => null,
            'transaction_id' => 'TX-123',
            'status' => 'paid',
        ]);

        $response->assertStatus(422);
    }

    public function test_webhook_rejects_missing_transaction_id(): void
    {
        $response = $this->postJson('/api/v1/payments/webhook', [
            'event_id' => 'EVT-001',
            'status' => 'paid',
        ]);

        $response->assertStatus(422);
    }

    public function test_webhook_cannot_move_payment_to_invalid_previous_state(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'total' => 190,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 190,
            'status' => 'paid',
            'transaction_id' => 'TX-123',
        ]);

        $response = $this->postJson('/api/v1/payments/webhook', [
            'event_id' => 'EVT-002',
            'transaction_id' => 'TX-123',
            'status' => 'pending',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
    }

    public function test_failed_payment_does_not_confirm_order(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 190,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 190,
            'status' => 'pending',
            'transaction_id' => 'TX-123',
        ]);

        $response = $this->postJson('/api/v1/payments/webhook', [
            'event_id' => 'EVT-003',
            'transaction_id' => 'TX-123',
            'status' => 'failed',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'failed',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }
}
