# Make CMS Block Command Guide

Artisan příkaz `make:cms-block` generuje kompletní CMS blok včetně Filament třídy, Astro komponenty a všech souvisejících souborů.

Dokumentace Filament forms: https://filamentphp.com/docs/5.x/forms/overview

## Základní použití

```bash
# S inline JSON schématem
php artisan make:cms-block "Block Name" '{"label": "...", "fields": [...]}'

# S JSON souborem
php artisan make:cms-block "Block Name" --schema-file=path/to/schema.json

# Dry run - náhled změn bez zápisu
php artisan make:cms-block "Block Name" '...' --dry-run

# Přepsat existující soubory
php artisan make:cms-block "Block Name" '...' --force
```

## JSON Schema

### Struktura schématu

```json
{
  "label": "Feature Grid",
  "label_cs": "Mřížka funkcí",
  "icon": "heroicon-o-squares-2x2",
  "order": 50,
  "fields": [...]
}
```

### Povinné klíče

| Klíč | Popis |
|------|-------|
| `label` | Název bloku v angličtině |
| `fields` | Pole definic polí |

### Volitelné klíče

| Klíč | Výchozí | Popis |
|------|---------|-------|
| `label_cs` | hodnota `label` | Český název bloku |
| `icon` | `heroicon-o-cube` | Heroicon ikona |
| `order` | `100` | Pořadí v UI (nižší = dříve) |

---

## Podporované typy polí

### TextInput (`text`)

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `email` | bool | Validace emailu |
| `url` | bool | Validace URL |
| `tel` | bool | Telefonní číslo |
| `numeric` | bool | Pouze čísla |
| `password` | bool | Skryté heslo |
| `minLength` | int | Min. délka |
| `maxLength` | int | Max. délka |
| `minValue` | number | Min. hodnota (numeric) |
| `maxValue` | number | Max. hodnota (numeric) |
| `step` | number | Krok (numeric) |
| `prefix` | string | Text před polem |
| `suffix` | string | Text za polem |
| `prefixIcon` | string | Ikona před polem |
| `suffixIcon` | string | Ikona za polem |
| `placeholder` | string | Placeholder |
| `mask` | string | Vstupní maska |
| `autocomplete` | string | HTML autocomplete |
| `datalist` | array | Návrhy hodnot |

### Textarea (`textarea`)

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `rows` | int | Počet řádků |
| `cols` | int | Počet sloupců |
| `minLength` | int | Min. délka |
| `maxLength` | int | Max. délka |
| `placeholder` | string | Placeholder |
| `autosize` | bool | Automatická výška |

### RichEditor (`richtext`)

```json
{"type": "richtext", "name": "content", "label": "Content"}
```

### MarkdownEditor (`markdown`)

```json
{"type": "markdown", "name": "body", "label": "Body"}
```

---

### Select (`select`)

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `options` | object | Možnosti `{value: label}` |
| `multiple` | bool | Vícenásobný výběr |
| `searchable` | bool | Vyhledávání v možnostech |
| `preload` | bool | Přednačtení dat |
| `native` | bool | HTML select (false = JS select) |
| `optionsLimit` | int | Max. zobrazených možností |
| `placeholder` | string | Placeholder |

### Radio (`radio`)

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

### CheckboxList (`checkbox_list`)

```json
{
  "type": "checkbox_list",
  "name": "categories",
  "label": "Categories",
  "options": {"cat1": "Category 1", "cat2": "Category 2"},
  "columns": 2
}
```

### ToggleButtons (`toggle_buttons`)

```json
{
  "type": "toggle_buttons",
  "name": "size",
  "label": "Size",
  "options": {"sm": "Small", "md": "Medium", "lg": "Large"},
  "inline": true
}
```

| Atribut | Typ | Popis |
|---------|-----|-------|
| `options` | object | Možnosti `{value: label}` |
| `inline` | bool | Horizontální zobrazení |
| `columns` | int | Počet sloupců |

### Checkbox (`checkbox`)

