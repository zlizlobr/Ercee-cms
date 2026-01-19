<?php

namespace Database\Factories;

use App\Domain\Commerce\Order;
use App\Domain\Commerce\Product;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

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
