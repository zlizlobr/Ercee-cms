#!/usr/bin/env bash
set -euo pipefail

usage() {
  cat <<'USAGE'
Usage:
  scripts/workflow/apply-module-frontend-branch-protection.sh \
    --repo owner/name \
    --type module|frontend \
    [--branch main] [--approvals 1] [--codeowners true|false] [--dry-run]

Required:
  --repo       GitHub repository in form owner/name
  --type       module or frontend
  GITHUB_TOKEN GitHub token with repo admin permission (unless --dry-run)

Examples:
  scripts/workflow/apply-module-frontend-branch-protection.sh --repo zlizlobr/ercee-module-commerce --type module
  scripts/workflow/apply-module-frontend-branch-protection.sh --repo zlizlobr/ercee-frontend --type frontend --approvals 1
USAGE
}

REPO=""
TYPE=""
BRANCH="main"
APPROVALS="1"
CODEOWNERS="false"
DRY_RUN="false"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --repo)
      REPO="${2:-}"
      shift 2
      ;;
    --type)
      TYPE="${2:-}"
      shift 2
      ;;
    --branch)
      BRANCH="${2:-}"
      shift 2
      ;;
    --approvals)
      APPROVALS="${2:-}"
      shift 2
      ;;
    --codeowners)
      CODEOWNERS="${2:-}"
      shift 2
      ;;
    --dry-run)
      DRY_RUN="true"
      shift
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

if [[ -z "$REPO" || -z "$TYPE" ]]; then
  echo "Missing required arguments." >&2
  usage
  exit 1
fi

if [[ "$TYPE" != "module" && "$TYPE" != "frontend" ]]; then
  echo "--type must be module or frontend" >&2
  exit 1
fi

if [[ "$CODEOWNERS" != "true" && "$CODEOWNERS" != "false" ]]; then
  echo "--codeowners must be true or false" >&2
  exit 1
fi

if [[ "$DRY_RUN" != "true" && -z "${GITHUB_TOKEN:-}" ]]; then
  echo "Missing GITHUB_TOKEN." >&2
  exit 1
fi

if [[ "$TYPE" == "module" ]]; then
  CHECK1='Module Only / Code Quality (required)'
  CHECK2='Module Only / Tests PHP 8.3 (required)'
else
  CHECK1='Frontend / Quality + Tests (required)'
  CHECK2='Frontend / Data Contract Check (required)'
fi

PAYLOAD=$(cat <<JSON
{
  "required_status_checks": {
    "strict": true,
    "contexts": [
      "$CHECK1",
      "$CHECK2"
    ]
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "dismiss_stale_reviews": true,
    "require_code_owner_reviews": $CODEOWNERS,
    "required_approving_review_count": $APPROVALS,
    "require_last_push_approval": false
  },
  "restrictions": null,
  "required_linear_history": false,
  "allow_force_pushes": false,
  "allow_deletions": false,
  "block_creations": false,
  "required_conversation_resolution": true,
  "lock_branch": false,
  "allow_fork_syncing": true
}
JSON
)

URL="https://api.github.com/repos/${REPO}/branches/${BRANCH}/protection"

echo "Applying protection for ${REPO}:${BRANCH} (type=${TYPE})"

if [[ "$DRY_RUN" == "true" ]]; then
  echo "$PAYLOAD"
  exit 0
fi

STATUS=$(curl -sS -o /tmp/module-frontend-branch-protection.json -w '%{http_code}' \
  -X PUT "$URL" \
  -H "Accept: application/vnd.github+json" \
  -H "Authorization: Bearer ${GITHUB_TOKEN}" \
  -H "X-GitHub-Api-Version: 2022-11-28" \
  -d "$PAYLOAD")

if [[ "$STATUS" != "200" ]]; then
  echo "GitHub API failed (HTTP $STATUS)." >&2
  cat /tmp/module-frontend-branch-protection.json >&2
  exit 1
fi

echo "OK: branch protection updated for ${REPO}:${BRANCH}"
