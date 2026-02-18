#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_DB="$ROOT_DIR/database/database.sqlite"
TEST_DB_DIR="$ROOT_DIR/storage/testing"
TEST_DB="$TEST_DB_DIR/database.sqlite"
TEST_CONFIG_CACHE="$TEST_DB_DIR/config.php"

if [ ! -f "$SOURCE_DB" ]; then
  echo "Source DB not found: $SOURCE_DB" >&2
  exit 1
fi

mkdir -p "$TEST_DB_DIR"
cp "$SOURCE_DB" "$TEST_DB"

cleanup() {
  rm -f "$TEST_DB"
  rm -f "$TEST_CONFIG_CACHE"
}
trap cleanup EXIT

DB_CONNECTION=sqlite \
DB_DATABASE="$TEST_DB" \
APP_CONFIG_CACHE="$TEST_CONFIG_CACHE" \
php artisan test "$@"
