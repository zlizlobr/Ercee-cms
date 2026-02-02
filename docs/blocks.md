# CMS Blocks (Builder)

Tento dokument sjednocuje informace k CMS blokum, jejich datovemu kontraktu,
renderingu a generovani pres Artisan prikaz.

## Overview

CMS bloky jsou spravovany ve Filament Builderu. Kazdy blok ma `type` a `data`.
API vraci bloky v teto podobe a frontend/preview si je rendruje podle typu.

### Datova struktura bloku

```json
[
  {
    "type": "block_type_name",
    "data": {
      "field_a": "value",
      "field_b": 123
    }
  }
]
```

## Rendering pipeline

### Public frontend (Astro)

1. `Page::getBlocks()` vraci builder bloky pres API.
2. Astro zpracuje bloky (pokud je potreba mapovani, resi se v `src/lib/api/endpoints/pages.ts`).
3. Renderuje se pres `src/components/BlockRenderer.astro` a registry mapu.

### Admin preview (Blade)

Preview bezi v Blade sablonach:

- `resources/views/filament/pages/preview.blade.php`
- `resources/views/filament/products/preview.blade.php`

Komponenty bloku jsou v:

- `resources/views/components/blocks/`
- fallback: `resources/views/frontend/blocks/`

## Vytvoreni noveho bloku (doporučeno)

Pouzijte Artisan prikaz `make:cms-block`, ktery vygeneruje vse potrebne.

### Zakladni pouziti

```bash
# Inline JSON schema
php artisan make:cms-block "Block Name" '{"label": "...", "fields": [...]}'

# JSON schema ze souboru
php artisan make:cms-block "Block Name" --schema-file=path/to/schema.json

# Dry run - nahled zmen bez zapisu
php artisan make:cms-block "Block Name" '...' --dry-run

# Prepsat existujici soubory
php artisan make:cms-block "Block Name" '...' --force
```

### Normalizace nazvu

- `Hero Banner` -> `hero_banner` (block type)
- `Hero Banner` -> `HeroBannerBlock` (PHP class)
- `Hero Banner` -> `HeroBanner` (Astro komponenta)

### Schema

Minimalni schema:

```json
{
  "label": "Gallery",
  "fields": [
    { "type": "text", "name": "title", "label": "Title", "maxLength": 255 }
  ]
}
```

Volitelne klice:

```json
{
  "label": "Feature Grid",
  "label_cs": "Mrizka funkci",
  "icon": "heroicon-o-squares-2x2",
  "order": 50,
  "fields": [...]
}
```

### Standardizovane UI komponenty

Projekt pouziva tri vlastni komponenty pro konzistentni tvorbu bloku.
**Vzdy je pouzivejte misto surovych Filament poli.**

#### LinkPicker (`App\Filament\Components\LinkPicker`)

Pro vsechna pole s odkazem / CTA. Generuje `page_id` (Select), `url` (TextInput) a `anchor` (TextInput).

```php
use App\Filament\Components\LinkPicker;

// CTA odkaz
...LinkPicker::make('cta.link')->fields(),

// Bez kotvy
...LinkPicker::make('link')->withoutAnchor()->fields(),

// S target (_self/_blank) - pro navigaci
...LinkPicker::make()->withoutAnchor()->withTarget()->fields(),
```

#### IconPicker (`App\Filament\Components\IconPicker`)

Pro vyber ikony. Centralizovany seznam ikon v `IconPicker::iconOptions()`.

```php
use App\Filament\Components\IconPicker;

IconPicker::make()->field(),
```

#### MediaPicker (`App\Filament\Components\MediaPicker`)

Pro vsechny obrazky a media. Nikdy nepouzivejte `TextInput` pro `*_media_uuid`.

```php
use App\Filament\Components\MediaPicker;

MediaPicker::make('image_media_uuid')
    ->label(__('admin.page.fields.image_media_uuid'))
    ->columnSpanFull(),
```

### Podporovane typy poli (make:cms-block)

Kryti odpovida `app/Console/Commands/MakeCmsBlock.php`.

- `text` (TextInput)
- `textarea` (Textarea)
- `richtext` (RichEditor)
- `markdown` (MarkdownEditor)
- `select` (Select)
- `checkbox` (Checkbox)
- `toggle` (Toggle)
- `checkbox_list` (CheckboxList)
- `radio` (Radio)
- `datetime` (DateTimePicker)
- `file` (FileUpload)
- `repeater` (Repeater)
- `builder` (Builder, zakladni podpora)
- `tags` (TagsInput)
- `key_value` (KeyValue)
- `color` (ColorPicker)
- `toggle_buttons` (ToggleButtons)
- `hidden` (Hidden)

Pokud potrebujete pokrocile nastaveni mimo podporovane atributy, upravte
vygenerovanou block class rucne.

### Spolecne atributy

```json
{
  "name": "field_name",
  "label": "Field Label",
  "label_cs": "Czech Label",
  "required": true,
  "default": "default value",
  "disabled": false,
  "hidden": false,
  "helperText": "Help text below field",
  "hint": "Hint text on the right",
  "hintIcon": "heroicon-o-information-circle",
  "columnSpanFull": true,
  "columnSpan": 1
}
```

### Vybrane typy poli

#### TextInput (`text`)

```json
{
  "type": "text",
  "name": "title",
  "label": "Title",
  "required": true,
  "maxLength": 255,
  "minLength": 3,
  "placeholder": "Enter title...",
  "email": true,
  "url": true,
  "tel": true,
  "numeric": true,
  "password": true,
  "minValue": 0,
  "maxValue": 100,
  "step": 0.01,
  "prefix": "$",
  "suffix": "kg",
  "prefixIcon": "heroicon-o-currency-dollar",
  "suffixIcon": "heroicon-o-globe-alt",
  "mask": "99/99/9999",
  "autocomplete": "email",
  "datalist": ["Option 1", "Option 2"]
}
```

