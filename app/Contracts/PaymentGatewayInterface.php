<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function process(Payment $payment): array;
}
