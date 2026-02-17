# Pilot #1 Execution Run Checklist (Module Change)

This checklist is an operator-ready sequence for running Pilot #1 on a real module change.
Use it together with `docs/workflow/pilots/pilot-1-module-change-playbook.md`.

## 0) Preconditions

- [ ] You have a real module change scope approved for pilot.
- [ ] `.linear/config/.env` is configured (`LINEAR_API_KEY`, `LINEAR_TEAM_ID`).
- [ ] Pilot evidence folder exists: `artifacts/gates/pilot-1-module-change/`.
- [ ] You know gate task IDs (or Linear IDs) for this pilot:
- [ ] Spec/Plan: `local-9a9bdbbc`
- [ ] Implementace: `local-3d8a326b`
- [ ] Test Gate: `local-1407f835`
- [ ] Ralph Review Gate: `local-24901a73`
- [ ] Docs Gate: `local-fbd404aa`
- [ ] Release Readiness: `local-12b5de0d`

## 1) Gate 1 - Spec/Plan

- [ ] Fill `artifacts/gates/pilot-1-module-change/spec-plan/summary.md` with:
- [ ] Scope and non-goals
- [ ] Impacted contracts/interfaces
- [ ] Responsible implementation agent and owner
- [ ] Exit check: scope is bounded and accepted.
- [ ] Mark gate task done in Linear:

`scripts/workflow/linear-transition-task.sh --task-id local-9a9bdbbc --state-name Done --pull`

Expected output:

- `OK: transitioned <LINEAR-ID> -> Done`

## 2) Gate 2 - Implementace

- [ ] Execute module implementation through orchestrator + `module-builder-agent`.
- [ ] Write summary to `artifacts/gates/pilot-1-module-change/implementace/summary.md`.
- [ ] Export handoff payload to `artifacts/gates/pilot-1-module-change/implementace/agent-output.json`.
- [ ] Validate payload schema:

`python3 scripts/workflow/validate-agent-output.py --type agent-output --file artifacts/gates/pilot-1-module-change/implementace/agent-output.json`

Expected output:

- `OK: valid agent-output payload`

- [ ] Mark gate task done in Linear:

`scripts/workflow/linear-transition-task.sh --task-id local-3d8a326b --state-name Done --pull`

## 3) Gate 3 - Test Gate (A -> B -> C policy)

- [ ] Run A-stage (preflight + verify):

`npm run preflight:blocks && npm run verify:blocks`

- [ ] Run B-stage checks for module scope (unit/contract commands defined by repo scope).
- [ ] Run C-stage only if release/UI-critical:

`npm run verify:blocks:e2e`

- [ ] Save command log to `artifacts/gates/pilot-1-module-change/test-gate/test-results.txt`.
- [ ] Write decision summary to `artifacts/gates/pilot-1-module-change/test-gate/summary.md`.
- [ ] Enforce sequence format:

`python3 scripts/workflow/enforce-test-flow.py --file artifacts/gates/pilot-1-module-change/test-gate/test-results.txt`

Expected output:

- `OK: test flow evidence passed`

- [ ] Mark gate task done in Linear:

`scripts/workflow/linear-transition-task.sh --task-id local-1407f835 --state-name Done --pull`

## 4) Gate 4 - Ralph Review Gate (independent)

- [ ] Run independent review via `review-agent` (must be separate from implementace loop).
- [ ] Store findings at `artifacts/gates/pilot-1-module-change/ralph-review/review-findings.json`.
- [ ] Store decision summary at `artifacts/gates/pilot-1-module-change/ralph-review/summary.md`.
- [ ] Validate findings schema:

`python3 scripts/workflow/validate-agent-output.py --type review-findings --file artifacts/gates/pilot-1-module-change/ralph-review/review-findings.json`

Expected output:

- `OK: valid review-findings payload`

- [ ] If any `blocker` exists: return to Gate 2, then rerun Gate 3 and Gate 4.
- [ ] Mark gate task done in Linear:

`scripts/workflow/linear-transition-task.sh --task-id local-24901a73 --state-name Done --pull`

## 5) Gate 5 - Docs Gate

- [ ] Update docs/changelog to match implementation.
- [ ] Write docs summary: `artifacts/gates/pilot-1-module-change/docs-gate/summary.md`.
- [ ] Run docs checks:

`python3 scripts/check-doc-links.py && python3 scripts/workflow/lint-doc-standards.py && python3 scripts/workflow/validate-canonical-links.py`

Expected output:

- `All local doc links in docs/ are valid.`
- `All docs files passed lightweight standards checks.`
- `Canonical documentation links are valid.`

- [ ] Mark gate task done in Linear:

`scripts/workflow/linear-transition-task.sh --task-id local-fbd404aa --state-name Done --pull`

## 6) Gate 6 - Release Readiness

- [ ] Write `artifacts/gates/pilot-1-module-change/release-readiness/summary.md` with:
- [ ] Change impact
- [ ] Risks + mitigations
- [ ] Rollback plan (or explicit N/A reason)
- [ ] Verify required evidence exists:

`python3 scripts/workflow/verify-gate-artifacts.py --initiative pilot-1-module-change`

Expected output:

- `OK: gate artifacts complete`

- [ ] Mark gate task done in Linear:

`scripts/workflow/linear-transition-task.sh --task-id local-12b5de0d --state-name Done --pull`

## 7) Pilot Closeout

- [ ] Mark pilot evidence subtask `local-9e10f998` as `Done`:

`scripts/workflow/linear-transition-task.sh --task-id local-9e10f998 --state-name Done --pull`

- [ ] Append pilot metrics into `artifacts/workflow-metrics.json`:

`python3 scripts/workflow/collect-workflow-metrics.py`

Expected output:

- `OK: transitioned <LINEAR-ID> -> Done`
- `Wrote metrics to artifacts/workflow-metrics.json`
