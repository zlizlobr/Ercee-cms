<?php

namespace Database\Seeders;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::updateOrCreate(
            ['slug' => 'main'],
            ['name' => 'Main']
        );

        $items = [
            ['slug' => 'home', 'title' => 'Domu'],
            ['slug' => 'capabilities', 'title' => 'Sluzby'],
            ['slug' => 'documentation', 'title' => 'Dokumentace'],
            ['slug' => 'facilities', 'title' => 'Zazemi'],
            ['slug' => 'rfq', 'title' => 'Poptavka'],
            ['slug' => 'use-cases', 'title' => 'Use cases'],
        ];

        $position = 10;
        foreach ($items as $item) {
            $page = Page::query()->where('slug', $item['slug'])->first();
            $url = $page ? null : ($item['slug'] === 'home' ? '/' : '/'.$item['slug']);

            Navigation::updateOrCreate(
                [
                    'menu_id' => $menu->id,
                    'slug' => Str::slug($item['title']),
                ],
                [
                    'title' => $item['title'],
                    'classes' => '',
                    'url' => $url,
                    'target' => '_self',
                    'parent_id' => null,
                    'page_id' => $page?->id,
                    'position' => $position,
                    'is_active' => true,
                ]
            );

            $position += 10;
        }
    }
}
