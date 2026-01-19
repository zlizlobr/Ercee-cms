# Ercee CMS

Laravel-based CMS platform with Filament admin panel.

## Features

- **Filament Admin Panel** - Modern backoffice UI at `/admin`
- **Role-Based Access Control** - Using spatie/laravel-permission (admin, operator, marketing roles)
- **Domain-Driven Design** - Clean architecture with Domain, Application, Infrastructure layers
- **Core Entities**:
  - **Subscribers** - Marketing contact management
  - **Pages** - CMS content with SEO support
  - **Products** - Lightweight commerce entities

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

### 5. Start services (optional)

```bash
brew services start mailpit
# Only if using Redis for cache/queue:
brew services start redis
```

- Mailpit UI: `http://localhost:8025`
- Mailpit SMTP: `127.0.0.1:1025`
- Redis (optional): `127.0.0.1:6379`

> **Note:** By default, the app uses database driver for cache and queue. Redis is optional.

### 6. Start development server

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

## Project Structure

```
app/
├── Domain/           # Business logic layer
│   ├── Subscriber/   # Marketing contacts
│   ├── Content/      # CMS pages
│   ├── Commerce/     # Products
│   └── Funnel/       # (future) Sales funnels
├── Application/      # Application services
├── Infrastructure/   # External integrations
└── Filament/         # Admin panel resources
```
