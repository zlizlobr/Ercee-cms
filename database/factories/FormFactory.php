<?php

namespace Database\Factories;

use App\Domain\Form\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating forms.
 *
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Form>
     */
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'schema' => [],
            'active' => true,
        ];
    }
}
