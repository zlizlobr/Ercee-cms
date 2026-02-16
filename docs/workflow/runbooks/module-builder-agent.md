# Module Builder Agent Runbook

Use this runbook when changes affect module boundaries, provider registration, or module lifecycle.

## Steps

1. Confirm gate status is `Spec and Plan` approved.
2. Implement scoped module changes.
3. Produce handoff payload from `docs/templates/agent-output.md`.
4. Hand off to `test-runner-agent`.

## Failure Recovery

- On blocker review findings, return to Implementace gate with explicit fix list.
