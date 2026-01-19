# Ercee CMS

Laravel-based headless CMS platform with Filament admin panel.

## Features

- **Filament Admin Panel** - Modern backoffice UI at `/admin`
- **Role-Based Access Control** - Using spatie/laravel-permission (admin, operator, marketing roles)
- **Domain-Driven Design** - Clean architecture with Domain, Application, Infrastructure layers
- **Headless CMS** - Block-based page builder with public REST API
- **Frontend MVP** - Blade + Tailwind templates for content pages, products, checkout
- **Core Entities**:
  - **Subscribers** - Marketing contact management
  - **Pages** - Block-based content with SEO support (text, image, CTA, form embed blocks)
  - **Products** - Lightweight commerce entities
  - **Orders** - Order tracking with payment integration
  - **Payments** - Multi-gateway payment processing (Stripe, GoPay, Comgate)
  - **Navigation** - Hierarchical site navigation management
  - **Forms** - Dynamic form builder with schema validation
  - **Contracts** - Lead capture and form submissions
- **Lead Capture System** - Public form submission API with anti-spam protection
- **Marketing Automation** - Funnel engine with multi-step workflows triggered by events
- **Lightweight Commerce** - Simple checkout with Stripe integration

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- Redis (optional - can use database driver instead)
- SQLite 3 (built into macOS)
- Mailpit (for email testing)

## Local Development Setup (macOS)

### 1. Install Homebrew (if not installed)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2. Install required packages

```bash
brew install php@8.3 composer node redis mailpit
```

### 3. Clone and setup the project

```bash
git clone <repository-url> ercee-cms
cd ercee-cms
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 4. Create SQLite database and seed

```bash
touch database/database.sqlite
php artisan migrate --seed
```

This creates the default admin user:
- **Email:** `admin@example.com`
- **Password:** `password`

### 5. Configure Stripe (for payments)

Add to your `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_CURRENCY=czk
STRIPE_SUCCESS_URL=http://localhost:8000/payment/return
STRIPE_CANCEL_URL=http://localhost:8000/products
```

For local webhook testing, use Stripe CLI:
```bash
brew install stripe/stripe-cli/stripe
stripe login
stripe listen --forward-to localhost:8000/api/webhooks/stripe
```

### 6. Start services (optional)

```bash
brew services start mailpit
# Only if using Redis for cache/queue:
brew services start redis
```

- Mailpit UI: `http://localhost:8025`
- Mailpit SMTP: `127.0.0.1:1025`
- Redis (optional): `127.0.0.1:6379`

> **Note:** By default, the app uses database driver for cache and queue. Redis is optional.

### 7. Start development server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

Or use the full dev environment with queue worker and Vite:
```bash
composer dev
```

## Available Commands

```bash
# Run tests
php artisan test

# Run linter
composer lint

# Run static analysis
composer analyse

# Start queue worker
php artisan queue:work

# Build frontend assets
npm run build

# Watch frontend assets
npm run dev
```

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

## Public API

The CMS exposes a REST API for frontend consumption.

### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/pages/{slug}` | Get published page by slug with blocks and SEO |
| GET | `/api/v1/navigation` | Get hierarchical navigation structure |
| GET | `/api/v1/products` | Get active products list |
| GET | `/api/v1/products/{id}` | Get single product detail |
| GET | `/api/v1/forms/{id}` | Get form schema for rendering |
| POST | `/api/v1/forms/{id}/submit` | Submit form data (rate limited: 5/min per IP) |
| POST | `/api/v1/checkout` | Initiate checkout (rate limited: 10/min per IP) |

### Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/webhooks/stripe` | Stripe payment webhook |

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

### Block Types

| Type | Fields |
|------|--------|
| `text` | heading, body |
| `image` | image, alt, caption |
| `cta` | title, description, button_text, button_url, style |
| `form_embed` | form_id, title, description |

### Form Submission

```bash
curl -X POST http://localhost:8000/api/v1/forms/1/submit \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "first_name": "John"}'
```

**Response:**
```json
{
  "message": "Form submitted successfully.",
  "data": {
    "contract_id": 1
  }
}
```

**Anti-spam protection:**
- Rate limiting: 5 requests per minute per IP
- Honeypot field: include `_hp_field` (must be empty)

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
  "message": "Checkout initiated",
  "data": {
    "order_id": 1,
    "redirect_url": "https://checkout.stripe.com/..."
  }
}
```

**Rate limiting:** 10 requests per minute per IP

## Commerce

The platform includes a lightweight commerce layer for selling 1-3 products.

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

For more details, see [docs/commerce-guide.md](docs/commerce-guide.md).

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

For more details, see [docs/funnel-guide.md](docs/funnel-guide.md).

## Project Structure

```
app/
├── Domain/           # Business logic layer
│   ├── Subscriber/   # Marketing contacts & services
│   ├── Content/      # CMS pages & navigation
│   ├── Commerce/     # Products, Orders, Payments & Gateways
│   ├── Form/         # Forms, Contracts & events
│   └── Funnel/       # Marketing automation engine
├── Application/      # Application orchestration layer (handlers, commands, results)
│   ├── Form/         # SubmitFormHandler
│   ├── Commerce/     # CreateOrderHandler, ProcessPaymentWebhookHandler
│   ├── Funnel/       # StartFunnelHandler
│   └── Content/      # PublishPageHandler
├── Infrastructure/   # External integrations
├── Http/Controllers/Api/  # Public API controllers (thin, delegate to handlers)
├── Listeners/        # Event listeners
├── Observers/        # Model observers (cache invalidation)
└── Filament/         # Admin panel resources
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
| `SubmitFormHandler` | Form | Processes form submissions, creates contracts |
| `CreateOrderHandler` | Commerce | Initiates checkout, creates orders |
| `ProcessPaymentWebhookHandler` | Commerce | Processes payment webhooks, updates order status |
| `StartFunnelHandler` | Funnel | Triggers marketing funnels |
| `PublishPageHandler` | Content | Publishes CMS pages |

### Handler Rules

1. Framework-agnostic (no Request/Response objects)
2. Accept only Command DTOs
3. Return only Result DTOs
4. Coordinate domain services and repositories
5. Dispatch domain events when needed

### Controller Rules

1. Validate HTTP format only
2. Map Request → Command
3. Call handler
4. Map Result → Response
5. Max 50 lines of code

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

## Documentation

| Document | Description |
|----------|-------------|
| [docs/frontend-guide.md](docs/frontend-guide.md) | Frontend development guide |
| [docs/commerce-guide.md](docs/commerce-guide.md) | Commerce & Checkout developer guide |
| [docs/funnel-guide.md](docs/funnel-guide.md) | Marketing Automation developer guide |
