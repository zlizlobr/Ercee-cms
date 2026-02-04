# Developer Workflow

## Architektura

CMS se skl z:
- **Core** (`app/`) -- Content, Media, ThemeSetting, Subscriber, modulovy system
- **Moduly** (`modules/`) -- Funnel
- **Externi moduly** -- Forms a Commerce jsou v samostatnych repech a napojene pres Composer

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

Externi moduly jsou napojene jako `path` repository v `composer.json`:

```json
"repositories": [
    { "type": "path", "url": "../ercee-modules/ercee-module-forms", "options": { "symlink": true } },
    { "type": "path", "url": "../ercee-modules/ercee-module-commerce", "options": { "symlink": true } }
]
```

Autoload pro externi modul neni definovany v root `composer.json` (respektuje se autoload z jeho `composer.json`).

Pro lokalni modul v mono-repu je autoload definovany primo v root `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Modules\\Funnel\\": "modules/funnel/src/"
    }
}
```

Po pridani noveho modulu:
1. Vytvorit adresarovou strukturu v `modules/<name>/`
2. Pridat `composer.json` s `name: ercee/module-<name>`, `type: ercee-module`
3. Pridat PSR-4 autoload do root `composer.json` (jen pro lokalni moduly v `modules/`)
4. Pridat path repository do root `composer.json` (pro externi moduly)
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

## Docker (budouci faze)

Az bude projekt pripraven na kontejnerizaci, doporucena konfigurace:

### Dockerfile (multi-stage)

```dockerfile
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
    nginx supervisor libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    libzip-dev icu-dev libxml2-dev oniguruma-dev curl-dev sqlite-dev postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql pdo_sqlite pdo_pgsql mbstring exif pcntl bcmath gd zip intl soap opcache \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

# ---

FROM base AS composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
COPY modules/ modules/
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

# ---

FROM base AS production

COPY --from=composer /var/www/html /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
RUN mkdir -p /var/log/supervisor /run/nginx

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
```

### docker-compose.yml

```yaml
services:
  app:
    build:
      context: .
      target: production
    ports:
      - "${APP_PORT:-8000}:80"
    environment:
      - APP_ENV=${APP_ENV:-production}
      - APP_KEY=${APP_KEY}
      - DB_CONNECTION=pgsql
      - DB_HOST=db
      - DB_PORT=5432
      - DB_DATABASE=${DB_DATABASE:-ercee}
      - DB_USERNAME=${DB_USERNAME:-ercee}
      - DB_PASSWORD=${DB_PASSWORD:-secret}
      - CACHE_STORE=redis
      - QUEUE_CONNECTION=redis
      - SESSION_DRIVER=redis
      - REDIS_HOST=redis
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_healthy
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/api/health"]
      interval: 30s
      timeout: 5s
      retries: 3
    volumes:
      - storage:/var/www/html/storage/app

  db:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: ${DB_DATABASE:-ercee}
      POSTGRES_USER: ${DB_USERNAME:-ercee}
      POSTGRES_PASSWORD: ${DB_PASSWORD:-secret}
    volumes:
      - dbdata:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${DB_USERNAME:-ercee}"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5
    volumes:
      - redisdata:/data

volumes:
  dbdata:
  redisdata:
  storage:
```

### Doplnkove soubory

**docker/php.ini:**
```ini
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M
max_execution_time = 60
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
```

**docker/nginx.conf:**
```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php;
    client_max_body_size 64M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**docker/supervisord.conf:**
```ini
[supervisord]
nodaemon=true

[program:php-fpm]
command=php-fpm -F
autorestart=true

[program:nginx]
command=nginx -g "daemon off;"
autorestart=true

[program:queue-worker]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autorestart=true
stopwaitsecs=30
```

### .dockerignore

```
.git
.github
node_modules
vendor
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
.env
tests
docs
docker-compose.yml
```

### Spusteni

```bash
# Build a start
docker compose up -d --build

# Prvni setup
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# Logy
docker compose logs -f app
```
