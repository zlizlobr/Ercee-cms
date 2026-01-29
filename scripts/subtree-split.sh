#!/usr/bin/env bash
set -euo pipefail

# Git subtree split script for extracting modules into standalone repos.
#
# Usage:
#   ./scripts/subtree-split.sh <module> [remote-url]
#
# Examples:
#   ./scripts/subtree-split.sh forms
#   ./scripts/subtree-split.sh commerce git@github.com:your-org/ercee-module-commerce.git

MODULE="${1:?Usage: subtree-split.sh <module> [remote-url]}"
REMOTE_URL="${2:-}"
PREFIX="modules/${MODULE}"
BRANCH="module-${MODULE}-split"

if [ ! -d "$PREFIX" ]; then
    echo "Error: Module directory '${PREFIX}' does not exist."
    exit 1
fi

echo "==> Splitting subtree for module: ${MODULE}"
echo "    prefix: ${PREFIX}"
echo "    branch: ${BRANCH}"

git subtree split --prefix="${PREFIX}" -b "${BRANCH}"

echo "==> Subtree split complete. Branch: ${BRANCH}"

if [ -n "$REMOTE_URL" ]; then
    REMOTE_NAME="module-${MODULE}"

    if ! git remote get-url "${REMOTE_NAME}" &>/dev/null; then
        echo "==> Adding remote: ${REMOTE_NAME} -> ${REMOTE_URL}"
        git remote add "${REMOTE_NAME}" "${REMOTE_URL}"
    fi

    echo "==> Pushing to ${REMOTE_NAME}..."
    git push "${REMOTE_NAME}" "${BRANCH}:main" --force

    VERSION=$(grep -o '"version": *"[^"]*"' "${PREFIX}/composer.json" | head -1 | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+')
    if [ -n "$VERSION" ]; then
        echo "==> Tagging v${VERSION}"
        git tag "v${VERSION}" "${BRANCH}" 2>/dev/null || echo "    Tag v${VERSION} already exists, skipping"
        git push "${REMOTE_NAME}" "v${VERSION}" 2>/dev/null || echo "    Tag already pushed, skipping"
    fi

    echo "==> Done! Module pushed to ${REMOTE_URL}"
else
    echo ""
    echo "To push to a remote repository, run:"
    echo "  git remote add module-${MODULE} <url>"
    echo "  git push module-${MODULE} ${BRANCH}:main"
fi
