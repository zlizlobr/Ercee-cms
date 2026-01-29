# Upgrade Guide: Monolith → Modular Architecture

## Prehled

Tento dokument popisuje migraci z monoliticke architektury (vse v `app/`) na modularni architekturu s oddelenym core a moduly (`modules/`).

## Zmeny v namespace

### Forms modul

| Puvodni namespace | Novy namespace |
|---|---|
| `App\Domain\Form\*` | `Modules\Forms\Domain\*` |
| `App\Application\Form\*` | `Modules\Forms\Application\*` |
| `App\Filament\Resources\FormResource` | `Modules\Forms\Filament\Resources\FormResource` |
| `App\Filament\Resources\ContractResource` | `Modules\Forms\Filament\Resources\ContractResource` |
| `App\Filament\Blocks\FormBlock` | `Modules\Forms\Filament\Blocks\FormBlock` |

### Commerce modul

| Puvodni namespace | Novy namespace |
|---|---|
| `App\Domain\Commerce\*` | `Modules\Commerce\Domain\*` |
| `App\Application\Commerce\*` | `Modules\Commerce\Application\*` |
| `App\Filament\Resources\ProductResource` | `Modules\Commerce\Filament\Resources\ProductResource` |
| `App\Filament\Resources\OrderResource` | `Modules\Commerce\Filament\Resources\OrderResource` |
| `App\Filament\Blocks\ProductBlock` | `Modules\Commerce\Filament\Blocks\ProductBlock` |

### Funnel modul

| Puvodni namespace | Novy namespace |
|---|---|
| `App\Domain\Funnel\*` | `Modules\Funnel\Domain\*` |
| `App\Application\Funnel\*` | `Modules\Funnel\Application\*` |
| `App\Filament\Resources\FunnelResource` | `Modules\Funnel\Filament\Resources\FunnelResource` |

## Backward-compatible aliasy

Backward-compatible alias tridy byly **odstraneny**. Nasledujici adresare jiz neexistuji:

- `app/Domain/Form/`, `app/Domain/Commerce/`, `app/Domain/Funnel/`
- `app/Application/Form/`, `app/Application/Commerce/`, `app/Application/Funnel/`
- `app/Filament/Resources/FormResource`, `app/Filament/Resources/ContractResource`, `app/Filament/Resources/ProductResource`, `app/Filament/Resources/OrderResource`, `app/Filament/Resources/PaymentResource`, `app/Filament/Resources/AttributeResource`, `app/Filament/Resources/TaxonomyResource`, `app/Filament/Resources/ProductReviewResource`, `app/Filament/Resources/FunnelResource`, `app/Filament/Resources/FunnelRunResource`
- `app/Filament/Blocks/FormEmbedBlock`, `app/Filament/Blocks/ContactFormBlock`

Vsechny reference musi pouzivat `Modules\*` namespace.

## Zmeny v event systemu

### Core eventy (nove)

Tyto eventy jsou nyni dispatchovany automaticky z observeru:

- `App\Domain\Content\Events\ContentPublished` — pri publikaci stranky
- `App\Domain\Content\Events\MenuUpdated` — pri ulozeni menu
- `App\Domain\Content\Events\MediaUploaded` — pri nahrani media

### Modulove eventy

- `Modules\Forms\Domain\Events\ContractCreated` — pri vytvoreni contractu
- `Modules\Commerce\Domain\Events\OrderPaid` — pri zaplaceni objednavky

### Listenery

Listenery se nyni registruji v `getEventListeners()` service provideru modulu, ne v `AppServiceProvider`. Pokud mate vlastni listenery registrovane v `EventServiceProvider` nebo `AppServiceProvider`, presunte je do prislusneho modulu.

## Zmeny v Composer

### Lokalni vyvoj

Moduly jsou nyni definovany jako path repositories v root `composer.json`:

```json
"repositories": [
    { "type": "path", "url": "modules/forms", "options": { "symlink": true } }
]
```

### Autoload

PSR-4 autoload pro moduly je definovany v root `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Modules\\Forms\\": "modules/forms/src/",
        "Modules\\Commerce\\": "modules/commerce/src/",
        "Modules\\Funnel\\": "modules/funnel/src/"
    }
}
```

Po jakychkoliv zmenach v autoloadu spustte:

```bash
composer dump-autoload
```

## Zmeny v konfiguraci

### config/modules.php

Kazdy modul musi byt zaregistrovany v `config/modules.php`:

```php
'forms' => [
    'enabled' => true,
    'provider' => \Modules\Forms\FormsModuleServiceProvider::class,
    'version' => '1.0.0',
    'dependencies' => [],
],
```

### Permissions

Modulove permissions jsou prefixovane `module.<name>.<permission>`. Po pridani noveho modulu spustte seeder:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

## Migrace databaze

Modulove migrace jsou v `modules/<name>/database/migrations/`. Pri migraci:

```bash
php artisan migrate
```

Laravel automaticky nacte migrace ze vsech registrovanych modulu.

## Checklist pro upgrade

1. [x] Aktualizovat `composer.json` — pridat path repositories a autoload
2. [x] Spustit `composer dump-autoload`
3. [x] Pridat moduly do `config/modules.php`
4. [x] Spustit migrace: `php artisan migrate`
5. [x] Spustit seeder: `php artisan db:seed --class=RolesAndPermissionsSeeder`
6. [x] Aktualizovat vlastni kod pouzivajici stare namespace
7. [ ] Vymazat cache: `php artisan cache:clear && php artisan config:clear`
8. [ ] Overit funkcnost v admin panelu
9. [ ] Spustit testy: `composer test`
