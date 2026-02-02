## Block Development Rules

Every block extends `BaseBlock` and implements `public static function make(): Block`.

### Required components for specific field types:

**Links / CTAs:**
```php
use App\Filament\Components\LinkPicker;

// Spread into schema — generates page_id, url, anchor fields
...LinkPicker::make('cta.link')->fields(),

// For primary/secondary pairs:
...LinkPicker::make('primary.link')->fields(),
...LinkPicker::make('secondary.link')->fields(),
```
Never use raw `TextInput` or `Select` for link-related fields (`page_id`, `url`, `anchor`).

**Icons:**
```php
use App\Filament\Components\IconPicker;

IconPicker::make()->field(),
```
Never hardcode icon option arrays. `IconPicker::iconOptions()` is the single source of truth.

**Media / Images:**
```php
use App\Filament\Components\MediaPicker;

MediaPicker::make('image_media_uuid')
    ->label(__('admin.page.fields.image_media_uuid'))
    ->columnSpanFull(),
```
Never use `TextInput` for `*_media_uuid` fields.

### Translation keys

- Block labels: `__('admin.page.blocks.{block_type}')`
- Field labels: `__('admin.page.fields.{field_name}')`
