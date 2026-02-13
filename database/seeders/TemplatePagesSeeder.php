<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Page;
use Database\Seeders\Concerns\ReadsJsonSeedData;
use Illuminate\Database\Seeder;

class TemplatePagesSeeder extends Seeder
{
    use ReadsJsonSeedData;

    public function run(): void
    {
        $pages = $this->readSeedJson('template-pages.json');
        if (! is_array($pages)) {
            $this->warn('Skipping TemplatePagesSeeder: invalid payload.');

            return;
        }

        foreach ($pages as $pageData) {
            if (! is_array($pageData) || ! isset($pageData['slug'], $pageData['title'])) {
                continue;
            }

            Page::updateOrCreate(
                ['slug' => (string) $pageData['slug']],
                [
                    'title' => $pageData['title'],
                    'content' => is_array($pageData['content'] ?? null) ? $pageData['content'] : [],
                    'seo_meta' => is_array($pageData['seo_meta'] ?? null) ? $pageData['seo_meta'] : [],
                    'status' => (string) ($pageData['status'] ?? Page::STATUS_PUBLISHED),
                    'published_at' => now(),
                ],
            );
        }
    }
}
