# Branch Protection Automation

Use this script to apply branch protection for workflow gates via GitHub API.

## Script

- `scripts/workflow/apply-branch-protection.sh`

## Prerequisites

1. GitHub token with repository admin permissions.
2. Export token:
   - `export GITHUB_TOKEN=...`

## Dry Run

1. Preview payloads:
   - `scripts/workflow/apply-branch-protection.sh --repo zlizlobr/Ercee-cms --dry-run`

## Apply Settings

1. Apply to `main` and `develop`:
   - `scripts/workflow/apply-branch-protection.sh --repo zlizlobr/Ercee-cms`
2. If `develop` does not exist, script skips it and still succeeds.

## What It Enforces

1. PR required before merge.
2. At least one approval required.
3. Stale reviews dismissed on push.
4. Conversation resolution required.
5. Force-push and deletion blocked.
6. Up-to-date branch required (`strict` status checks).
7. Required checks:
   - `Gate 1 - Spec and Plan`
   - `Gate 3/4 - Test and Ralph Review Checks`
   - `Gate 5/6 - Docs and Release Readiness Checks`
   - `Code Quality (Pint & PHPStan)`
   - `PHP 8.3 - Laravel 12.*`

## CI Speed Mode

1. PR path (fast gate):
   - runs `Code Quality (Pint & PHPStan)` + `PHP 8.3 - Laravel 12.*`
2. Push path (`main`/`develop` hardening):
   - also runs `PHP 8.4 - Laravel 12.*`

## Verification

1. Open test PR into `main`.
2. Confirm merge blocked while required checks/review are missing.
3. Confirm merge allowed only when all required checks are green.
