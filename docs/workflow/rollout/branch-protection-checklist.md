# Branch Protection Checklist

Use this checklist to complete the remaining branch-protection task outside repository files.

## Target Branches

- `main`
- `develop` (if branch exists in repository)

## Required Settings

- Require pull request before merging.
- Require status checks to pass before merging.
- Require branches to be up to date before merging.
- Restrict direct pushes (except allowed maintainers, if needed).

## Required Status Checks

Enable at minimum:

- `Gate 1 - Spec and Plan`
- `Gate 3/4 - Test and Ralph Review Checks`
- `Gate 5/6 - Docs and Release Readiness Checks`
- Existing core CI checks (`CI Tests` jobs) as applicable.

## Verification

- Open a test PR and confirm merge is blocked when any required check fails.
- Confirm merge is allowed only after all required checks are green.
