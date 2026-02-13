<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Database\Seeders\Concerns\ReadsJsonSeedData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NavigationSeeder extends Seeder
{
    use ReadsJsonSeedData;

    public function run(): void
    {
        $payload = $this->readSeedJson('navigation.json');
        if (! is_array($payload)) {
            $this->warn('Skipping NavigationSeeder: invalid payload.');

            return;
        }

        $menuData = is_array($payload['menu'] ?? null) ? $payload['menu'] : [];
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];

        $menu = Menu::updateOrCreate(
            ['slug' => (string) ($menuData['slug'] ?? 'main')],
            ['name' => (string) ($menuData['name'] ?? 'Main')]
        );

        $position = 10;
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $slug = (string) ($item['slug'] ?? '');
            $title = (string) ($item['title'] ?? '');
            if ($slug === '' || $title === '') {
                continue;
            }

            $pageSlug = (string) ($item['page_slug'] ?? $slug);
            $page = Page::query()->where('slug', $pageSlug)->first();
            $url = $page ? null : (string) ($item['url'] ?? ($slug === 'home' ? '/' : '/'.$slug));

            Navigation::updateOrCreate(
                [
                    'menu_id' => $menu->id,
                    'slug' => Str::slug((string) ($item['nav_slug'] ?? $title)),
                ],
                [
                    'title' => $title,
                    'classes' => (string) ($item['classes'] ?? ''),
                    'url' => $url,
                    'target' => (string) ($item['target'] ?? '_self'),
                    'parent_id' => null,
                    'page_id' => $page?->id,
                    'position' => (int) ($item['position'] ?? $position),
                    'is_active' => (bool) ($item['is_active'] ?? true),
                ]
            );

            $position += 10;
        }
    }
}
