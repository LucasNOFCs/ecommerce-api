<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private bool $shouldSucceed = true
    ) {}

    public function process(Payment $payment): array
    {
        if (! $this->shouldSucceed) {
            return [
                'success' => false,
                'transaction_id' => null,
            ];
        }

        return [
            'success' => true,
            'transaction_id' => 'FAKE-'.uniqid(),
        ];
    }
}
