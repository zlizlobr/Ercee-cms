# Block Builder Agent Runbook

Use this runbook when adding or modifying CMS blocks and frontend block contracts.

## Steps

1. Confirm affected block contract fields and compatibility requirements.
2. Implement block and preview updates (directly or via Ralph orchestration execution flow).
3. Record changed contract references in handoff payload.
4. Hand off to `test-runner-agent`.

## Failure Recovery

- If preview or schema mismatch is found, revert to Implementace gate and patch mapping.
- Do not skip independent Gate 4 review even when implementation ran through Ralph orchestration.
