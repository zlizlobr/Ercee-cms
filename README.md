# Ercee CMS

Laravel-based CMS platform.

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- Redis
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

### 5. Create SQLite database

```bash
touch database/database.sqlite
php artisan migrate
```

### 6. Start services

```bash
brew services start redis
brew services start mailpit
```

- Redis runs on `127.0.0.1:6379`
- Mailpit UI: `http://localhost:8025`
- Mailpit SMTP: `127.0.0.1:1025`

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
