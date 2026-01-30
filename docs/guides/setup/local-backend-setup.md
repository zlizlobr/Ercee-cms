# Local Backend Development Setup

This guide covers running the Laravel CMS locally for API and admin work.

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- SQLite 3 (built into macOS)
- Mailpit (optional, for email testing)
- Redis (optional, for cache/queue)

## macOS setup

### 1. Install Homebrew (if needed)

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

Default admin user:
- Email: `admin@example.com`
- Password: `password`

### 5. Configure payments (optional)

If you need Stripe payments or webhooks locally, follow the Commerce guide:
`docs/guides/commerce/commerce-guide.md`.

### 6. Start services (optional)

```bash
brew services start mailpit
# Only if using Redis for cache/queue:
brew services start redis
```

- Mailpit UI: `http://localhost:8025`
- Mailpit SMTP: `127.0.0.1:1025`
- Redis (optional): `127.0.0.1:6379`

> Note: By default, the app uses database drivers for cache and queue. Redis is optional.

## Run the app

### Start the API and admin server

```bash
php artisan serve
```

The application is available at `http://localhost:8000`.

### Start the queue worker (optional)

```bash
php artisan queue:work
```

### Use the full dev environment

```bash
composer dev
```
