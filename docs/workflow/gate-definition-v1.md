# Stage Gate Definition v1

This document defines mandatory entry and exit criteria for each workflow gate.

## Gate 1: Spec and Plan

### Entry

- Initiative parent exists in Linear.
- Scope statement is written.

### Exit

- Responsible agent selected.
- Impacted contracts identified.
- Required subtasks created for all gates.

## Gate 2: Implementace

### Entry

- Gate 1 approved.

### Exit

- Implementation complete for declared scope.
- Changes are localized to appropriate layers.
- Cross-module rules are not violated.
- Implementation can be executed via Ralph orchestration, but this does not satisfy review requirements by itself.

## Gate 3: Test Gate

### Entry

- Gate 2 approved.

### Exit

- A) preflight + verify executed.
- B) unit + contract checks executed.
- C) runtime smoke/e2e executed when release/UI critical.
- Test evidence stored in gate artifacts.

## Gate 4: Ralph Review Gate (agent-only)

### Entry

- Gate 3 approved.
- Review must be executed as an independent gate even if Gate 2 used Ralph for implementation orchestration.

### Exit

- review-agent findings generated.
- `blocker` findings must be fixed before progression.
- `major`/`minor` findings need fix list and owner.

## Gate 5: Docs Gate

### Entry

- Gate 4 approved.

### Exit

- Docs match implementation.
- Canonical links validated.
- Changelog notes prepared.

## Gate 6: Release Readiness

### Entry

- Gate 5 approved (or approved policy exception).

### Exit

- Change impact summary completed.
- Risks and mitigations documented.
- Rollback plan documented when relevant.
- Gate evidence package is complete.
