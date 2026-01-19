# Frontend Guide

Stručný průvodce pro práci s frontend částí aplikace.

## Stack

- **Laravel Blade** - šablony
- **Tailwind CSS** - styling
- **Axios** - HTTP requesty (globálně dostupný jako `window.axios`)

## Struktura souborů

```
resources/
├── views/frontend/
│   ├── layout.blade.php      # Hlavní layout (header, nav, footer)
│   ├── page.blade.php        # CMS stránka s bloky
│   ├── products.blade.php    # Seznam produktů
│   ├── product.blade.php     # Detail produktu
│   ├── checkout.blade.php    # Checkout formulář
│   ├── thank-you.blade.php   # Potvrzení po platbě
│   └── blocks/               # Block komponenty
│       ├── text.blade.php
│       ├── image.blade.php
│       ├── cta.blade.php
│       └── form_embed.blade.php
├── js/app.js                 # JavaScript (form submission)
└── css/app.css               # Tailwind config
```

## Routes

| URL | Controller | Popis |
|-----|------------|-------|
| `/` | `FrontendController@home` | Homepage (page slug: `home`) |
| `/{slug}` | `FrontendController@page` | CMS stránka |
| `/products` | `FrontendController@products` | Seznam produktů |
| `/products/{id}` | `FrontendController@product` | Detail produktu |
| `/checkout/{productId}` | `FrontendController@checkout` | Checkout |
| `/thank-you` | `FrontendController@thankYou` | Děkovná stránka |
| `/payment/return` | `FrontendController@paymentReturn` | Return z platební brány |

## API Endpointy (pro JS)

```javascript
// Odeslání formuláře
axios.post('/api/v1/forms/{id}/submit', { email: '...', ...data })

// Checkout
axios.post('/api/v1/checkout', { product_id: 1, email: '...' })

// Získání produktů
axios.get('/api/v1/products')

// Získání stránky
axios.get('/api/v1/pages/{slug}')

// Získání navigace
axios.get('/api/v1/navigation')

// Získání formuláře (schema)
axios.get('/api/v1/forms/{id}')
```

## Block typy

### text
```php
$block['data']['heading']  // string|null
$block['data']['body']     // string|null
```

### image
```php
$block['data']['image']    // string - cesta k souboru
$block['data']['alt']      // string|null
$block['data']['caption']  // string|null
```

### cta
```php
$block['data']['title']       // string|null
$block['data']['description'] // string|null
$block['data']['button_text'] // string|null
$block['data']['button_url']  // string|null
```

### form_embed
```php
$block['data']['form_id']  // int - ID formuláře
```

## Přidání nového block typu

1. Přidej konstantu do `app/Domain/Content/Page.php`:
```php
public const BLOCK_TYPE_VIDEO = 'video';
```

2. Vytvoř Blade šablonu `resources/views/frontend/blocks/video.blade.php`:
```blade
<div class="my-8">
    <video src="{{ $block['data']['url'] }}" controls></video>
</div>
```

3. Aktualizuj Filament resource pro Pages (přidej pole do block builderu).

## Form submission (JS)

Funkce `submitForm(event, formId)` je globálně dostupná:

```html
<form onsubmit="return submitForm(event, {{ $form->id }})">
    <input type="email" name="email" required>
    <button type="submit">Odeslat</button>
    <div id="form-message-{{ $form->id }}"></div>
</form>
```

## Checkout flow

1. Uživatel klikne "Buy Now" na produktu
2. Přesměrování na `/checkout/{productId}`
3. Uživatel vyplní email a odešle
4. JS volá `POST /api/v1/checkout`
5. API vrátí `redirect_url` (Stripe Checkout)
6. JS přesměruje na Stripe
7. Po platbě Stripe přesměruje na `/payment/return?session_id=...`
8. Zobrazí se thank-you stránka

## Caching

Data jsou cachována na 1 hodinu:
- Stránky: `page:{slug}`
- Produkty: `products:active`, `product:{id}`
- Navigace: `navigation:tree`

Cache se invaliduje přes Model Observers při změnách v adminu.

## Tailwind classes reference

```
// Container
mx-auto max-w-7xl px-4 sm:px-6 lg:px-8

// Button primary
rounded-md bg-blue-600 px-6 py-3 text-white hover:bg-blue-700

// Input
block w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500

// Card
rounded-lg bg-white p-6 shadow-md

// Error message
rounded-md bg-red-50 p-4 text-red-700

// Success message
rounded-md bg-green-50 p-4 text-green-700
```

## Přidání nové stránky

Pro statickou stránku mimo CMS:

1. Přidej route do `routes/web.php`:
```php
Route::get('/contact', [FrontendController::class, 'contact']);
```

2. Přidej metodu do `FrontendController`:
```php
public function contact(): View
{
    return view('frontend.contact', [
        'navigation' => $this->getNavigation(),
    ]);
}
```

3. Vytvoř `resources/views/frontend/contact.blade.php`:
```blade
@extends('frontend.layout')

@section('title', 'Contact')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8">
        <h1>Contact</h1>
    </div>
@endsection
```

## Build assets

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build
```
