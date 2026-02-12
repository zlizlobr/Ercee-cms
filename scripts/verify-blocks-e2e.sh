#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

FRONTEND_DIR="../ercee-frontend"

bash ./scripts/verify-blocks.sh

echo "[verify:blocks:e2e] running frontend E2E smoke..."
npm --prefix "$FRONTEND_DIR" run verify:blocks:e2e

echo "[verify:blocks:e2e] block E2E checks passed"
