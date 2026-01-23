<?php

namespace Database\Factories;

use App\Domain\Commerce\Order;
use App\Domain\Commerce\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating payments.
 *
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Payment>
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway' => Payment::GATEWAY_STRIPE,
            'transaction_id' => 'pi_'.fake()->uuid(),
            'status' => Payment::STATUS_PENDING,
            'payload' => [],
        ];
    }
}
