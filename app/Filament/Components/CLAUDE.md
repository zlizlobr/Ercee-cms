## Filament Components

This directory contains reusable form field components for the Filament admin panel.

### LinkPicker
Generates link fields (`page_id`, `url`, `anchor`) as an array of Filament form components.
- `LinkPicker::make('prefix.link')->fields()` — returns array, use spread `...` in schema
- Options: `->withoutAnchor()`, `->withTarget()`, `->withLinkType()`, `->isOverride()`, `->defaultUrl()`
- Translations: `admin.link_picker.*`

### IconPicker
Centralized icon selection with a searchable Select field.
- `IconPicker::make()->field()` — returns single `Forms\Components\Select`
- `IconPicker::iconOptions()` — canonical list of available icons (single source of truth)
- Translations: `admin.page.fields.icon`, `admin.page.fields.icon_placeholder`

### MediaPicker
Custom Filament Field for selecting media from the media library.
- `MediaPicker::make('field_name')` — returns a Filament Field (not an array)
- Supports image types by default, 10MB max
- Has upload, select from library, and clear actions
