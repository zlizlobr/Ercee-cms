#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "[verify:backend-admin:e2e] ensuring forms schema needed by admin dashboard..."
php artisan migrate --path=modules/forms/database/migrations/2026_02_06_000002_create_contracts_table.php --force
php artisan migrate --path=modules/forms/database/migrations/2026_02_10_000001_add_draft_token_to_contracts_table.php --force
php artisan migrate --path=modules/forms/database/migrations/2026_02_11_000001_make_subscriber_id_nullable_on_contracts_table.php --force

echo "[verify:backend-admin:e2e] seeding admin auth prerequisites..."
php artisan db:seed --class='Database\Seeders\RolesAndPermissionsSeeder'
php artisan db:seed --class='Database\Seeders\AdminUserSeeder'

echo "[verify:backend-admin:e2e] running CMS-layer backend admin login E2E..."
npm run test:e2e:admin

echo "[verify:backend-admin:e2e] backend admin login E2E passed"
