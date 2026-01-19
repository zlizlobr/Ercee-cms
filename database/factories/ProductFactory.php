<?php

namespace Database\Factories;

use App\Domain\Commerce\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(1000, 100000),
            'active' => true,
        ];
    }
}
