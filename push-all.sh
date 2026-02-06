#!/usr/bin/env bash
set -euo pipefail

REPO="/usr/local/var/www/Ercee-cms"
BRANCH="feature/refactor-modules-structure"

cd "$REPO"

git status -sb

commit_if_staged() {
  local msg="$1"
  if ! git diff --cached --quiet; then
    git commit -m "$msg"
  else
    echo "Nothing staged for: $msg"
  fi
}

# Core module infrastructure

git add app/Contracts \
  app/Providers/ModuleServiceProvider.php \
  app/Support/Module \
  bootstrap/providers.php \
  composer.json \
  config/modules.php
commit_if_staged "refactor(modules): add core module infrastructure"

# Commerce module is maintained in external repo now (no staging here).
