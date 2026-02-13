# JSON Seeders Guide

Tento projekt pouziva seedery, ktere nacitaji data z JSON souboru ve slozce:

- `storage/app/seed-data/`

## Aktualni mapovani

- `database/seeders/AdminUserSeeder.php` -> `storage/app/seed-data/admin-user.json`
- `database/seeders/RolesAndPermissionsSeeder.php` -> `storage/app/seed-data/roles-permissions.json`
- `database/seeders/NavigationSeeder.php` -> `storage/app/seed-data/navigation.json`
- `database/seeders/ProductsSeeder.php` -> `storage/app/seed-data/products.json`
- `database/seeders/TemplatePagesSeeder.php` -> `storage/app/seed-data/template-pages.json`
- `database/seeders/HomePageSeeder.php` -> `storage/app/seed-data/template-pages.json` (filtr `slug=home`)
- `Modules\\Forms\\Database\\Seeders\\FormsSeeder` -> `storage/app/seed-data/forms.json`

## Spusteni

```bash
php artisan db:seed
```

Nebo jednotlive:

```bash
php artisan db:seed --class=Database\\Seeders\\ProductsSeeder
```

## Chovani pri chybe

JSON seedery jsou navrzene fail-safe:

- pokud soubor chybi, seeder vypise warning a skonci
- pokud JSON neni validni, seeder vypise warning a skonci
- pokud je vadny jednotlivy zaznam, seeder ho preskoci (kde to konkretni seeder podporuje)

Sdilene nacitani je v `database/seeders/Concerns/ReadsJsonSeedData.php`.
