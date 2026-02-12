#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

FRONTEND_DIR="../ercee-frontend"

bash ./scripts/preflight-blocks.sh

echo "[verify:blocks] checking artisan commands..."
php artisan list | grep -q "make:cms-block"
php artisan list | grep -q "blocks:clear"

echo "[verify:blocks] running frontend verification..."
npm --prefix "$FRONTEND_DIR" run verify:blocks

echo "[verify:blocks] block checks passed"
