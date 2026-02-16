# Agent Workflow Governance

This document defines governance for the agent-based delivery workflow and stage gates.

## Scope

The workflow applies to engineering changes that impact modules, blocks, field schemas, tests, or project documentation.

## Agent Source of Truth

- Runtime agent skills are centralized in `/usr/local/var/www/agents/<agent-name>/SKILL.md`.
- This repository may include symlink references under `docs/workflow/agents/` for visibility.
- Skill behavior should be updated in the centralized agent directory first.

## Agent Responsibilities

### module-builder-agent

- Owns module scaffolding, provider wiring, registration, dependencies, and event integrations.
- Must deliver implementation notes with impacted contracts and risks.
- May execute implementation through Ralph orchestration where applicable.

### block-builder-agent

- Owns CMS block implementation and block contract compatibility (`type + data`).
- Must verify preview behavior and frontend mapping assumptions.
- May execute implementation through Ralph orchestration where applicable.

### field-type-agent

- Owns form field schema updates and validation rule compatibility.
- Must validate backward compatibility for `data_options` and persisted payloads.

### test-runner-agent

- Owns mandatory test flow execution order (`A -> B -> C`) and evidence.
- Must report blockers with recommended next action.

### review-agent

- Owns mandatory Ralph loop review with severity-tagged findings.
- Must emit `rule_id`, `required_fix`, and `auto_fixable` flags.
- Must stay an independent post-test gate, separate from implementation execution.

### docs-editor-agent

- Owns docs updates, canonical linking, and changelog note preparation.
- Must ensure docs reflect shipped behavior and gate evidence.

## Gate Ownership

| Gate | Primary owner | Approval authority |
| --- | --- | --- |
| Spec and Plan | implementation agent | Delivery lead |
| Implementace | implementation agent | Tech lead |
| Test Gate | test-runner-agent | QA owner / tech lead |
| Ralph Review Gate | review-agent | Delivery lead |
| Docs Gate | docs-editor-agent | Docs owner |
| Release Readiness | delivery lead + release owner | Release manager |

## Handoff Contract

All agent handoffs must follow `docs/templates/agent-output.md` and validate against `docs/schemas/agent-output.schema.json`.

## Escalation Path

1. Failing gate owner escalates to delivery lead.
2. Delivery lead decides rollback, rework, or controlled exception.
3. High-risk exceptions require release manager approval and documented mitigation.

## RACI Summary

- Responsible: Assigned gate owner.
- Accountable: Delivery lead.
- Consulted: Tech lead, QA owner, docs owner (as relevant).
- Informed: Initiative stakeholders in Linear.
