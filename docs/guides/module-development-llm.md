# Ercee CMS Module System — LLM Reference

This document is a structured reference for LLM agents working with the Ercee CMS module system. Follow these rules exactly when creating, modifying, or debugging modules.

## Critical Facts

- **Framework:** Laravel 12, Filament 3.2, PHP 8.2+
- **Module location:** `/usr/local/var/www/ercee-modules/ercee-module-{name}/`
- **Symlink target:** `modules/{name}/` in the CMS root (managed by Composer path repository)
- **Namespace:** `Modules\{StudlyName}\` (e.g. `Modules\Commerce\`, `Modules\Forms\`)
- **Package name:** `ercee/module-{name}` (e.g. `ercee/module-commerce`)
- **Config key:** `module.{name}` (merged from `config/module.php` in the module)
- **Permissions prefix:** `module.{name}.{permission}` (e.g. `module.commerce.view_products`)

## File Layout

When generating a module, use exactly this structure:

```
ercee-module-{name}/
├── composer.json
├── config/
│   └── module.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
└── src/
    ├── {StudlyName}ModuleServiceProvider.php
    ├── Domain/           # Eloquent models, Events, Contracts, Services
    ├── Application/      # CQRS: Commands/, Handlers/, Results/
    ├── Filament/
    │   ├── Resources/    # Filament Resources + Pages/ + RelationManagers/
    │   └── Blocks/       # Page builder blocks
    ├── Http/
    │   ├── Controllers/  # API and web controllers
    │   └── Requests/     # Form Request validation
    ├── Listeners/        # Event listeners
    └── Observers/        # Eloquent model observers
```

## Service Provider Template

Every module MUST have exactly one service provider extending `BaseModuleServiceProvider`. Use this template:

```php
<?php

declare(strict_types=1);

namespace Modules\{StudlyName};

use App\Support\Module\BaseModuleServiceProvider;

class {StudlyName}ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = '{name}';
    protected string $version = '1.0.0';
    protected string $description = '{description}';
    protected array $dependencies = [];
    protected array $permissions = [];

    protected function registerBindings(): void
    {
        // Bind contracts to implementations
    }

    public function boot(): void
    {
        parent::boot();
        // Register observers here
    }

    public function getResources(): array
    {
        return [];
    }

    public function getBlocks(): array
    {
        return [];
    }

    public function getEventListeners(): array
    {
        return [];
    }

    public function getRebuildRules(): array
    {
        return [];
    }

    protected function getModulePath(string $path = ''): string
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }
}
```

### Available Override Methods

| Method | Return | Purpose |
|---|---|---|
| `registerBindings()` | `void` | DI container bindings (called in `register()`) |
| `boot()` | `void` | Observer registration, always call `parent::boot()` first |
| `getResources()` | `array` | Filament Resource FQCN list |
| `getPages()` | `array` | Filament Page FQCN list |
| `getWidgets()` | `array` | Filament Widget FQCN list |
| `getBlocks()` | `array` | Page builder block FQCN list |
| `getNavigationItems()` | `array` | Admin sidebar navigation items |
| `getEventListeners()` | `array` | `[EventClass => ListenerClass]` or `[EventClass => [Listener1, Listener2]]` |
| `getEventSubscribers()` | `array` | Event subscriber FQCN list |
| `getPolicies()` | `array` | `[ModelClass => PolicyClass]` |
| `getRebuildRules()` | `array` | Frontend rebuild trigger rules (see below) |

## Route Rules

### API routes (`routes/api.php`)

ModuleManager auto-wraps with `Route::middleware('api')->prefix('api')->group(...)`.

DO:
```php
Route::prefix('v1')->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->middleware('throttle:api-read');
});
```

DO NOT:
```php
// WRONG - do not add api prefix or middleware, it's already applied
Route::middleware('api')->prefix('api/v1')->group(function () { ... });
```

Result: `GET /api/v1/posts`

### Web routes (`routes/web.php`)

ModuleManager auto-wraps with `Route::middleware('web')->group(...)`.

DO:
```php
Route::middleware('redirect.frontend')->group(function () {
    Route::get('/blog', [BlogController::class, 'index'])->name('frontend.blog');
});
```

DO NOT:
```php
// WRONG - do not add web middleware, it's already applied
Route::middleware('web')->group(function () { ... });
```

### Route naming convention

- Frontend routes: `frontend.{entity}`, `frontend.{entity}.show`
- Admin routes: `admin.{entity}.preview`, `admin.{entity}.action`
- API routes: no name required (use prefix grouping)

## Frontend Rebuild Rules

When a model change in admin should trigger a frontend rebuild, register rules in the provider AND create an Observer.

### Rule format

```php
public function getRebuildRules(): array
{
    return [
        ResourceClass::class => [
            'model' => ModelClass::class,
            'events' => [
                'saved' => [
                    'reason' => 'entity_updated:{slug}',        // {slug}, {id} are model attribute placeholders
                    'condition' => [                              // optional
                        'method' => 'isPublished',               // model method name
                        'equals' => true,                        // expected return value
                    ],
                ],
                'deleted' => [
                    'reason' => 'entity_deleted:{slug}',
                ],
            ],
        ],
    ];
}
```

### Observer template

```php
<?php