```json
{"type": "checkbox", "name": "featured", "label": "Featured", "default": true}
```

### Toggle (`toggle`)

```json
{"type": "toggle", "name": "active", "label": "Active", "default": false}
```

---

### FileUpload (`file`)

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `image` | bool | Pouze obrázky |
| `multiple` | bool | Více souborů |
| `directory` | string | Cílová složka |
| `disk` | string | Storage disk |
| `visibility` | string | `public` / `private` |
| `acceptedFileTypes` | array | Povolené MIME typy |
| `minSize` | int | Min. velikost (KB) |
| `maxSize` | int | Max. velikost (KB) |
| `maxFiles` | int | Max. počet souborů |
| `imageEditor` | bool | Editor obrázků |
| `imageCropAspectRatio` | string | Poměr ořezu (`16:9`) |
| `imageResizeTargetWidth` | int | Cílová šířka |
| `imageResizeTargetHeight` | int | Cílová výška |
| `reorderable` | bool | Řazení souborů |
| `downloadable` | bool | Tlačítko stažení |
| `openable` | bool | Tlačítko otevření |
| `previewable` | bool | Náhled souborů |

---

### DateTimePicker (`datetime`)

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `format` | string | Formát pro uložení |
| `displayFormat` | string | Formát pro zobrazení |
| `native` | bool | HTML5 date picker |
| `seconds` | bool | Zobrazit sekundy |
| `minDate` | string | Min. datum |
| `maxDate` | string | Max. datum |
| `timezone` | string | Časová zóna |

### ColorPicker (`color`)

```json
{
  "type": "color",
  "name": "background_color",
  "label": "Background Color",
  "format": "hex"
}
```

| Atribut | Typ | Popis |
|---------|-----|-------|
| `format` | string | `hex`, `rgb`, `rgba`, `hsl` |

### TagsInput (`tags`)

```json
{
  "type": "tags",
  "name": "keywords",
  "label": "Keywords",
  "separator": ",",
  "suggestions": ["tag1", "tag2", "tag3"]
}
```

| Atribut | Typ | Popis |
|---------|-----|-------|
| `separator` | string | Oddělovač tagů |
| `suggestions` | array | Návrhy tagů |

### KeyValue (`key_value`)

```json
{"type": "key_value", "name": "metadata", "label": "Metadata"}
```

### Hidden (`hidden`)

```json
{"type": "hidden", "name": "version", "label": "Version", "default": "1.0"}
```

---

### Repeater (`repeater`)

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `schema` | array | Vnořená pole (povinné) |
| `columns` | int | Počet sloupců uvnitř |
| `defaultItems` | int | Výchozí počet položek |
| `minItems` | int | Min. počet položek |
| `maxItems` | int | Max. počet položek |
| `collapsible` | bool | Možnost sbalit/rozbalit |
| `collapsed` | bool | Výchozí stav sbalen |
| `cloneable` | bool | Možnost klonování |
| `reorderable` | bool | Možnost řazení (default true) |
| `addable` | bool | Možnost přidání (default true) |
| `deletable` | bool | Možnost smazání (default true) |
| `grid` | int | Mřížka pro položky |
| `itemLabel` | string | Pole pro label položky |
| `simple` | bool | Jednoduchý repeater |

---

## Společné atributy

Tyto atributy fungují u všech typů polí:

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

| Atribut | Typ | Popis |
|---------|-----|-------|
| `name` | string | Název pole (povinné) |
| `label` | string | Popisek EN |
| `label_cs` | string | Popisek CZ |
| `required` | bool | Povinné pole |
| `default` | mixed | Výchozí hodnota |
| `disabled` | bool | Zakázané pole |
| `hidden` | bool | Skryté pole |
| `helperText` | string | Pomocný text pod polem |
| `hint` | string | Nápověda vpravo |
| `hintIcon` | string | Ikona nápovědy |
| `columnSpanFull` | bool | Přes celou šířku |
| `columnSpan` | int | Šířka v grid sloupcích |

