<?php

namespace Database\Factories;

use App\Domain\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating subscribers.
 *
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Subscriber>
     */
    protected $model = Subscriber::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
            'source' => 'test',
        ];
    }
}
