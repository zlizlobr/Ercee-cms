<?php

namespace Database\Factories;

use App\Domain\Content\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating pages.
 *
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * The model associated with the factory.
     *
     * @var class-string<Page>
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'content' => ['blocks' => []],
            'seo_meta' => [],
            'status' => Page::STATUS_DRAFT,
            'published_at' => null,
        ];
    }
}
