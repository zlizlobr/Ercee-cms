#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

FRONTEND_DIR="../ercee-frontend"

echo "[preflight:blocks] checking CMS environment..."

if ! command -v php >/dev/null 2>&1; then
  echo "[preflight:blocks] php is not available in PATH."
  exit 1
fi

if [ ! -f "artisan" ]; then
  echo "[preflight:blocks] artisan file not found in CMS root."
  exit 1
fi

echo "[preflight:blocks] checking frontend environment..."

if [ ! -d "$FRONTEND_DIR" ]; then
  echo "[preflight:blocks] frontend directory not found: $FRONTEND_DIR"
  exit 1
fi

if ! command -v npm >/dev/null 2>&1; then
  echo "[preflight:blocks] npm is not available in PATH."
  exit 1
fi

npm --prefix "$FRONTEND_DIR" run preflight:blocks

echo "[preflight:blocks] OK"
