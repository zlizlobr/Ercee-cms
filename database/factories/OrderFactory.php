<?php

namespace Database\Factories;

use Modules\Commerce\Domain\Order;
use Modules\Commerce\Domain\Product;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating orders.
 *
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Order>
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'product_id' => Product::factory(),
            'email' => fake()->safeEmail(),
            'price' => fake()->numberBetween(1000, 100000),
            'status' => Order::STATUS_PENDING,
        ];
    }
}
