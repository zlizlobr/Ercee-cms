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
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.page.fields.gallery_title'))
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->label(__('admin.page.fields.gallery_images'))
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('pages/galleries')
                    ->columnSpanFull(),
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

## Best Practices

1. Keep blocks focused and reusable.
2. Provide sensible defaults and helper text.
3. Validate inputs with `->required()` and `->maxLength()`.
4. Use `resources/views/components/blocks/` for preview templates.

## Related Documentation

- `docs/reference/content/block-contract.md` - Data structure and rendering pipeline
- https://filamentphp.com/docs/forms/fields/builder
