# Exception and Hotfix Policy

This policy defines when the workflow can be bypassed and what evidence is required.

## Policy Goals

- Keep production safe during urgent fixes.
- Preserve traceability when any gate is bypassed.
- Prevent repeated bypasses from becoming default process.

## Allowed Exception Types

### Emergency hotfix

- Trigger: Production outage, data corruption risk, or critical security issue.
- Minimum required gates: Test Gate A+B, Ralph Review Gate, Release Readiness evidence.
- Docs Gate may be deferred by up to 24 hours.

### Time-boxed delivery exception

- Trigger: External deadline with non-critical risk profile.
- Required approval: Delivery lead and release manager.
- Deferred gate completion must be scheduled in the same initiative within 2 business days.

## Disallowed Exceptions

- Skipping Ralph Review Gate.
- Shipping without rollback or mitigation notes.
- Repeated exception for the same unresolved root cause.

## Required Exception Record

Store under `artifacts/gates/<initiative-id>/release-readiness/exception-record.md`:

- reason
- impacted scope
- skipped/deferred gates
- approvers
- mitigation plan
- due date for deferred work

## Approval Matrix

| Change type | Required approvers |
| --- | --- |
| Emergency hotfix | Delivery lead + release manager |
| Security hotfix | Delivery lead + security owner + release manager |
| Time-boxed exception | Delivery lead + tech lead + release manager |

## Post-Release Follow-Up

- Open follow-up Linear subtask for deferred gates.
- Complete deferred gate work within SLA.
- Add short incident summary to `CHANGELOG.md` under Unreleased.
