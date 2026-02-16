# v1.1 Workflow Adjustments (From Pilot Data)

## Change Set

- Change ID: `WF-V11-TEST-001`
- Related friction point: module-local test harness unavailability for private dependency modules.
- Affected gate/agent: Gate 3 (`test-runner-agent`)

## Proposed Adjustment

- Rule/policy change:
  - Gate 3 allows workspace bootstrap fallback only when documented in test evidence with remediation follow-up.
- Script/CI change:
  - No CI relaxation; evidence requirement remains strict.
- Documentation change:
  - Updated `docs/workflow/gate-definition-v1.md` Gate 3 exit criteria.

## Risk Assessment

- Risk of false positives:
  - Low; fallback path requires explicit evidence.
- Risk of missed defects:
  - Medium if fallback becomes default; mitigated by remediation follow-up requirement.
- Rollback approach:
  - Remove fallback clause from Gate 3 definition if abuse is observed.

## Change Set

- Change ID: `WF-V11-OPS-001`
- Related friction point: transient Linear API transition failures.
- Affected gate/agent: rollout operations (`run-tasks-agent` / Linear transition path)

## Proposed Adjustment

- Rule/policy change:
  - Linear task state transitions require retry policy (bounded retries + explicit failure log).
- Script/CI change:
  - Planned: add wrapper around `scripts/workflow/linear-transition-task.sh`.
- Documentation change:
  - Track in rollout ops runbook as pending implementation.

## Risk Assessment

- Risk of false positives:
  - Low.
- Risk of missed defects:
  - Low; this is operational resilience only.
- Rollback approach:
  - Disable wrapper and use direct transition command.

## Acceptance Criteria

- [x] Gate 3 fallback policy documented.
- [ ] Linear transition retry wrapper implemented.
- [x] Pilot-based retro and adjustments documented.
