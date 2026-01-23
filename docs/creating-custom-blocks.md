# Creating Custom Blocks for Page Builder

This guide explains how to create custom content blocks for the Filament Page Builder.

## Overview

Each block consists of 4 parts:

1. **Block constant** in `Page` model
2. **Builder block definition** in `PageResource`
3. **Frontend Blade component** for rendering
4. **Localization strings** for labels

## Step-by-Step Guide

### 1. Add Block Type Constant

Add a new constant to `app/Domain/Content/Page.php`:

```php
public const BLOCK_TYPE_GALLERY = 'gallery';
```

Then add it to the `blockTypes()` method:

```php
public static function blockTypes(): array
{
    return [
        // ... existing blocks
        self::BLOCK_TYPE_GALLERY => __('admin.page.blocks.gallery'),
    ];
}
```

### 2. Define Builder Block Schema

Add the block definition in `app/Filament/Resources/PageResource.php` inside the `Builder::make('content')->blocks([...])` array:

```php
use Filament\Forms\Components\Builder;

// Gallery block
Builder\Block::make(Page::BLOCK_TYPE_GALLERY)
    ->label(__('admin.page.blocks.gallery'))
    ->icon('heroicon-o-squares-2x2')
    ->columns(2)
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
        Forms\Components\Select::make('columns')
            ->label(__('admin.page.fields.columns'))
            ->options([
                2 => '2 columns',
                3 => '3 columns',
                4 => '4 columns',
            ])
            ->default(3),
        Forms\Components\Toggle::make('lightbox')
            ->label(__('admin.page.fields.lightbox'))
            ->default(true),
    ]),
```

### 3. Create Frontend Blade Component

Create `resources/views/components/blocks/gallery.blade.php`:

```blade
@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $images = $data['images'] ?? [];
    $columns = $data['columns'] ?? 3;
    $lightbox = $data['lightbox'] ?? true;

    $gridCols = match((int) $columns) {
        2 => 'grid-cols-2',
        4 => 'grid-cols-4',
        default => 'grid-cols-3',
    };
@endphp

<section class="py-8">
    @if(!empty($data['title']))
        <h2 class="mb-6 text-2xl font-bold text-gray-900">{{ $data['title'] }}</h2>
    @endif

    @if(count($images) > 0)
        <div class="grid gap-4 {{ $gridCols }}">
            @foreach($images as $image)
                <div class="overflow-hidden rounded-lg">
                    @if($lightbox)
                        <a href="{{ asset('storage/' . $image) }}" data-lightbox="gallery">
                    @endif

                    <img
                        src="{{ asset('storage/' . $image) }}"
                        alt=""
                        class="h-48 w-full object-cover transition hover:scale-105"
                        loading="lazy"
                    >

                    @if($lightbox)
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
```

### 4. Add Localization Strings

Add translations to both language files:

**`lang/en/admin.php`:**

```php
'page' => [
    'blocks' => [
        // ... existing blocks
        'gallery' => 'Image Gallery',
    ],
    'fields' => [
        // ... existing fields
        'gallery_title' => 'Gallery Title',
        'gallery_images' => 'Images',
        'columns' => 'Columns',
        'lightbox' => 'Enable Lightbox',
    ],
],
```

**`lang/cs/admin.php`:**

```php
'page' => [
    'blocks' => [
        // ... existing blocks
        'gallery' => 'Galerie obrázků',
    ],
    'fields' => [
        // ... existing fields
        'gallery_title' => 'Název galerie',
        'gallery_images' => 'Obrázky',
        'columns' => 'Sloupce',
        'lightbox' => 'Povolit lightbox',
    ],
],
```

## Block Data Structure

All blocks are stored in the `pages.content` JSON column with this structure:

```json
[
    {
        "type": "gallery",
        "data": {
            "title": "Our Work",
            "images": ["path/to/image1.jpg", "path/to/image2.jpg"],
            "columns": 3,
            "lightbox": true
        }
    }
]
```

## Available Form Components

Common Filament form components for blocks:

