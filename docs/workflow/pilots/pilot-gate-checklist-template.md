# Pilot Gate Checklist Template

Use this checklist for each real pilot execution.

## Gate 1 — Spec/Plan

- [ ] Scope approved and bounded.
- [ ] Impacted contracts listed.
- [ ] Responsible implementation agent selected.
- [ ] `spec-plan/summary.md` written.

## Gate 2 — Implementace

- [ ] Implementation completed for declared scope.
- [ ] Handoff payload generated (`agent-output.json`).
- [ ] Cross-module boundary checks passed.
- [ ] `implementace/summary.md` written.

## Gate 3 — Test Gate

- [ ] A-stage preflight + verify passed.
- [ ] B-stage unit + contract-like checks passed.
- [ ] C-stage runtime/e2e passed (or documented blocker with policy reason).
- [ ] `test-gate/summary.md` and `test-results.txt` written.

## Gate 4 — Ralph Review Gate

- [ ] Review findings generated in required schema.
- [ ] Any blocker finding resolved before progression.
- [ ] `ralph-review/summary.md` and `review-findings.json` written.

## Gate 5 — Docs Gate

- [ ] Docs reflect shipped behavior.
- [ ] Canonical links and docs standards checks pass.
- [ ] Changelog update included when release-relevant.
- [ ] `docs-gate/summary.md` written.

## Gate 6 — Release Readiness

- [ ] Change impact captured.
- [ ] Risks captured.
- [ ] Rollback or mitigation captured.
- [ ] `release-readiness/summary.md` written.

