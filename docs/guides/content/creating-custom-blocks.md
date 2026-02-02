# Creating Custom Blocks for Page Builder

This guide explains how to add a new CMS block and wire it to the Astro frontend and the admin preview.

## Overview

Each block spans:

1. **CMS constant** in `app/Domain/Content/Page.php`
2. **Block class** in `app/Filament/Blocks/`
3. **Admin preview component** in `resources/views/components/blocks/`
4. **Astro type + mapping + component** in the `ercee-frontend` repo
5. **Localization strings** in `lang/*/admin.php`

## Step-by-Step

### 1. Add Block Type Constant

```php
public const BLOCK_TYPE_GALLERY = 'gallery';
```

Add it to `Page::blockTypes()` so labels show up in Filament.

### 2. Create Block Class

Create `app/Filament/Blocks/GalleryBlock.php`:

```php
<?php

namespace App\Filament\Blocks;

use App\Domain\Content\Page;
use App\Filament\Components\LinkPicker;
use App\Filament\Components\MediaPicker;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class GalleryBlock extends BaseBlock
{
    public static int $order = 60;

    public static function make(): Block
    {
        return Block::make(Page::BLOCK_TYPE_GALLERY)
            ->label(__('admin.page.blocks.gallery'))
            ->icon('heroicon-o-squares-2x2')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.title'))
                    ->maxLength(200)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('items')
                    ->label(__('admin.page.fields.items'))
                    ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('admin.page.fields.title'))
                        ->maxLength(160)
                        ->columnSpanFull(),
                    MediaPicker::make('image_media_uuid')
                        ->label(__('admin.page.fields.image_media_uuid'))
                        ->columnSpanFull(),
                    ])
                    ->defaultItems(3)
                    ->minItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                Forms\Components\TextInput::make('cta.label')
                    ->label(__('admin.page.fields.cta.label'))
                    ->maxLength(80),
                ...LinkPicker::make('cta.link')->fields(),
            ]);
    }
}
```

Clear the block cache:

```bash
php artisan blocks:clear
```

### 3. Add Admin Preview Blade Component

Create `resources/views/components/blocks/gallery.blade.php` (used by preview).

### 4. Add Localization Strings

Update `lang/en/admin.php` and `lang/cs/admin.php` with labels for the block and fields.

### 5. Wire the Astro Frontend

In the **Astro** repo:

1. Add the data shape in `src/lib/api/types.ts`.
2. Map raw CMS data in `src/lib/api/endpoints/pages.ts`.
3. Create a component in `src/components/blocks/`.
4. Register it in `src/components/BlockRenderer.astro`.

## Block Data Structure

All blocks are stored in `pages.content`:

```json
[
  {
    "type": "gallery",
    "data": {
      "title": "Our Work",
      "images": ["path/to/image1.jpg", "path/to/image2.jpg"]
    }
  }
]
```

## Standardized UI Components

When building blocks, always use the project's standardized components instead of raw Filament fields:

### LinkPicker (links / CTAs)

Use `App\Filament\Components\LinkPicker` for any link or CTA field. It generates `page_id`, `url`, and `anchor` fields automatically.

```php
use App\Filament\Components\LinkPicker;

// Simple CTA link (page_id + url + anchor)
Forms\Components\TextInput::make('cta.label')
    ->label(__('admin.page.fields.cta.label'))
    ->maxLength(80),
...LinkPicker::make('cta.link')->fields(),

// Primary + secondary CTA pair
Forms\Components\TextInput::make('primary.label')
    ->label(__('admin.page.fields.primary.label'))
    ->maxLength(80),
...LinkPicker::make('primary.link')->fields(),
Forms\Components\TextInput::make('secondary.label')
    ->label(__('admin.page.fields.secondary.label'))
    ->maxLength(80),
...LinkPicker::make('secondary.link')->fields(),

// Inside a repeater
...LinkPicker::make('link')->fields(),

// Without anchor field
...LinkPicker::make('link')->withoutAnchor()->fields(),

// With target (_self/_blank)
...LinkPicker::make()->withoutAnchor()->withTarget()->fields(),
```

### IconPicker (icons)

Use `App\Filament\Components\IconPicker` for icon selection fields. It provides a searchable Select with centralized icon options.

```php
use App\Filament\Components\IconPicker;

IconPicker::make()->field(),
// or with custom field name:
IconPicker::make('custom_icon')->field(),
```

### MediaPicker (images / media)

Use `App\Filament\Components\MediaPicker` for any image or media UUID field. Never use raw `TextInput` for media UUIDs.

```php
use App\Filament\Components\MediaPicker;

MediaPicker::make('background_media_uuid')
    ->label(__('admin.page.fields.background_image'))
    ->columnSpanFull(),

MediaPicker::make('image_media_uuid')
    ->label(__('admin.page.fields.image_media_uuid'))
    ->columnSpanFull(),
```

## Best Practices

1. Keep blocks focused and reusable.
2. Provide sensible defaults and helper text.
3. Validate inputs with `->required()` and `->maxLength()`.
4. Use `resources/views/components/blocks/` for preview templates.
5. **Always** use `LinkPicker` for links, `IconPicker` for icons, `MediaPicker` for media/images.
6. Never use raw `TextInput` for `*_media_uuid` fields, `*_page_id` / `*_url` link fields, or `icon_key` fields.

## Related Documentation

- `docs/reference/content/block-contract.md` - Data structure and rendering pipeline
- https://filamentphp.com/docs/forms/fields/builder
