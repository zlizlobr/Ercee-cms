<?php

namespace Database\Factories;

use App\Domain\Content\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

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
