#!/usr/bin/env bash
set -euo pipefail

usage() {
  cat <<'USAGE'
Usage:
  scripts/workflow/install-frontend-ci-template.sh \
    --repo-path /absolute/path/to/ercee-frontend

What it does:
  - Copies .github/workflows/frontend-ci.yml.template into target repo as .github/workflows/ci.yml
  - Creates backup if target file already exists
USAGE
}

REPO_PATH=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --repo-path)
      REPO_PATH="${2:-}"
      shift 2
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Unknown argument: $1" >&2
      usage
      exit 1
      ;;
  esac
done

if [[ -z "$REPO_PATH" ]]; then
  echo "Missing --repo-path argument." >&2
  usage
  exit 1
fi

if [[ ! -d "$REPO_PATH" ]]; then
  echo "Repository path not found: $REPO_PATH" >&2
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
TEMPLATE="$ROOT_DIR/.github/workflows/frontend-ci.yml.template"
TARGET_DIR="$REPO_PATH/.github/workflows"
TARGET_FILE="$TARGET_DIR/ci.yml"

if [[ ! -f "$TEMPLATE" ]]; then
  echo "Template not found: $TEMPLATE" >&2
  exit 1
fi

mkdir -p "$TARGET_DIR"

if [[ -f "$TARGET_FILE" ]]; then
  cp "$TARGET_FILE" "$TARGET_FILE.bak.$(date +%Y%m%d%H%M%S)"
fi

cp "$TEMPLATE" "$TARGET_FILE"

echo "Installed frontend CI workflow to: $TARGET_FILE"
