<?php

namespace Database\Factories;

use App\Domain\Form\Contract;
use App\Domain\Form\Form;
use App\Domain\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

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
