# Docs Editor Agent Runbook

Use this runbook for Docs Gate enforcement.

## Steps

1. Update docs reflecting implemented behavior.
2. Run docs standards and canonical link checks.
3. Add changelog note for release-relevant changes.
4. Save docs evidence under `artifacts/gates/<initiative-id>/docs-gate/`.
5. Hand off to Release Readiness.

## Failure Recovery

- If canonical links or doc standards fail, block gate until fixed.