#### Textarea (`textarea`)

```json
{
  "type": "textarea",
  "name": "description",
  "label": "Description",
  "rows": 4,
  "cols": 50,
  "minLength": 10,
  "maxLength": 1000,
  "placeholder": "Enter description...",
  "autosize": true
}
```

#### Select (`select`)

```json
{
  "type": "select",
  "name": "style",
  "label": "Style",
  "options": {
    "primary": "Primary",
    "secondary": "Secondary",
    "outline": "Outline"
  },
  "multiple": true,
  "searchable": true,
  "preload": true,
  "native": false,
  "optionsLimit": 50,
  "placeholder": "Select style..."
}
```

#### Radio / CheckboxList / ToggleButtons

```json
{
  "type": "radio",
  "name": "alignment",
  "label": "Alignment",
  "options": {"left": "Left", "center": "Center", "right": "Right"},
  "inline": true,
  "columns": 3
}
```

#### FileUpload (`file`)

```json
{
  "type": "file",
  "name": "image",
  "label": "Image",
  "image": true,
  "multiple": true,
  "directory": "blocks/gallery",
  "disk": "public",
  "visibility": "public",
  "acceptedFileTypes": ["image/jpeg", "image/png", "image/webp"],
  "minSize": 10,
  "maxSize": 5120,
  "maxFiles": 10,
  "imageEditor": true,
  "imageCropAspectRatio": "16:9",
  "imageResizeTargetWidth": 1920,
  "imageResizeTargetHeight": 1080,
  "reorderable": true,
  "downloadable": true,
  "openable": true,
  "previewable": true
}
```

#### DateTimePicker (`datetime`)

```json
{
  "type": "datetime",
  "name": "published_at",
  "label": "Published At",
  "format": "Y-m-d H:i:s",
  "displayFormat": "d.m.Y H:i",
  "native": true,
  "seconds": true,
  "minDate": "2024-01-01",
  "maxDate": "2025-12-31",
  "timezone": "Europe/Prague"
}
```

#### Repeater (`repeater`)

```json
{
  "type": "repeater",
  "name": "features",
  "label": "Features",
  "schema": [
    {"type": "text", "name": "title", "label": "Title", "required": true},
    {"type": "textarea", "name": "description", "label": "Description"},
    {"type": "file", "name": "icon", "label": "Icon", "image": true}
  ],
  "columns": 2,
  "defaultItems": 1,
  "minItems": 1,
  "maxItems": 10,
  "collapsible": true,
  "collapsed": false,
  "cloneable": true,
  "reorderable": true,
  "addable": true,
  "deletable": true,
  "grid": 2,
  "itemLabel": "title",
  "simple": false
}
```

### Priklady

#### Hero blok

```json
{
  "label": "Hero Section",
  "label_cs": "Hero sekce",
  "icon": "heroicon-o-star",
  "order": 10,
  "fields": [
    {
      "type": "text",
      "name": "heading",
      "label": "Heading",
      "label_cs": "Nadpis",
      "required": true,
      "maxLength": 255,
      "columnSpanFull": true
    },
    {
      "type": "textarea",
      "name": "subheading",
      "label": "Subheading",
      "label_cs": "Podnadpis",
      "rows": 2,
      "maxLength": 500,
      "columnSpanFull": true
    }
  ]
}
```

#### Pricing table

```json
{
  "label": "Pricing Table",
  "label_cs": "Cenik",
  "icon": "heroicon-o-currency-dollar",
  "order": 40,
  "fields": [
    {
      "type": "text",
      "name": "title",
      "label": "Title",
      "columnSpanFull": true
    },
    {
      "type": "repeater",
      "name": "plans",
      "label": "Plans",
      "collapsible": true,
      "cloneable": true,
      "itemLabel": "name",
      "grid": 3,
      "schema": [
        {"type": "text", "name": "name", "label": "Plan Name", "required": true},
        {"type": "text", "name": "price", "label": "Price", "numeric": true, "prefix": "$"},
        {"type": "select", "name": "billing", "label": "Billing", "options": {"monthly": "Monthly", "yearly": "Yearly"}},
        {"type": "textarea", "name": "description", "label": "Description", "rows": 2},
        {"type": "tags", "name": "features", "label": "Features"},
        {"type": "toggle", "name": "featured", "label": "Featured", "default": false}
      ]
    }
  ]
}
```

## Co prikaz vygeneruje

### CMS (Laravel)

- `app/Filament/Blocks/{Name}Block.php`
- `resources/views/components/blocks/{name}.blade.php`
- `app/Domain/Content/Page.php` (konstanta + `blockTypes()`)
- `lang/en/admin.php`
- `lang/cs/admin.php`

### Astro frontend

- `../ercee-frontend/src/components/blocks/{Name}.astro`
- `../ercee-frontend/src/lib/api/types.ts`
- `../ercee-frontend/src/components/blocks/registry.ts`

## Po vygenerovani

```bash
php artisan blocks:clear
cd ../ercee-frontend && pnpm lint
```

## Customizace sablon

Sablony lze upravit v:

- `resources/stubs/blocks/block-class.stub`
- `resources/stubs/blocks/block-preview.stub`
- `resources/stubs/astro/BlockComponent.astro.stub`

## Troubleshooting

### Blok uz existuje

Pouzijte `--force`:

```bash
php artisan make:cms-block "Gallery" '...' --force
```

### Astro frontend nenalezen

Prikaz ocekava frontend v `../ercee-frontend`.

### Neplatny JSON

Otestujte schema pres dry-run:

```bash
php artisan make:cms-block "Test" '{"label": "Test", "fields": []}' --dry-run
```
