# Agent-Human Review Policy

This policy defines when Agent-Human review is mandatory versus optional.

## Decision Matrix

Agent-Human review is mandatory when any of the following apply:

- Cross-module architecture refactor.
- Security/auth/permissions model change.
- Payment, webhook, or externally exposed API contract breaking change.
- Release-critical change with rollback complexity.

Agent-Human review is optional for:

- Low-risk localized bugfixes.
- Non-breaking docs-only or internal refactor updates.
- Pure UI copy/style changes with no contract or behavior impact.

## Required Evidence Checklist

Store under `artifacts/gates/<initiative-id>/ralph-review/agent-human-review.md`:

- Context and scope
- Agent findings summary
- Human decisions and accepted risks
- Required follow-up tasks
- Release approval decision (`yes/no`)

## Gate Position

Run after Gate 4 (Ralph Review Gate) and before Release Readiness. It may run before or after Docs Gate depending on change risk.
