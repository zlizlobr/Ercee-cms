<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Page;
use Database\Seeders\Concerns\ReadsJsonSeedData;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    use ReadsJsonSeedData;

    public function run(): void
    {
        $pages = $this->readSeedJson('template-pages.json');
        if (! is_array($pages)) {
            $this->warn('Skipping HomePageSeeder: invalid payload.');

            return;
        }

        $home = collect($pages)->first(fn ($page): bool => is_array($page) && (($page['slug'] ?? null) === 'home'));
        if (! is_array($home)) {
            $this->warn('Skipping HomePageSeeder: page with slug "home" not found in template-pages.json.');

            return;
        }

        Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => $home['title'] ?? 'Domovska stranka',
                'seo_meta' => is_array($home['seo_meta'] ?? null) ? $home['seo_meta'] : [],
                'content' => is_array($home['content'] ?? null) ? $home['content'] : [],
                'status' => (string) ($home['status'] ?? Page::STATUS_PUBLISHED),
                'published_at' => now(),
            ],
        );
    }
}
