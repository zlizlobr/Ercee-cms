# Field Type Agent Runbook

Use this runbook when modifying form field schema or validation behavior.

## Steps

1. Validate schema constraints and backward compatibility.
2. Implement field and validation changes.
3. Record migration risk in handoff payload.
4. Hand off to `test-runner-agent`.

## Failure Recovery

- If data compatibility is unclear, block progression and require spec update.
