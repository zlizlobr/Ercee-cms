<?php

namespace App\Filament\Resources;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Content\ThemeSetting;
use App\Domain\Media\Media;
use App\Support\Module\ModuleManager;

class FrontendRebuildMap
{
    public static function rules(): array
    {
        $coreRules = [
            PageResource::class => [
                'model' => Page::class,
                'events' => [
                    'saved' => [
                        'reason' => 'page_updated:{slug}',
                        'condition' => [
                            'method' => 'isPublished',
                            'equals' => true,
                        ],
                    ],
                    'deleted' => [
                        'reason' => 'page_deleted:{slug}',
                    ],
                ],
            ],
            NavigationResource::class => [
                'model' => Navigation::class,
                'events' => [
                    'saved' => ['reason' => 'navigation_updated'],
                    'deleted' => ['reason' => 'navigation_deleted'],
                ],
            ],
            ThemeSettingResource::class => [
                'model' => ThemeSetting::class,
                'events' => [
                    'saved' => ['reason' => 'theme_settings_updated'],
                    'deleted' => ['reason' => 'theme_settings_deleted'],
                ],
            ],
            MenuResource::class => [
                'model' => Menu::class,
                'events' => [
                    'saved' => ['reason' => 'menu_updated:{slug}'],
                    'deleted' => ['reason' => 'menu_deleted:{slug}'],
                ],
            ],
            MediaResource::class => [
                'model' => Media::class,
                'events' => [
                    'saved' => ['reason' => 'media_updated:{id}'],
                    'deleted' => ['reason' => 'media_deleted:{id}'],
                ],
            ],
        ];

        $moduleRules = app(ModuleManager::class)->getModuleRebuildRules();

        return array_merge($coreRules, $moduleRules);
    }
}
