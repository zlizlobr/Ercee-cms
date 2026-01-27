# Ercee CMS

Laravel-based headless CMS platform with Filament admin panel and decoupled Astro static frontend.

## Architecture

```
┌─────────────────────┐     ┌─────────────────────┐     ┌─────────────────────┐
│   admin.domain      │     │    api.domain       │     │    www.domain       │
│   Laravel + Filament│────▶│    Laravel API      │◀────│    Astro (CDN)      │
│   (Backoffice)      │     │    (REST API)       │     │    (Static Site)    │
└─────────────────────┘     └─────────────────────┘     └─────────────────────┘
                                     │
                                     ▼
                            GitHub Actions
                            (Auto-rebuild on content change)
```

**Frontend rebuild mapping:** which admin changes trigger a frontend rebuild is centrally defined in
`app/Filament/Resources/FrontendRebuildMap.php` and enforced by model observers.

**Repositories:**
- **CMS Backend:** This repository (Laravel + Filament)
- **Public Frontend:** [github.com/zlizlobr/ercee-frontend](https://github.com/zlizlobr/ercee-frontend) (Astro)

## Features

- **Filament Admin Panel** - Modern backoffice UI at `/admin`
- **Private Media Library** - Spatie Media Library on a private disk with exportable public assets
- **Role-Based Access Control** - Using spatie/laravel-permission (admin, operator, marketing roles)
- **Domain-Driven Design** - Clean architecture with Domain, Application, Infrastructure layers
- **Headless CMS** - Block-based page builder with public REST API
- **Decoupled Astro Frontend** - Static site generation with automatic rebuilds on content changes
- **Core Entities**:
  - **Subscribers** - Marketing contact management
  - **Pages** - Block-based content with SEO support (text, image, CTA, form embed blocks)
  - **Products** - WooCommerce-like product system with types, variants, and taxonomies
  - **Orders** - Order tracking with payment integration
  - **Payments** - Multi-gateway payment processing (Stripe, GoPay, Comgate)
  - **Menus & Navigation** - Multi-menu system with hierarchical navigation items (supports pages, custom URLs, anchors)
  - **Theme Settings** - Global/header/footer configuration with menu mapping and CTA overrides for the frontend
  - **Theme Settings** - Global/header/footer configuration with menu mapping and CTA overrides for the frontend
  - **Forms** - Dynamic form builder with schema validation
  - **Contracts** - Lead capture and form submissions
  - **Product Reviews** - Customer reviews with approval workflow
- **Lead Capture System** - Public form submission API with anti-spam protection
- **Marketing Automation** - Funnel engine with multi-step workflows triggered by events
- **Lightweight Commerce** - Simple checkout with Stripe integration
- **Production Hardening** - Idempotent handlers, request IDs, rate limiting, webhook signature verification, IP whitelisting

## Local Development

- Backend setup: `docs/guides/setup/local-backend-setup.md`
- Astro frontend setup: `docs/guides/setup/local-frontend-setup.md`

## Available Commands

```bash
# Run tests
php artisan test

# Run linter
composer lint

# Run static analysis
composer analyse

# Clear Builder blocks cache (after adding/removing blocks)
php artisan blocks:clear

# Generate a CMS block (Filament + Astro + translations)
php artisan make:cms-block "Block Name" --schema-file=path/to/schema.json

# Export private media assets + manifest
php artisan media:export --only-changed

# Migrate legacy block images to Media Library
php artisan media:migrate-blocks --dry-run

# Migrate legacy RichEditor images to Media Library placeholders
php artisan media:migrate-richtext --dry-run --model=Page

# Start queue worker
php artisan queue:work

# Build frontend assets
npm run build

# Watch frontend assets
npm run dev
```

## CMS Block Generator

Use the `make:cms-block` Artisan command to scaffold CMS blocks, Filament form schema, localization entries, and Astro components/types.

- Guide: `docs/make-cms-block-guide.md`
- Implementation plan: `docs/block-command-implementation.md`

## Code Quality

This project uses:
- **Laravel Pint** for code formatting
- **PHPStan + Larastan** for static analysis

Run before committing:
```bash
composer lint
composer analyse
```

## Admin Panel

Access the admin panel at `http://localhost:8000/admin`

### Default Credentials

- **Email:** `admin@example.com`
- **Password:** `password`

### Available Roles

| Role | Description |
|------|-------------|
| `admin` | Full access to all features |
| `operator` | Operational access |
| `marketing` | Marketing-related features |

### Navigation Groups

| Group | Resources |
|-------|-----------|
| Content | Pages, Navigation, Forms |
| Products | Products, Taxonomies, Attributes, Reviews |
| Commerce | Orders, Payments |
| Marketing | Subscribers, Funnels, Contracts |
| Thema | Theme Settings |
| Thema | Theme Settings |

## Public API

The CMS exposes a REST API for frontend consumption.

### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/pages` | Get all published page slugs (for static generation) |
| GET | `/api/v1/pages/{slug}` | Get published page by slug with blocks and SEO |
| GET | `/api/v1/navigation` | Get main menu navigation items (default) |
| GET | `/api/v1/navigation/{menuSlug}` | Get navigation items by menu slug |
| GET | `/api/v1/menus/{menuSlug}` | Get full menu with metadata and items |
| GET | `/api/v1/theme` | Get theme settings (global, header, footer) |
| GET | `/api/v1/theme` | Get theme settings (global, header, footer) |
| GET | `/api/v1/products` | Get active products list (with filters) |
| GET | `/api/v1/products/{id}` | Get single product with variants, taxonomies, attributes |
| GET | `/api/v1/forms/{id}` | Get form schema for rendering |
| POST | `/api/v1/forms/{id}/submit` | Submit form data (rate limited: 5/min per IP) |
| POST | `/api/v1/checkout` | Initiate checkout (rate limited: 10/min per IP) |

### Internal Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/internal/rebuild-frontend` | Trigger frontend rebuild (API token protected) |

### Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/webhooks/stripe` | Stripe payment webhook (IP whitelisted) |

> **Security:** Webhook endpoints verify the `Stripe-Signature` header and can be protected by IP whitelist middleware. Configure `WEBHOOK_IP_WHITELIST` in `.env` with comma-separated IPs or CIDR ranges (e.g., Stripe webhook IPs).

### Example Response - Page

```json
{
  "data": {
    "id": 1,
    "slug": "homepage",
    "title": "Homepage",
    "blocks": [
      {
        "type": "text",
        "position": 0,
        "data": {
          "heading": "Welcome",
          "body": "<p>Content here...</p>"
        }
      }
    ],
    "seo": {
      "title": "Homepage | Ercee",
      "description": "Welcome to our site",
      "open_graph": {
        "title": "Homepage",
        "description": "Welcome",
        "image": "pages/og/image.jpg"
      }
    },
    "published_at": "2026-01-19T10:00:00+00:00"
  }
}
```

### Example Response - Navigation

```json
{
  "data": [
    {
      "id": 1,
      "title": "Home",
      "slug": "home",
      "url": "/",
      "target": "_self",
      "children": [
        {
          "id": 2,
          "title": "About",
          "slug": "about",
          "url": "/about",
          "target": "_self",
          "children": []
        }
      ]
    }
  ]
}
```

### Navigation Item URL Types

| Type | Example | Description |
|------|---------|-------------|
| Page link | `/about` | Internal page reference |
| Custom URL | `https://example.com` | External link |
| Anchor | `#contact` | Same-page anchor |
| Page + anchor | `/about#team` | Internal page with anchor |

### Block Types

| Type | Fields |
|------|--------|
| `hero` | heading, subheading, background_media, button_text, button_url |
| `text` | heading, body |
| `image` | media, alt, caption |
| `cta` | title, description, button_text, button_url, style |
| `form_embed` | form_id, title, description |

Blocks are auto-loaded from `app/Filament/Blocks/` directory. See [Page Builder](#page-builder-blocks) section for details.

### Form Submission

```bash
curl -X POST http://localhost:8000/api/v1/forms/1/submit \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "first_name": "John"}'
```

**Response:**
```json
{
  "data": {
    "contract_id": 1
  }
}
```

**Anti-spam protection:**
- Rate limiting: 5 requests per minute per IP
- Honeypot field: include `_hp_field` (must be empty)
- Idempotency: send `Idempotency-Key` to safely retry (responses may include `X-Idempotent-Replay: true`)

### Form Field Types

| Type | Description |
|------|-------------|
| `text` | Single line text input |
| `email` | Email input with validation |
| `textarea` | Multi-line text input |
| `select` | Dropdown with options |
| `checkbox` | Boolean checkbox |

### Checkout

```bash
curl -X POST http://localhost:8000/api/v1/checkout \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "email": "customer@example.com"}'
```

**Response:**
```json
{
  "data": {
    "order_id": 1,
    "redirect_url": "https://checkout.stripe.com/..."
  }
}
```

**Rate limiting:** 10 requests per minute per IP
**Idempotency:** send `Idempotency-Key` to safely retry (responses may include `X-Idempotent-Replay: true`)

## Commerce

The platform includes a WooCommerce-like commerce layer with advanced product management.

### Configuration

Add to `.env`:

```env
CURRENCY_CODE=CZK
CURRENCY_DECIMALS=2
```

### Product Types

| Type | Description |
|------|-------------|
| `simple` | Standard product with single price |
| `virtual` | Digital/downloadable product (no shipping) |
| `variable` | Product with variants (e.g., size, color combinations) |

### Taxonomies

Products can be organized using three taxonomy types:

| Type | Description |
|------|-------------|
| `category` | Hierarchical product categories |
| `tag` | Flat tags for flexible grouping |
| `brand` | Product brands/manufacturers |

### Attributes & Variants

For variable products, you can define:
- **Attributes** - Product characteristics (color, size, material)
- **Attribute Values** - Possible values for each attribute (red, blue, S, M, L)
- **Variants** - Specific combinations with own SKU, price, and stock

### Product Reviews

Reviews support an approval workflow:

| Status | Description |
|--------|-------------|
| `pending` | Awaiting moderation |
| `approved` | Visible on storefront |
| `rejected` | Hidden from storefront |

### Order Statuses

| Status | Description |
|--------|-------------|
| `pending` | Waiting for payment |
| `paid` | Successfully paid |
| `failed` | Payment failed |
| `cancelled` | Order cancelled |

### Payment Gateways

| Gateway | Status |
|---------|--------|
| Stripe | ✅ Implemented |
| GoPay | 🔜 Planned |
| Comgate | 🔜 Planned |

### Checkout Flow

1. Customer submits checkout form → `POST /api/v1/checkout`
2. System creates Order and redirects to Stripe Checkout
3. Customer completes payment on Stripe
4. Stripe sends webhook → `POST /api/webhooks/stripe`
5. System marks Order as paid and dispatches `OrderPaid` event
6. Marketing funnels are triggered automatically

For more details, see [docs/guides/commerce/commerce-guide.md](docs/guides/commerce/commerce-guide.md).

## Marketing Automation (Funnels)

The platform includes a funnel engine for automated marketing sequences.

### Trigger Types

| Trigger | Description |
|---------|-------------|
| `contract_created` | Fires when a form is submitted |
| `order_paid` | Fires when an order is paid |
| `manual` | Manually triggered via admin |

### Step Types

| Step | Config | Description |
|------|--------|-------------|
| `delay` | `{"seconds": 3600}` | Wait before next step |
| `email` | `{"subject": "...", "body": "..."}` | Send email to subscriber |
| `webhook` | `{"url": "...", "method": "POST"}` | Call external HTTP endpoint |
| `tag` | `{"tag": "lead_qualified"}` | Add tag to subscriber |

### Queue Processing

Funnels run asynchronously via Laravel queues. Start the queue worker:

```bash
php artisan queue:work
```

For more details, see [docs/guides/marketing/funnel-guide.md](docs/guides/marketing/funnel-guide.md).

## Project Structure

```
app/
├── Domain/           # Business logic layer
│   ├── Subscriber/   # Marketing contacts & services
│   ├── Content/      # CMS pages & navigation
│   ├── Commerce/     # E-commerce entities & services
│   │   ├── Product.php           # Product model (simple, virtual, variable types)
│   │   ├── ProductVariant.php    # Variant model (SKU, price, stock)
│   │   ├── ProductReview.php     # Review model with approval workflow
│   │   ├── Taxonomy.php          # Categories, tags, brands
│   │   ├── Attribute.php         # Product attributes
│   │   ├── AttributeValue.php    # Attribute values
│   │   ├── Order.php             # Order model
│   │   ├── Payment.php           # Payment model
│   │   └── Services/
│   │       ├── ProductPricingService.php      # Price formatting & ranges
│   │       └── ProductAvailabilityService.php # Stock & availability
│   ├── Form/         # Forms, Contracts & events
│   └── Funnel/       # Marketing automation engine
├── Application/      # Application orchestration layer (handlers, commands, results)
│   ├── Form/         # SubmitFormHandler
│   ├── Commerce/     # CreateOrderHandler, ProcessPaymentWebhookHandler
│   ├── Funnel/       # StartFunnelHandler
│   └── Content/      # PublishPageHandler
├── Infrastructure/   # External integrations
├── Http/Controllers/
│   ├── Api/          # Public API controllers (thin, delegate to handlers)
│   └── Admin/        # Admin controllers (previews)
├── Http/Middleware/  # Custom middleware (SetLocale, WebhookIpWhitelist)
├── Listeners/        # Event listeners
├── Observers/        # Model observers (cache invalidation)
└── Filament/         # Admin panel resources
    ├── Resources/
    │   ├── ProductResource/      # Product management with variants & reviews
    │   ├── TaxonomyResource/     # Categories, tags, brands
    │   ├── AttributeResource/    # Product attributes
    │   ├── ProductReviewResource/# Review moderation
    │   ├── OrderResource/        # Order management
    │   └── PaymentResource/      # Payment tracking
    └── Blocks/       # Page Builder blocks (auto-loaded)

config/
└── commerce.php      # Currency settings (code, decimals)

lang/
├── cs/               # Czech translations
│   └── admin.php     # Admin panel labels
└── en/               # English translations
    └── admin.php     # Admin panel labels
```

## Application Layer Architecture

The project uses a clean Application Layer that separates HTTP/UI concerns from business logic.

### Architecture Flow

```
HTTP Request → Controller → Command DTO → Handler → Domain Services → Result DTO → HTTP Response
```

### Handlers

| Handler | Module | Description |
|---------|--------|-------------|
| `SubmitFormHandler` | Form | Processes form submissions, creates contracts (idempotent, transactional) |
| `CreateOrderHandler` | Commerce | Initiates checkout, creates orders (idempotent, transactional) |
| `ProcessPaymentWebhookHandler` | Commerce | Processes payment webhooks, updates order status (idempotent, transactional, signature verified) |
| `StartFunnelHandler` | Funnel | Triggers marketing funnels (prevents duplicate runs) |
| `PublishPageHandler` | Content | Publishes CMS pages |

### Handler Rules

1. Framework-agnostic (no Request/Response objects)
2. Accept only Command DTOs
3. Return only Result DTOs
4. Coordinate domain services and repositories
5. Dispatch domain events when needed

### Domain Services

| Service | Description |
|---------|-------------|
| `ProductPricingService` | Get product price, formatted price, price range for variable products |
| `ProductAvailabilityService` | Check stock availability, get purchasable variants |

### Controller Rules

1. Validate HTTP format only
2. Map Request → Command
3. Call handler
4. Map Result → Response
5. Max 50 lines of code

## Page Builder Blocks

The CMS uses an auto-loading architecture for Filament Builder blocks. Blocks are automatically discovered from `app/Filament/Blocks/` directory.

### Directory Structure

```
app/Filament/Blocks/
├── BaseBlock.php      # Abstract base class (contract)
├── BlockRegistry.php  # Auto-loader with caching
├── HeroBlock.php      # Hero section block
├── TextBlock.php      # Rich text block
├── ImageBlock.php     # Image with caption block
├── CtaBlock.php       # Call-to-action block
└── FormEmbedBlock.php # Embedded form block
```

### Creating a New Block

1. Create a new class in `app/Filament/Blocks/` extending `BaseBlock`
2. Implement the `make()` method returning a `Filament\Forms\Components\Builder\Block`
3. Clear the cache: `php artisan blocks:clear`

```php
<?php

namespace App\Filament\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class MyCustomBlock extends BaseBlock
{
    public static int $order = 60;      // Lower = appears first
    public static bool $enabled = true; // Set false to disable

    public static function make(): Block
    {
        return Block::make('my_custom')
            ->label(__('admin.page.blocks.my_custom'))
            ->icon('heroicon-o-star')
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
            ]);
    }
}
```

### Block Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$order` | int | 100 | Sort order in block picker (lower = first) |
| `$enabled` | bool | true | Whether the block is available |

### Cache Management

Block discovery is cached for performance. Clear the cache when adding or removing blocks:

```bash
php artisan blocks:clear
```

## Media Workflow

The CMS stores media on a private disk and exports public assets at build time.

- **Private disk:** `media` disk at `storage/app/media` via Spatie Media Library
- **Admin management:** `Media` resource in Filament uses the private disk
- **Block usage:** `ImageBlock` and `HeroBlock` store `media_uuid` and the API resolves them to `media` objects
- **RichEditor uploads:** insert `/__media__/{uuid}/original` placeholders and resolve via manifest on API output
- **Export step:** `php artisan media:export` writes `public/media` and `public/media-manifest.json`

## Frontend Routes

| URL | Description |
|-----|-------------|
| `/` | Homepage (renders CMS page with slug `home`) |
| `/{slug}` | CMS page by slug |
| `/products` | Product listing |
| `/products/{id}` | Product detail |
| `/checkout/{productId}` | Checkout form |
| `/thank-you` | Thank you page |
| `/payment/return` | Payment gateway return URL |
| `/lang/{locale}` | Switch language (cs, en) |

## Localization (i18n)

The application supports multiple languages with runtime switching.

### Supported Languages

| Locale | Language |
|--------|----------|
| `cs` | Czech (default) |
| `en` | English |

### Configuration

Add to `.env`:

```env
APP_LOCALE=cs
APP_FALLBACK_LOCALE=en
```

### Language Switching

Switch language via URL: `/lang/cs` or `/lang/en`. The selected language is stored in the session.

### Translation Files

| File | Purpose |
|------|---------|
| `lang/cs/admin.php` | Czech admin panel translations |
| `lang/en/admin.php` | English admin panel translations |

### Translatable Content

Page titles support multiple languages and are stored as JSON in the database:

```json
{
  "cs": "Úvodní stránka",
  "en": "Homepage"
}
```

Use `$page->getLocalizedTitle()` to get the title in the current locale with automatic fallback.

## Production Configuration

For production deployment, configure the following environment variables:

```env
# Commerce settings
CURRENCY_CODE=CZK
CURRENCY_DECIMALS=2

# Webhook IP Whitelist (comma-separated IPs or CIDR ranges)
# Get Stripe IPs from: https://stripe.com/docs/ips
WEBHOOK_IP_WHITELIST=3.18.12.63,3.130.192.231,13.235.14.237,13.235.122.149

# Queue settings (already configured in config/queue.php)
DB_QUEUE_RETRY_AFTER=120
```

### Production Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Configure `WEBHOOK_IP_WHITELIST` with payment gateway IPs
- [ ] Set up queue worker: `php artisan queue:work --tries=3`
- [ ] (Optional) Install Sentry: `composer require sentry/sentry-laravel`
- [ ] (Optional) Install Horizon: `composer require laravel/horizon`

## Astro Frontend Integration

The CMS automatically triggers frontend rebuilds when content changes:

1. **Page saved/deleted** → `TriggerFrontendRebuildJob` dispatched
2. **Navigation updated** → `TriggerFrontendRebuildJob` dispatched
3. **Job calls GitHub API** → `repository_dispatch` event sent
4. **GitHub Actions** → Builds and deploys Astro site

### Configuration

Add to `.env`:

```env
# GitHub Integration
GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxx
GITHUB_FRONTEND_REPOSITORY=zlizlobr/ercee-frontend

# Internal API Token (for manual triggers)
API_INTERNAL_TOKEN=your_secure_random_string

# CORS (allow frontend domain)
CORS_ALLOWED_ORIGINS=https://www.yourdomain.com
```

### Manual Rebuild Trigger

```bash
curl -X POST https://api.yourdomain.com/api/internal/rebuild-frontend \
  -H "Authorization: Bearer your_secure_random_string" \
  -H "Content-Type: application/json" \
  -d '{"reason": "manual"}'
```

For local development setup, see [docs/guides/setup/local-frontend-setup.md](docs/guides/setup/local-frontend-setup.md).

## Documentation

| Document | Description |
|----------|-------------|
| [docs/README.md](docs/README.md) | Documentation index |
| [docs/guides/frontend/preview-frontend-guide.md](docs/guides/frontend/preview-frontend-guide.md) | Admin preview frontend guide |
| [docs/guides/frontend/astro-frontend-guide.md](docs/guides/frontend/astro-frontend-guide.md) | Astro frontend architecture & development |
| [docs/guides/setup/local-frontend-setup.md](docs/guides/setup/local-frontend-setup.md) | Local frontend testing setup |
| [docs/guides/setup/local-backend-setup.md](docs/guides/setup/local-backend-setup.md) | Local backend setup (Laravel API + admin) |
| [docs/guides/commerce/commerce-guide.md](docs/guides/commerce/commerce-guide.md) | Commerce & Checkout developer guide |
| [docs/guides/marketing/funnel-guide.md](docs/guides/marketing/funnel-guide.md) | Marketing Automation developer guide |
| [docs/guides/frontend/frontend-menu-integration.md](docs/guides/frontend/frontend-menu-integration.md) | Frontend navigation/menu integration tasks |
