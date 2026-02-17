# Pilot #1 Module Change Playbook

This playbook defines how to execute the first real workflow pilot on a module-level change.

## Goal

Validate end-to-end workflow execution (Spec -> Release Readiness) on a real module change with full evidence.

## Linear Mapping

- Parent initiative: `local-c94aa39b` (`Workflow rollout v1 pilots`)
- Pilot evidence subtask: `local-9e10f998` (`Workflow rollout v1 pilots - Pilot #1 module change evidence`)

Use these standard gate subtasks from the same parent:

- `local-9a9bdbbc` Spec/Plan
- `local-3d8a326b` Implementace
- `local-1407f835` Test Gate
- `local-24901a73` Ralph Review Gate (agent-only)
- `local-fbd404aa` Docs Gate
- `local-12b5de0d` Release Readiness

## Scope Definition

Pick one real module change with controlled risk:

- module service/provider wiring adjustment, or
- additive module feature with no broad cross-module refactor.

Avoid for pilot #1:

- payment/auth critical changes
- multi-module breaking refactors

## Evidence Location

Use this initiative folder:

- `artifacts/gates/pilot-1-module-change/`

Required gate files:

- `spec-plan/summary.md`
- `implementace/summary.md`
- `implementace/agent-output.json`
- `test-gate/summary.md`
- `test-gate/test-results.txt`
- `ralph-review/summary.md`
- `ralph-review/review-findings.json`
- `docs-gate/summary.md`
- `release-readiness/summary.md`

## Execution Steps

1. Confirm pilot scope and owner.
2. Run Gate 1 and produce spec artifact.
3. Execute Gate 2 via module-focused implementation flow.
4. Run Gate 3 with A->B->C policy.
5. Run Gate 4 independent review.
6. Run Gate 5 docs/changelog updates.
7. Finalize Gate 6 release readiness summary.
8. Transition corresponding Linear subtasks to `Done` after each successful gate.

## Success Criteria

- All six gate artifacts exist and are complete.
- No unresolved blocker findings.
- Pilot evidence subtask `local-9e10f998` marked done.

