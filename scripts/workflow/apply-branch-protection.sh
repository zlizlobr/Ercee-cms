#!/usr/bin/env bash
set -euo pipefail

usage() {
  cat <<'EOF'
Usage:
  scripts/workflow/apply-branch-protection.sh --repo owner/name [--dry-run] [--branches main,develop]

Required:
  --repo               GitHub repository in form owner/name
  GITHUB_TOKEN         GitHub token with repository admin permissions

Optional:
  --dry-run            Print payloads without calling GitHub API
  --branches           Comma-separated branch list (default: main,develop)

Behavior:
  - Applies branch protection to main and develop
  - main: requires code-owner review
  - develop: does not require code-owner review
EOF
}

REPO=""
DRY_RUN="false"
BRANCHES_CSV="main,develop"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --repo)
      REPO="${2:-}"
      shift 2
      ;;
    --dry-run)
      DRY_RUN="true"
      shift
      ;;
    --branches)
      BRANCHES_CSV="${2:-}"
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

if [[ -z "$REPO" ]]; then
  echo "Missing --repo argument." >&2
  usage
  exit 1
fi

if [[ "${GITHUB_TOKEN:-}" == "" && "$DRY_RUN" != "true" ]]; then
  echo "Missing GITHUB_TOKEN environment variable." >&2
  exit 1
fi

api_url() {
  local branch="$1"
  printf 'https://api.github.com/repos/%s/branches/%s/protection' "$REPO" "$branch"
}

build_payload() {
  local require_codeowners="$1"
  cat <<EOF
{
  "required_status_checks": {
    "strict": true,
    "contexts": [
      "Gate 1 - Spec and Plan",
      "Gate 3/4 - Test and Ralph Review Checks",
      "Gate 5/6 - Docs and Release Readiness Checks",
      "Code Quality (Pint & PHPStan)",
      "PHP 8.3 - Laravel 12.*"
    ]
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "dismiss_stale_reviews": true,
    "require_code_owner_reviews": ${require_codeowners},
    "required_approving_review_count": 1,
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
EOF
}

apply_branch() {
  local branch="$1"
  local require_codeowners="$2"
  local payload
  payload="$(build_payload "$require_codeowners")"

  echo "Applying branch protection for ${REPO}:${branch}"

  if [[ "$DRY_RUN" == "true" ]]; then
    echo "Dry run mode. Payload:"
    echo "$payload"
    echo
    return 0
  fi

  local status
  status="$(curl -sS -o /tmp/branch-protection-response.json -w '%{http_code}' \
    -X PUT "$(api_url "$branch")" \
    -H "Accept: application/vnd.github+json" \
    -H "Authorization: Bearer ${GITHUB_TOKEN}" \
    -H "X-GitHub-Api-Version: 2022-11-28" \
    -d "$payload")"

  if [[ "$status" != "200" ]]; then
    if [[ "$status" == "404" ]]; then
      echo "WARN: ${branch} not found on remote, skipping."
      return 0
    fi
    echo "GitHub API failed for ${branch} (HTTP ${status})." >&2
    cat /tmp/branch-protection-response.json >&2
    exit 1
  fi

  echo "OK: ${branch} updated."
}

IFS=',' read -r -a branches <<< "$BRANCHES_CSV"
for branch in "${branches[@]}"; do
  branch="$(echo "$branch" | xargs)"
  [[ -z "$branch" ]] && continue

  if [[ "$branch" == "main" ]]; then
    apply_branch "$branch" "true"
  else
    apply_branch "$branch" "false"
  fi
done

echo "Branch protection applied for ${REPO}."
