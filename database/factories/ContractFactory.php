<?php

namespace Database\Factories;

use App\Domain\Form\Contract;
use App\Domain\Form\Form;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating contracts.
 *
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Contract>
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'form_id' => Form::factory(),
            'email' => fake()->safeEmail(),
            'data' => [],
            'source' => 'test',
            'status' => Contract::STATUS_NEW,
        ];
    }
}
