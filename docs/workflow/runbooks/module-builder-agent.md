# Module Builder Agent Runbook

Use this runbook when changes affect module boundaries, provider registration, or module lifecycle.

## Steps

1. Confirm gate status is `Spec and Plan` approved.
2. Implement scoped module changes.
3. If a module adds or changes seeders, enforce JSON seeder pattern:
   - seeder reads from `storage/app/seed-data/*.json` (or explicit env override)
   - invalid/missing JSON logs warning and exits safely
   - no hardcoded seed payload arrays in seeder classes
4. Update module docs and CMS docs for seeder path/format changes.
5. Produce handoff payload from `docs/templates/agent-output.md`.
6. Hand off to `test-runner-agent`.

## Failure Recovery

- On blocker review findings, return to Implementace gate with explicit fix list.
