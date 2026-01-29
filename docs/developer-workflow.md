# Developer Workflow

## Architektura

CMS se skl z:
- **Core** (`app/`) -- Content, Media, ThemeSetting, Subscriber, modulovy system
- **Moduly** (`modules/`) -- Forms, Commerce, Funnel

Moduly jsou registrovane v `config/modules.php` a nahrane pres `ModuleManager`.

## Struktura modulu

```
modules/<name>/
  src/
    Domain/          # Eloquent modely, eventy, services
    Application/     # Handlers, commands, results
    Filament/
      Resources/     # Filament admin resources
      Blocks/        # Page builder bloky
    Listeners/       # Event listeners
    <Name>ModuleServiceProvider.php
  routes/
    web.php
    api.php
  resources/
    views/
    lang/
  database/
    migrations/
  config/
    module.php
  tests/
  composer.json
```

## Lokalni vyvoj

Moduly jsou v mono-repu jako `path` repositories v `composer.json`:

```json
"repositories": [
    { "type": "path", "url": "modules/forms", "options": { "symlink": true } }
]
```

Autoload je definovany primo v root `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Modules\\Forms\\": "modules/forms/src/"
    }
}
```

Po pridani noveho modulu:
1. Vytvorit adresarovou strukturu v `modules/<name>/`
2. Pridat `composer.json` s `name: ercee/module-<name>`, `type: ercee-module`
3. Pridat PSR-4 autoload do root `composer.json`
4. Pridat path repository do root `composer.json`
5. Pridat konfiguraci do `config/modules.php`
6. Spustit `composer dump-autoload`

## Registrace modulu

Kazdy modul musi mit `ServiceProvider` ktery extenduje `BaseModuleServiceProvider`:

```php
class MyModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'my-module';
    protected string $version = '1.0.0';
    protected array $dependencies = [];
    protected array $permissions = ['view_items', 'create_items'];

    public function getResources(): array { return [...]; }
    public function getBlocks(): array { return [...]; }
    public function getEventListeners(): array { return [...]; }
}
```

Registrace v `config/modules.php`:

```php
'my-module' => [
    'enabled' => true,
    'provider' => \Modules\MyModule\MyModuleServiceProvider::class,
    'version' => '1.0.0',
    'dependencies' => [],
],
```

## Eventy

### Core eventy (dispatchovane z observeru)
- `ContentPublished` -- Page publikovana
- `MenuUpdated` -- Menu ulozeno
- `MediaUploaded` -- Nove medium vytvoreno

### Modulove eventy
- `ContractCreated` (forms) -- Novy contract
- `OrderPaid` (commerce) -- Objednavka zaplacena

Listenery se registruji v `getEventListeners()` service provideru:

```php
public function getEventListeners(): array
{
    return [
        ContractCreated::class => [MyListener::class],
    ];
}
```

## Permissions

Modulove permissions jsou prefixovane `module.<name>.<permission>`:
- `module.forms.view_forms`
- `module.commerce.view_products`

Seedovani: `RolesAndPermissionsSeeder` automaticky nacita permissions ze vsech modulu pres `ModuleManager::getAllPermissions()`.

## Verzovani

Moduly pouzivaji semantic versioning:
- **Major**: breaking zmeny v kontraktech/API
- **Minor**: nove features bez breaking zmen
- **Patch**: bugfixy

Verze je definovana na dvou mistech:
1. `composer.json` (`version` field)
2. `ServiceProvider` (`$version` property)

`ModuleManager` kontroluje shodu pri nacitani a loguje varovani pri nesouladu.

### Dependency constraints

Moduly deklaruji zavislosti s version constraints (`^`, `~`, `>=`, `<`):

```php
protected array $dependencies = [
    'forms' => '^1.0',
    'commerce' => '^1.0',
];
```

`ModuleManager` overi constraints pred registraci modulu.

## Testy

```bash
# Vsechny testy
composer test

# Modulove testy
php artisan test --filter=Forms
php artisan test --filter=Commerce
php artisan test --filter=Funnel
```

## Cache

Po zmenach v modulech:
```bash
php artisan cache:clear          # Vymazat block cache
php artisan config:clear         # Vymazat config cache
composer dump-autoload           # Regenerovat autoload
```

## Produkce

V produkci budou moduly nainstalovany pres Composer z VCS repositories:

```json
"require": {
    "ercee/module-forms": "^1.0",
    "ercee/module-commerce": "^1.0",
    "ercee/module-funnel": "^1.0"
}
```

Prechod z path na VCS: nahradit `path` repository za `vcs` repository s URL git repa modulu.
