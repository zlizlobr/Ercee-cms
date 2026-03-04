#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

SKIP_INSTALL=0
RUN_TESTS=1
RUN_CHANGED_ONLY=1

# Ensure common local package-manager binaries are reachable in non-interactive shells.
export PATH="/usr/local/bin:/opt/homebrew/bin:$PATH"

usage() {
  cat <<'EOF'
Usage: bash ./scripts/run-local-ci.sh [--skip-install] [--skip-tests] [--all-php]

Runs the local equivalent of the main GitHub Actions CI jobs:
1. composer install
2. Pint (changed PHP files only, like CI)
3. PHPStan (changed PHP files only, like CI)
4. Root test suites (Unit + Feature)

Options:
  --skip-install Skip composer install (use existing vendor/).
  --skip-tests   Skip the test phase.
  --all-php      Run Pint and PHPStan against all tracked PHP files.
  -h, --help     Show this help.
EOF
}

fail() {
  echo "[local-ci] $1" >&2
  exit 1
}

require_command() {
  local command_name="$1"

  if ! command -v "$command_name" >/dev/null 2>&1; then
    fail "missing required command: $command_name"
  fi
}

require_file() {
  local path="$1"

  if [[ ! -x "$path" ]]; then
    fail "missing executable: $path"
  fi
}

run_step() {
  local label="$1"
  shift

  printf '\n==> %s\n' "$label"
  "$@"
}

collect_changed_php_files() {
  local output_file="$1"

  git diff --name-only --diff-filter=ACMR -- '*.php' > "$output_file" || true
  git diff --cached --name-only --diff-filter=ACMR -- '*.php' >> "$output_file" || true
  git ls-files --others --exclude-standard -- '*.php' >> "$output_file" || true
  sort -u -o "$output_file" "$output_file"
}

collect_all_php_files() {
  local output_file="$1"

  git ls-files '*.php' > "$output_file"
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --skip-install)
      SKIP_INSTALL=1
      shift
      ;;
    --skip-tests)
      RUN_TESTS=0
      shift
      ;;
    --all-php)
      RUN_CHANGED_ONLY=0
      shift
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "[local-ci] unknown option: $1" >&2
      usage >&2
      exit 1
      ;;
  esac
done

require_command git
require_command php
require_command composer

if [[ "$SKIP_INSTALL" -eq 0 ]]; then
  run_step "Install Composer dependencies" composer install --prefer-dist --no-interaction --no-progress --ansi
else
  echo "[local-ci] skipping composer install"
fi

require_file "$ROOT_DIR/vendor/bin/pint"
require_file "$ROOT_DIR/vendor/bin/phpstan"

changed_php_file_list="$(mktemp)"
cleanup() {
  rm -f "$changed_php_file_list"
}
trap cleanup EXIT

if [[ "$RUN_CHANGED_ONLY" -eq 1 ]]; then
  collect_changed_php_files "$changed_php_file_list"
else
  collect_all_php_files "$changed_php_file_list"
fi

changed_count="$(wc -l < "$changed_php_file_list" | tr -d ' ')"

if [[ "$changed_count" -gt 0 ]]; then
  echo "[local-ci] resolved ${changed_count} PHP file(s) for quality checks"
  run_step "Code style check (Pint)" xargs "$ROOT_DIR/vendor/bin/pint" --test --ansi < "$changed_php_file_list"
  run_step "Static analysis (PHPStan)" xargs "$ROOT_DIR/vendor/bin/phpstan" analyse --memory-limit=2G --ansi < "$changed_php_file_list"
else
  echo "[local-ci] no PHP files selected for quality checks; skipping Pint and PHPStan"
fi

if [[ "$RUN_TESTS" -eq 1 ]]; then
  [[ -f "$ROOT_DIR/database/database.sqlite" ]] || fail "source DB not found: $ROOT_DIR/database/database.sqlite"
  run_step "Root test suites (Unit + Feature)" bash ./scripts/test-safe.sh --ansi --testsuite=Unit,Feature
else
  echo "[local-ci] skipping tests"
fi

printf '\n[local-ci] all checks passed\n'
