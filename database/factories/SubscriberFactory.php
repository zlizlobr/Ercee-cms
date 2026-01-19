<?php

namespace Database\Factories;

use App\Domain\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
            'source' => 'test',
        ];
    }
}
