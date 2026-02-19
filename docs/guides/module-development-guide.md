# Vývoj modulů pro Ercee CMS

Tento průvodce popisuje, jak vytvářet, registrovat a spravovat moduly v Ercee CMS.

## Architektura

Moduly jsou samostatné Composer balíčky žijící v `../ercee-modules/ercee-module-{name}/`. Propojení s CMS probíhá přes:

1. **Composer path repository** – symlink z `../ercee-modules/*` do `modules/*/`
2. **Config registrace** – `config/modules.php` definuje enabled moduly a jejich providery
3. **ModuleManager** – centrální orchestrátor (`app/Support/Module/ModuleManager.php`), který moduly načítá, registruje a bootuje

```
ercee-modules/
├── ercee-module-commerce/     ← Composer balíček "ercee/module-commerce"
├── ercee-module-forms/        ← Composer balíček "ercee/module-forms"
└── ercee-module-funnel/       ← Composer balíček "ercee/module-funnel"

Ercee-cms-llm-claude/
├── modules/                   ← Symlinky na ercee-modules (Composer)
├── config/modules.php         ← Registrace modulů
└── app/Support/Module/        ← Core module system
```

## Vytvoření nového modulu

### 1. Scaffold

```bash
php artisan module:make blog
```

Vytvoří strukturu v `../ercee-modules/ercee-module-blog/`:

```
ercee-module-blog/
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
    ├── Domain/
    ├── Application/
    ├── Filament/
    │   └── Resources/
    ├── Http/
    │   └── Controllers/
    └── BlogModuleServiceProvider.php
```

### 2. Registrace v Composeru

V hlavním `composer.json` přidej do `require`:

```json
"ercee/module-blog": "@dev"
```

Spusť:

```bash
composer update ercee/module-blog
```

### 3. Registrace v config

V `config/modules.php` přidej do pole `modules`:

```php
'blog' => [
    'enabled' => true,
    'provider' => \Modules\Blog\BlogModuleServiceProvider::class,
    'version' => '1.0.0',
    'dependencies' => [],
],
```

### 4. Ověření

```bash
php artisan module:list
```

## Testy, CI a release workflow

Nově platí, že každý modul musí mít základní testy a CI workflow.

### Povinné soubory v modulu

- `.github/workflows/ci.yml`
- `.github/workflows/pr-check.yml`
- `.github/workflows/release.yml`
- `phpunit.xml`
- `tests/TestCase.php`
- `tests/Unit/` (minimálně 1 unit test)
- `CHANGELOG.md` (release workflow ho aktualizuje)

### Doporučené kroky po scaffoldu

1. Zkopíruj workflow z `../ercee-modules/ercee-module-forms/.github/workflows/`.
2. Vytvoř `phpunit.xml` a `tests/` strukturu ve stejném formátu jako `ercee-module-forms`.
3. Přidej minimální unit testy (bez DB/HTTP) ověřující DTO, value objekty nebo doménové konstanty.
4. Přidej `CHANGELOG.md` se sekcí `## [Unreleased]`.

### Šablony (kopírovat beze změn)

`phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="vendor/autoload.php"
    colors="true"
    failOnRisky="true"
    failOnWarning="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

`tests/TestCase.php`:

```php
<?php

