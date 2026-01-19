<?php

namespace Database\Factories;

use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway' => Payment::GATEWAY_STRIPE,
            'transaction_id' => 'pi_' . fake()->uuid(),
            'status' => Payment::STATUS_PENDING,
            'payload' => [],
        ];
    }
}
