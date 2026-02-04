#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_DB="$ROOT_DIR/database/database.sqlite"
TEST_DB_DIR="$ROOT_DIR/storage/testing"
TEST_DB="$TEST_DB_DIR/database.sqlite"

if [ ! -f "$SOURCE_DB" ]; then
  echo "Source DB not found: $SOURCE_DB" >&2
  exit 1
fi

mkdir -p "$TEST_DB_DIR"
cp "$SOURCE_DB" "$TEST_DB"

cleanup() {
  rm -f "$TEST_DB"
}
trap cleanup EXIT

DB_CONNECTION=sqlite \
DB_DATABASE="$TEST_DB" \
php artisan test "$@"
