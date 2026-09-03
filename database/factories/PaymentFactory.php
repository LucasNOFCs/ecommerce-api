<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 10, 1000);

        return [
            'order_id' => Order::factory(),
            'amount' => $amount,
            'method' => fake()->randomElement([
                'pix',
                'credit_card',
                'debit_card',
                'boleto',
            ]),
            'status' => 'pending',
            'transaction_id' => null,
            'provider_event_id' => null,
        ];
    }
}
