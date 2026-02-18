#!/usr/bin/env bash
set -euo pipefail

usage() {
  cat <<'USAGE'
Usage:
  scripts/workflow/install-module-ci-template.sh \
    --repo-path /absolute/path/to/module-repo \
    --module-name commerce \
    --module-namespace Modules\\Commerce \
    --cms-repo zlizlobr/Ercee-cms

What it does:
  - Copies .github/workflows/module-ci.yml.template into target repo as .github/workflows/ci.yml
  - Replaces placeholders {{MODULE_NAME}}, {{MODULE_NAMESPACE}}, and your-org/ercee-cms
  - Creates backup if target file already exists
USAGE
}

REPO_PATH=""
MODULE_NAME=""
MODULE_NAMESPACE=""
CMS_REPO=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --repo-path)
      REPO_PATH="${2:-}"
      shift 2
      ;;
    --module-name)
      MODULE_NAME="${2:-}"
      shift 2
      ;;
    --module-namespace)
      MODULE_NAMESPACE="${2:-}"
      shift 2
      ;;
    --cms-repo)
      CMS_REPO="${2:-}"
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

if [[ -z "$REPO_PATH" || -z "$MODULE_NAME" || -z "$MODULE_NAMESPACE" || -z "$CMS_REPO" ]]; then
  echo "Missing required arguments." >&2
  usage
  exit 1
fi

if [[ ! -d "$REPO_PATH" ]]; then
  echo "Repository path not found: $REPO_PATH" >&2
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
TEMPLATE="$ROOT_DIR/.github/workflows/module-ci.yml.template"
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

python3 - "$TARGET_FILE" "$MODULE_NAME" "$MODULE_NAMESPACE" "$CMS_REPO" <<'PY'
from pathlib import Path
import sys

target = Path(sys.argv[1])
module_name = sys.argv[2]
module_namespace = sys.argv[3]
cms_repo = sys.argv[4]

text = target.read_text(encoding="utf-8")
text = text.replace("{{MODULE_NAME}}", module_name)
text = text.replace("{{MODULE_NAMESPACE}}", module_namespace)
text = text.replace("your-org/ercee-cms", cms_repo)
target.write_text(text, encoding="utf-8")
PY

echo "Installed module CI workflow to: $TARGET_FILE"
