# Pilot #2 Block Change Playbook

This playbook defines how to execute the second real workflow pilot on a block-level change.

## Goal

Validate end-to-end workflow execution on a real block contract/rendering change with full evidence.

## Linear Mapping

- Parent initiative: `local-c94aa39b` (`Workflow rollout v1 pilots`)
- Pilot evidence subtask: `local-a9a2d8a8` (`Workflow rollout v1 pilots - Pilot #2 block change evidence`)

Use these standard gate subtasks from the same parent:

- `local-9a9bdbbc` Spec/Plan
- `local-3d8a326b` Implementace
- `local-1407f835` Test Gate
- `local-24901a73` Ralph Review Gate (agent-only)
- `local-fbd404aa` Docs Gate
- `local-12b5de0d` Release Readiness

## Scope Definition

Pick one real block scope with clear frontend impact:

- add one new block via generator, or
- update one existing block contract + mapping + preview parity.

Avoid for pilot #2:

- multi-block refactors in a single pass
- cross-module architecture migration

## Evidence Location

Use this initiative folder:

- `artifacts/gates/pilot-2-block-change/`

## Success Criteria

- All six gate artifacts are complete.
- No unresolved blocker findings.
- Pilot evidence subtask `local-a9a2d8a8` marked done.
