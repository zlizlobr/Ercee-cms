# Review Agent Runbook

Use this runbook for mandatory Ralph Review Gate execution.

## Steps

1. Load review rules from `docs/workflow/review-agent-rules.v1.json`.
2. Generate findings with severity, rule_id, required_fix, auto_fixable.
3. Save findings to `artifacts/gates/<initiative-id>/ralph-review/review-findings.json`.
4. If blocker exists, return to Implementace.
5. If only major/minor findings exist, pass with fix list to Docs Gate.

## Failure Recovery

- If required evidence is missing, fail the gate and request completion from prior gate owner.