| Component | Use Case |
|-----------|----------|
| `TextInput` | Short text, titles |
| `Textarea` | Multi-line text without formatting |
| `RichEditor` | Formatted HTML content |
| `FileUpload` | Images, files |
| `Select` | Dropdown options |
| `Toggle` | Boolean on/off |
| `ColorPicker` | Color selection |
| `Repeater` | Nested repeatable items |

## Available Icons

Use Heroicons for block icons. Browse at [heroicons.com](https://heroicons.com/).

Common icons:
- `heroicon-o-document-text` - Text content
- `heroicon-o-photo` - Images
- `heroicon-o-squares-2x2` - Grid/Gallery
- `heroicon-o-play` - Video
- `heroicon-o-code-bracket` - Code/Embed
- `heroicon-o-chat-bubble-left` - Testimonial/Quote
- `heroicon-o-map-pin` - Location/Map

## Validation

Add validation rules to form fields:

```php
Forms\Components\TextInput::make('title')
    ->required()           // Field is required
    ->maxLength(255)       // Maximum characters
    ->minLength(3)         // Minimum characters
    ->url()                // Must be valid URL
    ->email()              // Must be valid email
    ->numeric()            // Must be number
    ->rules(['regex:/^[a-z]+$/']) // Custom regex
```

## Best Practices

1. **Keep blocks focused** - Each block should do one thing well
2. **Use meaningful icons** - Help editors identify blocks quickly
3. **Provide sensible defaults** - Use `->default()` for optional fields
4. **Add helper text** - Use `->helperText()` to guide editors
5. **Group related fields** - Use `->columns()` and `->columnSpanFull()`
6. **Validate input** - Add appropriate validation rules
7. **Support both formats** - Always use `$block['data'] ?? $block` in Blade

## Example: Video Embed Block

Complete example of a video embed block:

```php
// Page.php
public const BLOCK_TYPE_VIDEO = 'video';

// PageResource.php
Builder\Block::make(Page::BLOCK_TYPE_VIDEO)
    ->label(__('admin.page.blocks.video'))
    ->icon('heroicon-o-play')
    ->schema([
        Forms\Components\TextInput::make('url')
            ->label(__('admin.page.fields.video_url'))
            ->url()
            ->required()
            ->helperText('YouTube or Vimeo URL'),
        Forms\Components\TextInput::make('title')
            ->label(__('admin.page.fields.video_title'))
            ->maxLength(255),
        Forms\Components\Select::make('aspect_ratio')
            ->label(__('admin.page.fields.aspect_ratio'))
            ->options([
                '16:9' => '16:9 (Widescreen)',
                '4:3' => '4:3 (Standard)',
                '1:1' => '1:1 (Square)',
            ])
            ->default('16:9'),
    ]),
```

```blade
{{-- resources/views/components/blocks/video.blade.php --}}
@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $url = $data['url'] ?? '';
    $aspectRatio = $data['aspect_ratio'] ?? '16:9';

    // Extract video ID and determine provider
    $videoId = null;
    $provider = null;

    if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $url, $matches);
        $videoId = $matches[1] ?? null;
        $provider = 'youtube';
    } elseif (str_contains($url, 'vimeo.com')) {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        $videoId = $matches[1] ?? null;
        $provider = 'vimeo';
    }

    $aspectClass = match($aspectRatio) {
        '4:3' => 'aspect-[4/3]',
        '1:1' => 'aspect-square',
        default => 'aspect-video',
    };
@endphp

<section class="py-8">
    @if(!empty($data['title']))
        <h2 class="mb-4 text-xl font-bold text-gray-900">{{ $data['title'] }}</h2>
    @endif

    @if($videoId && $provider)
        <div class="{{ $aspectClass }} w-full overflow-hidden rounded-lg">
            @if($provider === 'youtube')
                <iframe
                    src="https://www.youtube.com/embed/{{ $videoId }}"
                    class="h-full w-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            @elseif($provider === 'vimeo')
                <iframe
                    src="https://player.vimeo.com/video/{{ $videoId }}"
                    class="h-full w-full"
                    frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen
                ></iframe>
            @endif
        </div>
    @endif
</section>
```

## Related Documentation

- [Block Contract](./block-contract.md) - Data structure specification
- [Filament Builder Docs](https://filamentphp.com/docs/forms/fields/builder)
