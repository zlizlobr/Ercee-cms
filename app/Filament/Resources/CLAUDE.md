## Filament Resources Rules

When building or modifying resource forms, use the project's standardized components:

**Links / CTAs** — always use `LinkPicker`:
```php
use App\Filament\Components\LinkPicker;
...LinkPicker::make('link')->withoutAnchor()->withTarget()->fields(),
```

**Icons** — always use `IconPicker`:
```php
use App\Filament\Components\IconPicker;
IconPicker::make()->field(),
```

**Media / Images** — always use `MediaPicker`:
```php
use App\Filament\Components\MediaPicker;
MediaPicker::make('image_media_uuid')->label(...)->columnSpanFull(),
```

Never use raw `TextInput` / `Select` for link fields, icon selects, or media UUID fields.
