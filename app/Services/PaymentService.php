<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function createPayment(
        User $user,
        Order $order,
        string $method
    ): Payment {
        if ($order->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'order' => 'Order not found.',
            ]);
        }

        if ($order->status === 'cancelled') {
            throw ValidationException::withMessages([
                'order' => 'Cancelled orders cannot receive payments.',
            ]);
        }

        if ($order->payment) {
            throw ValidationException::withMessages([
                'payment' => 'This order already has a payment.',
            ]);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total,
            'method' => $method,
            'status' => 'pending',
        ]);

        $result = $this->paymentGateway->process($payment);

        if (! $result['success']) {
            $payment->update([
                'status' => 'failed',
            ]);

            return $payment->refresh();
        }

        $payment->update([
            'transaction_id' => $result['transaction_id'],
        ]);

        return $payment->refresh();
    }

    public function getPayment(
        User $user,
        Order $order
    ): Payment {
        if ($order->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'order' => 'Order not found.',
            ]);
        }

        if (! $order->payment) {
            throw ValidationException::withMessages([
                'payment' => 'Payment not found',
            ]);
        }

        return $order->payment;
    }
}
