<?php

namespace Database\Factories;

use Modules\Commerce\Domain\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating products.
 *
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Product>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(1000, 100000),
            'active' => true,
        ];
    }
}