namespace Modules\{StudlyName}\Observers;

use Modules\{StudlyName}\Domain\{Model};
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class {Model}Observer
{
    public function saved({Model} $model): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($model, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted({Model} $model): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($model, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}
```

Register in provider `boot()`:

```php
public function boot(): void
{
    parent::boot();
    {Model}::observe({Model}Observer::class);
}
```

## Dependencies

Declare in both the provider AND `config/modules.php`:

```php
// Provider
protected array $dependencies = [
    'forms' => '^1.0',
];

// config/modules.php
'my_module' => [
    'enabled' => true,
    'provider' => \Modules\MyModule\MyModuleModuleServiceProvider::class,
    'version' => '1.0.0',
    'dependencies' => [
        'forms' => '^1.0',
    ],
],
```

Supported constraints: `^1.0` (caret), `~1.2` (tilde), `>=1.0`, `>1.0`, `<=2.0`, `<2.0`, `*` (any).

## Cross-Module Communication

Modules MUST communicate through Laravel Events only. Never import classes from another module's `Application/` or `Http/` layers.

Allowed cross-module imports:
- `Domain\Events\*` — event classes
- `Domain\{Model}` — Eloquent models (read-only usage)
- `Domain\Contracts\*` — interfaces

Forbidden cross-module imports:
- `Application\*` — commands, handlers
- `Http\*` — controllers, requests
- `Filament\*` — admin resources
- `Observers\*` — observers

### Event listener registration

```php
public function getEventListeners(): array
{
    return [
        \Modules\Forms\Domain\Events\ContractCreated::class =>
            \Modules\Funnel\Listeners\StartFunnelOnContractCreated::class,
        \Modules\Commerce\Domain\Events\OrderPaid::class =>
            \Modules\Funnel\Listeners\StartFunnelOnOrderPaid::class,
    ];
}
```

## composer.json Template

```json
{
    "name": "ercee/module-{name}",
    "description": "Ercee CMS {StudlyName} module",
    "type": "library",
    "version": "1.0.0",
    "license": "proprietary",
    "require": {
        "php": "^8.2",
        "filament/filament": "^3.2",
        "laravel/framework": "^12.0"
    },
    "autoload": {
        "psr-4": {
            "Modules\\{StudlyName}\\": "src/"
        }
    },
    "extra": {
        "ercee": {
            "name": "{name}",
            "provider": "Modules\\{StudlyName}\\{StudlyName}ModuleServiceProvider"
        },
        "laravel": {
            "providers": [
                "Modules\\{StudlyName}\\{StudlyName}ModuleServiceProvider"
            ]
        }
    }
}
```

## Registration Checklist

When creating a new module, ALL of these steps must be completed:

1. Create the module directory and files in `../ercee-modules/ercee-module-{name}/`
2. Add `"ercee/module-{name}": "@dev"` to main `composer.json` `require`
3. Run `composer update ercee/module-{name}`
4. Add entry to `config/modules.php` `modules` array
5. Run `php artisan module:list` to verify

When modifying an existing module:
- Never add module-specific code to core `app/` — it belongs in the module
- Never import module classes in `AppServiceProvider` — use module's own provider
- Never add module routes to core `routes/api.php` or `routes/web.php`
- Never register module observers in `AppServiceProvider` — register in module `boot()`

## Core Files Reference

These core files interact with the module system. Read them for context when debugging:

| File | Role |
|---|---|
| `app/Support/Module/BaseModuleServiceProvider.php` | Abstract base class for module providers |
| `app/Support/Module/ModuleManager.php` | Loads, registers, boots modules; manages dependencies and aggregation |
| `app/Contracts/Module/ModuleInterface.php` | Core identity contract |
| `app/Contracts/Module/AdminExtensionInterface.php` | Filament admin extension contract |
| `app/Contracts/Module/HasRoutesInterface.php` | Route registration contract |
| `app/Contracts/Module/HasMigrationsInterface.php` | Migration registration contract |
| `app/Contracts/Module/HasEventsInterface.php` | Event listener contract |
| `app/Contracts/Module/HasPoliciesInterface.php` | Authorization policy contract |
| `app/Filament/Resources/FrontendRebuildMap.php` | Aggregates rebuild rules from core + modules |
| `app/Support/FrontendRebuildRegistry.php` | Resolves rebuild reasons for a given model and event |
| `app/Jobs/TriggerFrontendRebuildJob.php` | Dispatches frontend rebuild |
| `config/modules.php` | Module registry config |

## Existing Modules Reference

### commerce (`ercee/module-commerce`)

- **Namespace:** `Modules\Commerce\`
- **Dependencies:** none
- **Domain models:** Product, ProductVariant, ProductReview, Order, Payment, Attribute, AttributeValue, Taxonomy
- **Events dispatched:** OrderPaid
- **Bindings:** PaymentGatewayInterface => StripeGateway
- **Observers:** ProductObserver, ProductReviewObserver, TaxonomyObserver, AttributeObserver
- **Filament Resources:** ProductResource, OrderResource, PaymentResource, AttributeResource, TaxonomyResource, ProductReviewResource
- **Routes:** API (products, taxonomy-mapping, checkout, stripe webhook), Web (storefront pages, product preview)
- **Permissions:** view/create/update/delete products, view/update orders, view payments, CRUD attributes, CRUD taxonomies

### forms (`ercee/module-forms`)

- **Namespace:** `Modules\Forms\`
- **Dependencies:** none
- **Domain models:** Form, Contract
- **Events dispatched:** ContractCreated
- **Observers:** FormObserver
- **Filament Resources:** FormResource, ContractResource
- **Blocks:** ContactFormBlock
- **Routes:** API (form show, form submit)
- **Permissions:** CRUD forms, view/update/delete contracts

### funnel (`ercee/module-funnel`)

- **Namespace:** `Modules\Funnel\`
- **Dependencies:** `forms ^1.0`, `commerce ^1.0`
- **Domain models:** Funnel, FunnelStep, FunnelRun
- **Listens to:** Forms\ContractCreated, Commerce\OrderPaid
- **Filament Resources:** FunnelResource, FunnelRunResource
- **Permissions:** CRUD funnels, view funnel_runs

## Common Mistakes to Avoid

1. **Adding `middleware('api')` or `prefix('api')` in module `routes/api.php`** — ModuleManager already applies these.
2. **Adding `middleware('web')` in module `routes/web.php`** — ModuleManager already applies this.
3. **Registering module observers in `AppServiceProvider`** — register in module `boot()`.
4. **Hardcoding module class references in core files** — use dynamic aggregation via ModuleManager.
5. **Forgetting `parent::boot()` call** — views and translations won't load.
6. **Importing module Application/Http classes cross-module** — use Events for communication.
7. **Forgetting to register the Observer in `boot()`** when creating rebuild rules — rules without observers won't trigger rebuilds.
8. **Creating module config under wrong key** — it must be `config/module.php` (singular), accessed via `config('module.{name}.key')`.
