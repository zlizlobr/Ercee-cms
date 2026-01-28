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

# Commerce module

git add app/Application/Commerce app/Domain/Commerce modules/commerce
commit_if_staged "refactor(modules): extract commerce module"

# Funnel module

git add app/Application/Funnel app/Domain/Funnel modules/funnel
commit_if_staged "refactor(modules): extract funnel module"

# Forms module + related events

git add app/Domain/Form app/Domain/Content/Events app/Domain/Media/Events modules/forms
commit_if_staged "refactor(modules): extract forms module"

# Docs + notes

git add docs/refactor-task-list.md git-worktree-llm-flow.md
commit_if_staged "docs: update refactor notes"

# Any remaining changes

git add -A
commit_if_staged "refactor: remaining cleanup"

git push -u origin "$BRANCH"
