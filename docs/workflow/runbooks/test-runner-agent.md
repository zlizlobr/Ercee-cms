# Test Runner Agent Runbook

Use this runbook to execute mandatory test flow evidence for Gate 3.

## Steps

1. Run A: preflight + verify checks.
2. Run B: unit + contract checks.
3. Run C: runtime smoke/e2e when release/UI critical.
4. Save outputs in `artifacts/gates/<initiative-id>/test-gate/`.
5. Hand off to `review-agent`.

## Failure Recovery

- On any failed stage, report blocker and return task to Implementace gate.
