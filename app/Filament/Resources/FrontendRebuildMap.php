<?php

namespace App\Filament\Resources;

use App\Domain\Content\Menu;
use App\Domain\Content\Navigation;
use App\Domain\Content\Page;
use App\Domain\Content\ThemeSetting;
use App\Domain\Media\Media;
use Modules\Commerce\Domain\Attribute;
use Modules\Commerce\Domain\Product;
use Modules\Commerce\Domain\ProductReview;
use Modules\Commerce\Domain\Taxonomy;
use Modules\Commerce\Filament\Resources\AttributeResource;
use Modules\Commerce\Filament\Resources\ProductResource;
use Modules\Commerce\Filament\Resources\ProductReviewResource;
use Modules\Commerce\Filament\Resources\TaxonomyResource;
use Modules\Forms\Domain\Form;
use Modules\Forms\Filament\Resources\FormResource;

class FrontendRebuildMap
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function rules(): array
    {
        return [
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
                    'saved' => [
                        'reason' => 'navigation_updated',
                    ],
                    'deleted' => [
                        'reason' => 'navigation_deleted',
                    ],
                ],
            ],
            ThemeSettingResource::class => [
                'model' => ThemeSetting::class,
                'events' => [
                    'saved' => [
                        'reason' => 'theme_settings_updated',
                    ],
                    'deleted' => [
                        'reason' => 'theme_settings_deleted',
                    ],
                ],
            ],
            MenuResource::class => [
                'model' => Menu::class,
                'events' => [
                    'saved' => [
                        'reason' => 'menu_updated:{slug}',
                    ],
                    'deleted' => [
                        'reason' => 'menu_deleted:{slug}',
                    ],
                ],
            ],
            ProductResource::class => [
                'model' => Product::class,
                'events' => [
                    'saved' => [
                        'reason' => 'product_updated:{slug}',
                    ],
                    'deleted' => [
                        'reason' => 'product_deleted:{slug}',
                    ],
                ],
            ],
            FormResource::class => [
                'model' => Form::class,
                'events' => [
                    'saved' => [
                        'reason' => 'form_updated:{id}',
                    ],
                    'deleted' => [
                        'reason' => 'form_deleted:{id}',
                    ],
                ],
            ],
            TaxonomyResource::class => [
                'model' => Taxonomy::class,
                'events' => [
                    'saved' => [
                        'reason' => 'taxonomy_updated:{id}',
                    ],
                    'deleted' => [
                        'reason' => 'taxonomy_deleted:{id}',
                    ],
                ],
            ],
            AttributeResource::class => [
                'model' => Attribute::class,
                'events' => [
                    'saved' => [
                        'reason' => 'attribute_updated:{id}',
                    ],
                    'deleted' => [
                        'reason' => 'attribute_deleted:{id}',
                    ],
                ],
            ],
            ProductReviewResource::class => [
                'model' => ProductReview::class,
                'events' => [
                    'saved' => [
                        'reason' => 'product_review_updated:{id}',
                        'condition' => [
                            'method' => 'isApproved',
                            'equals' => true,
                        ],
                    ],
                    'deleted' => [
                        'reason' => 'product_review_deleted:{id}',
                    ],
                ],
            ],
            MediaResource::class => [
                'model' => Media::class,
                'events' => [
                    'saved' => [
                        'reason' => 'media_updated:{id}',
                    ],
                    'deleted' => [
                        'reason' => 'media_deleted:{id}',
                    ],
                ],
            ],
        ];
    }
}
