<?php

namespace Database\Factories;

use App\Domain\Form\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'schema' => [],
            'active' => true,
        ];
    }
}
