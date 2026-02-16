# Workflow Default Rollout Activation

## Objective

Activate the stage-gate agent workflow as the default delivery path for all new initiatives.

## Effective Date

- Start date: 2026-02-16

## Scope

- Applies to all new initiatives created after the effective date.
- Applies to module changes, block changes, field-type changes, and cross-repo flows that require release readiness evidence.

## Default Execution Policy

1. Every new initiative must include gate subtasks in this order:
   - Spec/Plan
   - Implementace
   - Test Gate
   - Ralph Review Gate
   - Docs Gate
   - Release Readiness
2. Gate progression is mandatory; skipping gates requires policy exception evidence.
3. Review gate is always independent from implementation gate.
4. Docs + changelog updates are required before Release Readiness closure.

## Operational Procedure

1. Create initiative + subtasks via:
   - `scripts/workflow/linear-initiative-scaffold.py`
2. Execute work through orchestrated agent flow.
3. Store evidence in:
   - `artifacts/gates/<initiative>/...`
4. Transition gate tasks to `Done` only after gate exit criteria pass.
5. Record weekly compliance using:
   - `docs/workflow/rollout/weekly-compliance-template.md`

## Governance Ownership

- Workflow owner: Delivery Lead / Staff Architect role
- Gate enforcement owner: CI + code owners for workflow assets
- Compliance owner: run-tasks orchestration + weekly report reviewer

## Activation Status

- Pilot #1: completed
- Pilot #2: completed
- Retro and v1.1 adjustments: completed
- Weekly compliance reporting: active
- Branch protection hard enforcement: pending manual GitHub settings verification

## Immediate Follow-up

1. Complete branch protection checklist:
   - `docs/workflow/rollout/branch-protection-checklist.md`
2. Keep weekly compliance cadence with archived reports in:
   - `docs/workflow/rollout/`
