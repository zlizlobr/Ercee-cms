#!/usr/bin/env bash
set -euo pipefail

# Production deploy script for Ercee CMS
# Usage: ./scripts/deploy.sh [--fresh]
#
# Expects:
#   - PHP 8.3+, Composer, Node.js 20+
#   - .env configured for production
#   - Web server (nginx/Apache) pointing to public/

FRESH=false
if [[ "${1:-}" == "--fresh" ]]; then
    FRESH=true
fi

echo "==> Installing Composer dependencies (production)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Installing Node dependencies"
npm ci --omit=dev

echo "==> Building frontend assets"
npm run build

echo "==> Running migrations"
if $FRESH; then
    php artisan migrate --force
    php artisan db:seed --force
else
    php artisan migrate --force
fi

echo "==> Caching configuration"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache

echo "==> Clearing old caches"
php artisan cache:clear

echo "==> Restarting queue workers"
php artisan queue:restart

echo "==> Deploy complete"
php artisan about --only=Environment
