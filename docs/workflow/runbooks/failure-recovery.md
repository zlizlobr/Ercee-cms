# Workflow Failure Recovery

This runbook defines consistent handling for gate failures.

## Failure Classification

- blocker: must be fixed before next gate.
- major: can proceed with a tracked fix list.
- minor: can proceed with tracked cleanup.

## Recovery Procedure

1. Record failure in gate artifact summary.
2. Re-open or create implementation subtask in Linear.
3. Re-run failed gate only after fixes are merged.
4. Preserve previous evidence to keep audit trail.