namespace Modules\Blog\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
}
```

### Příkazy pro lokální ověření

V modulech spouštěj PHPUnit přímo (moduly nejsou plná Laravel aplikace):

```bash
composer install
./vendor/bin/phpunit
```

Pokud potřebuješ testovat modul přes Laravel `php artisan test`, spouštěj testy z hlavního CMS projektu, ne z repo modulu.

## Seeder pattern (JSON only)

Pro konzistenci mezi CMS a moduly musí seedery načítat data z JSON souborů, ne z hardcoded PHP polí.

Požadovaný pattern:

- JSON data ukládej do `storage/app/seed-data/*.json` (v kontextu CMS projektu)
- seeder musí bezpečně řešit:
  - neexistující soubor (warning + return)
  - nevalidní JSON (warning + return)
  - nevalidní záznam v poli (skip + pokračovat)
- preferuj `updateOrCreate()` pro idempotentní seed běh
- volitelně podporuj env override typu `*_SEED_PATH`

Příklad spuštění:

```bash
php artisan db:seed --class='Modules\\Analytics\\Database\\Seeders\\AnalyticsProvidersSeeder'
```

## Adresářová struktura modulu

| Adresář | Účel |
|---|---|
| `src/Domain/` | Eloquent modely, Events, Contracts, Services, Gateways |
| `src/Application/` | CQRS: Commands, Handlers, Results |
| `src/Filament/Resources/` | Filament admin Resources, Pages, RelationManagers |
| `src/Filament/Blocks/` | Page builder bloky |
| `src/Http/Controllers/` | API a web controllery |
| `src/Http/Requests/` | Form Request validace |
| `src/Observers/` | Eloquent model observery |
| `config/module.php` | Konfigurace modulu (mergována pod klíčem `module.{name}`) |
| `database/migrations/` | Migrace (automaticky registrovány) |
| `routes/api.php` | API routes (automaticky dostávají `middleware('api')` + `prefix('api')`) |
| `routes/web.php` | Web routes (automaticky dostávají `middleware('web')`) |
| `resources/views/` | Blade views (přístupné přes `{name}::view.name`) |

## Service Provider

Každý modul má Service Provider dědící z `BaseModuleServiceProvider`:

```php
<?php

declare(strict_types=1);

namespace Modules\Blog;

use App\Support\Module\BaseModuleServiceProvider;

class BlogModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'blog';
    protected string $version = '1.0.0';
    protected string $description = 'Blog module';
    protected array $dependencies = [];
    protected array $permissions = [
        'view_posts',
        'create_posts',
        'update_posts',
        'delete_posts',
    ];

    protected function registerBindings(): void
    {
        // DI bindings, např.:
        // $this->app->bind(PostRepositoryInterface::class, EloquentPostRepository::class);
    }

    public function boot(): void
    {
        parent::boot(); // Načte views, translations, assets

        // Registrace observerů
        \Modules\Blog\Domain\Post::observe(\Modules\Blog\Observers\PostObserver::class);
    }

    public function getResources(): array
    {
        return [
            \Modules\Blog\Filament\Resources\PostResource::class,
        ];
    }

    public function getBlocks(): array
    {
        return [
            \Modules\Blog\Filament\Blocks\LatestPostsBlock::class,
        ];
    }

    public function getEventListeners(): array
    {
        return [
            // Event::class => ListenerClass::class,
        ];
    }

    public function getRebuildRules(): array
    {
        return [
            \Modules\Blog\Filament\Resources\PostResource::class => [
                'model' => \Modules\Blog\Domain\Post::class,
                'events' => [
                    'saved' => ['reason' => 'post_updated:{slug}'],
                    'deleted' => ['reason' => 'post_deleted:{slug}'],
                ],
            ],
        ];
    }

    protected function getModulePath(string $path = ''): string
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }
}
```

## Kontrakty (interfaces)

Provider implementuje 6 kontraktů automaticky přes `BaseModuleServiceProvider`:

| Interface | Metody | Účel |
|---|---|---|
| `ModuleInterface` | `getName()`, `getVersion()`, `getDescription()`, `getDependencies()`, `getPermissions()`, `isEnabled()` | Základní identita modulu |
| `AdminExtensionInterface` | `getResources()`, `getPages()`, `getWidgets()`, `getNavigationItems()`, `getBlocks()` | Integrace do Filament admin panelu |
| `HasRoutesInterface` | `getWebRoutes()`, `getApiRoutes()` | Registrace routes |
| `HasMigrationsInterface` | `getMigrationsPath()` | Registrace migrací |
| `HasEventsInterface` | `getEventListeners()`, `getEventSubscribers()` | Event-driven komunikace |
| `HasPoliciesInterface` | `getPolicies()` | Authorization policies |

Všechny metody mají default implementaci v `BaseModuleServiceProvider` (vrací prázdné pole / null). Stačí overridnout jen ty, které potřebuješ.

## Routy

### API routes (`routes/api.php`)

ModuleManager automaticky wrappuje api routes: `Route::middleware('api')->prefix('api')->group(...)`.

Nepoužívej tyto wrappery v souboru – piš rovnou prefix `v1`:

```php
<?php

use Modules\Blog\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->middleware('throttle:api-read');
    Route::get('/posts/{id}', [PostController::class, 'show'])->whereNumber('id')->middleware('throttle:api-read');
});
```

Výsledné URL: `GET /api/v1/posts`, `GET /api/v1/posts/{id}`

### Web routes (`routes/web.php`)

ModuleManager wrappuje web routes: `Route::middleware('web')->group(...)`.

```php
<?php

use Modules\Blog\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::middleware('redirect.frontend')->group(function () {
    Route::get('/blog', [BlogController::class, 'index'])->name('frontend.blog');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('frontend.blog.show');
});
```

## Závislosti mezi moduly

Závislosti se deklarují na dvou místech:

### 1. V service provideru

```php
protected array $dependencies = [
    'forms' => '^1.0',
    'commerce' => '^1.0',
];
```

### 2. V `config/modules.php`

```php
'funnel' => [
    'enabled' => true,
    'provider' => \Modules\Funnel\FunnelModuleServiceProvider::class,
    'version' => '1.0.0',
    'dependencies' => [
        'forms' => '^1.0',
        'commerce' => '^1.0',
    ],
],
```

Podporované version constraints: `^1.0`, `~1.2`, `>=1.0`, `>1.0`, `<=2.0`, `<2.0`, `*`

ModuleManager zkontroluje závislosti při registraci. Pokud požadovaný modul není enabled nebo nesplňuje verzi, modul se nezaregistruje a zapíše warning do logu.

## Cross-module komunikace

Moduly spolu komunikují přes **Laravel Events**:

```php
// Modul Forms vyšle event
class ContractCreated
{
    public function __construct(public Contract $contract) {}
}

// Modul Funnel naslouchá
public function getEventListeners(): array
{
    return [
        \Modules\Forms\Domain\Events\ContractCreated::class =>
            \Modules\Funnel\Listeners\StartFunnelOnContractCreated::class,
    ];
}
```

## Frontend rebuild rules

Pokud se při uložení modelu v admin panelu má přegenerovat frontend, registruj rebuild rules:

```php
public function getRebuildRules(): array
{
    return [
        PostResource::class => [
            'model' => Post::class,
            'events' => [
                'saved' => [
                    'reason' => 'post_updated:{slug}',
                    'condition' => ['method' => 'isPublished', 'equals' => true],
                ],
                'deleted' => ['reason' => 'post_deleted:{slug}'],
            ],
        ],
    ];
}
```

Zároveň vytvoř Observer, který dispatchuje rebuild job:

```php
<?php

namespace Modules\Blog\Observers;

use Modules\Blog\Domain\Post;
use App\Jobs\TriggerFrontendRebuildJob;
use App\Support\FrontendRebuildRegistry;

class PostObserver
{
    public function saved(Post $post): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($post, 'saved') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }

    public function deleted(Post $post): void
    {
        foreach (FrontendRebuildRegistry::reasonsFor($post, 'deleted') as $reason) {
            TriggerFrontendRebuildJob::dispatch($reason);
        }
    }
}
```

Registruj observer v `boot()` provideru:

```php
public function boot(): void
{
    parent::boot();
    Post::observe(PostObserver::class);
}
```

## Permissions

Permissions se definují v provideru a automaticky se prefixují:

```php
protected array $permissions = ['view_posts', 'create_posts'];
```

Ve Filament/kódu pak: `module.blog.view_posts`, `module.blog.create_posts`

## Konfigurace

`config/module.php` se automaticky merguje pod klíčem `module.{name}`:

```php
// config/module.php
return [
    'name' => 'blog',
    'version' => '1.0.0',
    'posts_per_page' => env('BLOG_POSTS_PER_PAGE', 12),
];
```

Přístup: `config('module.blog.posts_per_page')`

## composer.json modulu

```json
{
    "name": "ercee/module-blog",
    "description": "Ercee CMS Blog module",
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
            "Modules\\Blog\\": "src/"
        }
    },
    "extra": {
        "ercee": {
            "name": "blog",
            "provider": "Modules\\Blog\\BlogModuleServiceProvider"
        },
        "laravel": {
            "providers": [
                "Modules\\Blog\\BlogModuleServiceProvider"
            ]
        }
    }
}
```

## Artisan příkazy

| Příkaz | Popis |
|---|---|
| `php artisan module:make {name}` | Scaffolduje nový modul |
| `php artisan module:list` | Vypíše všechny moduly se statusem |
| `php artisan module:sync` | Zkontroluje shodu config vs. nalezené moduly |

## Checklist pro nový modul

- [ ] `php artisan module:make {name}`
- [ ] Přidat Domain modely do `src/Domain/`
- [ ] Vytvořit migrace v `database/migrations/`
- [ ] Vytvořit Filament Resources v `src/Filament/Resources/`
- [ ] Vytvořit API/web controllery v `src/Http/Controllers/`
- [ ] Definovat routes v `routes/api.php` a/nebo `routes/web.php`
- [ ] Registrovat resources v `getResources()`
- [ ] Registrovat bloky v `getBlocks()` (pokud modul rozšiřuje page builder)
- [ ] Přidat rebuild rules v `getRebuildRules()` + vytvořit Observery
- [ ] Definovat permissions v `$permissions`
- [ ] Přidat DI bindings v `registerBindings()`
- [ ] Přidat event listeners v `getEventListeners()` (pokud naslouchá jiným modulům)
- [ ] Přidat `"ercee/module-{name}": "@dev"` do hlavního `composer.json`
- [ ] `composer update ercee/module-{name}`
- [ ] Registrovat v `config/modules.php`
- [ ] `php artisan module:list` pro ověření
- [ ] `php artisan migrate` pro spuštění migrací
