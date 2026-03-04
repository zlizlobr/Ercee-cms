# Tinker: schémata tabulek + ukázkový záznam (modely)

Tento dokument je určený pro práci v `php artisan tinker`.

## Script 1: mapování model → tabulka → sloupce
Spusť tinker a vlož celý blok:

```php
use Illuminate\Support\Facades\Schema;

$models = [
    App\Domain\Content\Menu::class,
    App\Domain\Content\Navigation::class,
    App\Domain\Content\Page::class,
    App\Domain\Content\ThemeSetting::class,
    App\Domain\Media\MediaLibrary::class,
    Modules\Forms\Domain\Subscriber\Subscriber::class,
    Modules\Commerce\Domain\Attribute::class,
    Modules\Commerce\Domain\AttributeValue::class,
    Modules\Commerce\Domain\Order::class,
    Modules\Commerce\Domain\Payment::class,
    Modules\Commerce\Domain\Product::class,
    Modules\Commerce\Domain\ProductReview::class,
    Modules\Commerce\Domain\ProductVariant::class,
    Modules\Commerce\Domain\Taxonomy::class,
    Modules\Forms\Domain\Contract::class,
    Modules\Forms\Domain\Form::class,
    Modules\Funnel\Domain\Funnel::class,
    Modules\Funnel\Domain\FunnelRun::class,
    Modules\Funnel\Domain\FunnelRunStep::class,
    Modules\Funnel\Domain\FunnelStep::class,
];

collect($models)->mapWithKeys(function (string $modelClass) {
    $model = new $modelClass();
    $table = $model->getTable();

    return [
        $modelClass => [
            'table' => $table,
            'columns' => Schema::getColumnListing($table),
        ],
    ];
})->all();
```

## Jednopříkazové získání záznamu pro každý model
Níže je vždy jeden příkaz pro vytažení prvního záznamu (pokud existuje):

```php
App\Domain\Content\Menu::query()->first();
App\Domain\Content\Navigation::query()->first();
App\Domain\Content\Page::query()->first();
App\Domain\Content\ThemeSetting::query()->first();
App\Domain\Media\MediaLibrary::query()->first();
Modules\Forms\Domain\Subscriber\Subscriber::query()->first();
Modules\Commerce\Domain\Attribute::query()->first();
Modules\Commerce\Domain\AttributeValue::query()->first();
Modules\Commerce\Domain\Order::query()->first();
Modules\Commerce\Domain\Payment::query()->first();
Modules\Commerce\Domain\Product::query()->first();
Modules\Commerce\Domain\ProductReview::query()->first();
Modules\Commerce\Domain\ProductVariant::query()->first();
Modules\Commerce\Domain\Taxonomy::query()->first();
Modules\Forms\Domain\Contract::query()->first();
Modules\Forms\Domain\Form::query()->first();
Modules\Funnel\Domain\Funnel::query()->first();
Modules\Funnel\Domain\FunnelRun::query()->first();
Modules\Funnel\Domain\FunnelRunStep::query()->first();
Modules\Funnel\Domain\FunnelStep::query()->first();
```

## Jednopříkazové získání záznamu pro ID
Níže je vždy jeden příkaz pro vytažení prvního záznamu (pokud existuje):

```php
use Modules\Forms\Domain\Contract;

// vrátí model (nebo null, pokud neexistuje)
Contract::find(26);

// pokud chceš čistě data jako pole:
Contract::query()->whereKey(26)->first()?->toArray();
```