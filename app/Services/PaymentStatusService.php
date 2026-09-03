<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Validation\ValidationException;

class PaymentStatusService
{
    private array $transitions = [
        'pending' => [
            'authorized',
            'failed',
            'cancelled',
        ],
        'authorized' => [
            'paid',
            'failed',
        ],

        'paid' => [],
        'failed' => [],
        'cancelled' => [],
    ];

    public function transition(
        Payment $payment,
        string $newStatus
    ): Payment {
        $currentStatus = $payment->status;

        if (! isset($this->transitions[$currentStatus])) {
            throw ValidationException::withMessages([
                'status' => 'Current payment status is invalid.',
            ]);
        }

        if (! in_array(
            $newStatus,
            $this->transitions[$currentStatus],
            true
        )) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition payment from {$currentStatus} to {$newStatus}",
            ]);
        }

        $payment->update([
            'status' => $newStatus,
        ]);

        return $payment->refresh();
    }
}