---

## Příklady

### Hero blok

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
    },
    {
      "type": "file",
      "name": "background_image",
      "label": "Background Image",
      "label_cs": "Obrázek pozadí",
      "image": true,
      "imageEditor": true,
      "imageCropAspectRatio": "16:9",
      "maxSize": 5120,
      "columnSpanFull": true
    },
    {
      "type": "text",
      "name": "button_text",
      "label": "Button Text",
      "label_cs": "Text tlačítka",
      "maxLength": 100
    },
    {
      "type": "text",
      "name": "button_url",
      "label": "Button URL",
      "label_cs": "URL tlačítka",
      "url": true
    }
  ]
}
```

### Pricing table

```json
{
  "label": "Pricing Table",
  "label_cs": "Ceník",
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

### FAQ s accordion

```json
{
  "label": "FAQ",
  "label_cs": "Často kladené dotazy",
  "icon": "heroicon-o-question-mark-circle",
  "order": 70,
  "fields": [
    {
      "type": "text",
      "name": "title",
      "label": "Title",
      "columnSpanFull": true
    },
    {
      "type": "repeater",
      "name": "items",
      "label": "Questions",
      "collapsible": true,
      "collapsed": true,
      "itemLabel": "question",
      "minItems": 1,
      "schema": [
        {"type": "text", "name": "question", "label": "Question", "required": true, "columnSpanFull": true},
        {"type": "richtext", "name": "answer", "label": "Answer", "columnSpanFull": true}
      ]
    }
  ]
}
```

### Galerie s editorem

```json
{
  "label": "Gallery",
  "label_cs": "Galerie",
  "icon": "heroicon-o-photo",
  "order": 60,
  "fields": [
    {"type": "text", "name": "title", "label": "Title"},
    {"type": "textarea", "name": "description", "label": "Description"},
    {
      "type": "file",
      "name": "images",
      "label": "Images",
      "multiple": true,
      "image": true,
      "maxFiles": 20,
      "maxSize": 5120,
      "imageEditor": true,
      "reorderable": true,
      "downloadable": true,
      "columnSpanFull": true
    }
  ]
}
```

---

## Generované soubory

### CMS (Laravel)

| Soubor | Popis |
|--------|-------|
| `app/Filament/Blocks/{Name}Block.php` | Filament block třída |
| `resources/views/components/blocks/{name}.blade.php` | Blade preview šablona |
| `app/Domain/Content/Page.php` | Konstanta + záznam v `blockTypes()` |
| `lang/en/admin.php` | Anglické překlady |
| `lang/cs/admin.php` | České překlady |

### Astro Frontend

| Soubor | Popis |
|--------|-------|
| `../ercee-frontend/src/components/blocks/{Name}.astro` | Astro komponenta |
| `../ercee-frontend/src/lib/api/types.ts` | TypeScript interface |
| `../ercee-frontend/src/components/blocks/registry.ts` | Block registry |

---

## Po vygenerování

```bash
# 1. Vyčistit cache bloků
php artisan blocks:clear

# 2. Zkontrolovat vygenerované soubory

# 3. Spustit lint ve frontendu
cd ../ercee-frontend && pnpm lint
```

## Customizace šablon

Šablony lze upravit v:

- `resources/stubs/blocks/block-class.stub` - PHP třída
- `resources/stubs/blocks/block-preview.stub` - Blade preview
- `resources/stubs/astro/BlockComponent.astro.stub` - Astro komponenta

## Řešení problémů

### Blok již existuje

Použijte `--force` pro přepsání existujících souborů:

```bash
php artisan make:cms-block "Gallery" '...' --force
```

### Astro frontend nenalezen

Příkaz očekává frontend v `../ercee-frontend`. Ujistěte se, že adresář existuje.

### Neplatný JSON

Ověřte JSON pomocí dry-run:

```bash
php artisan make:cms-block "Test" '{"label": "Test", "fields": []}' --dry-run
```
