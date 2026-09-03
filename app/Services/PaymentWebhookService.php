<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentWebhookService
{
    public function __construct(
        private PaymentStatusService $paymentStatusService
    ) {}

    public function handle(array $payload): void
    {
        $eventId = $payload['event_id'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (! $eventId || ! $transactionId || ! $status) {
            throw ValidationException::withMessages([
                'webhook' => 'Invalid webhook payload.',
            ]);
        }

        $payment = Payment::where(
            'transaction_id',
            $transactionId
        )->firstOrFail();

        if ($payment->provider_event_id === $eventId) {
            return;
        }

        DB::transaction(function () use (
            $payment,
            $eventId,
            $status
        ) {
            $payment->update([
                'provider_event_id' => $eventId,
            ]);

            $this->paymentStatusService->transition(
                $payment,
                $status
            );

            if ($status === 'paid') {
                $payment->order->update([
                    'status' => 'confirmed',
                ]);
            }
        });
    }
}
