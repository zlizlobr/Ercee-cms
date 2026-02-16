# Workflow Implementation Tasks

## Phase 1 — Foundations
- [x] Publish v1 workflow blueprint and stage sequence in `agent-flow-brainstorm.md`.
- [x] Define responsibilities for `module-builder-agent`, `block-builder-agent`, `field-type-agent`, `test-runner-agent`, `review-agent`, and `docs-editor-agent` in `docs/workflow/agent-governance.md`.
- [x] Define unified handoff format and publish template in `docs/templates/agent-output.md`.
- [x] Define Agent-Human review output template in `docs/templates/agent-human-review-output.md`.
- [x] Define gate entry/exit criteria in `docs/workflow/gate-definition-v1.md`.
- [x] Define exception and hotfix policy in `docs/workflow/exception-hotfix-policy.md`.
- [x] Add ownership mapping for workflow assets in `.github/CODEOWNERS`.
- [x] Define repository workflow labels manifest in `.github/labels/workflow-labels.json`.
- [x] Define gate evidence storage convention in `artifacts/gates/README.md`.

## Phase 2 — Agents
- [x] Create centralized `SKILL.md` files for all six workflow agents in `/usr/local/var/www/agents/*` and expose repo references via `docs/workflow/agents/*/SKILL.md` symlinks.
- [x] Upgrade centralized `SKILL.md` files with deterministic playbooks (entry criteria, classification rules, command matrix, retry/fail policy, and required output contract) derived from project docs.
- [x] Align `block-builder-agent` skill to `docs/cms-block-integration-guide.md` parity (generator workflow, naming normalization, standardized components, media handling, and block DoD checks).
- [x] Align `docs-editor-agent` skill to `docs/guides/docs-authoring-guide.md` parity (doc-type decision rules, naming suffixes, canonical-vs-task split, cross-repo linking, and placement rules).
- [x] Align `field-type-agent` skill to forms canonical guide `/usr/local/var/www/ercee-modules/ercee-module-forms/docs/form-field-type-integration-guide.md` (generator flow, provider registration, JSON registry, translations, FE renderer integration, and forms-field verification steps).
- [x] Align `test-runner-agent` skill to `docs/guides/testing-flow-llm.md` parity (explicit A/B/C command matrix across CMS/frontend/modules, decision policy, and C-stage blocker handling).
- [x] Upgrade `review-agent` to policy-driven v2 (rule->evidence mapping, deterministic go/no-go thresholds, re-review loop policy, and explicit output contract).
- [x] Extend review ruleset with coding standards coverage (`STYLE-*` naming consistency, `STRUCT-*` namespace/layer consistency).
- [x] Refactor all centralized agent skills to repo-root-aware path resolution (`${REPO_ROOT}/docs/...`) with explicit missing-doc blocker policy.
- [x] Define Ralph orchestration model: Implementace may run via Ralph task execution, but evaluation must pass through separate Gate 4 `review-agent` loop.
- [x] Create machine-readable schema for agent handoff payloads in `docs/schemas/agent-output.schema.json`.
- [x] Create machine-readable schema for review findings in `docs/schemas/review-findings.schema.json`.
- [x] Add example agent output payload in `docs/workflow/examples/agent-output.example.json`.
- [x] Add example review findings payload in `docs/workflow/examples/review-findings.example.json`.
- [x] Implement JSON payload validation script in `scripts/workflow/validate-agent-output.py`.
- [x] Define review-agent core ruleset v1 in `docs/workflow/review-agent-rules.v1.json`.
- [x] Implement test flow sequence enforcement script in `scripts/workflow/enforce-test-flow.py`.
- [x] Add test gate report template in `docs/templates/test-gate-report.md`.
- [x] Publish runbooks for all workflow agents in `docs/workflow/runbooks/`.
- [x] Publish failure recovery runbook in `docs/workflow/runbooks/failure-recovery.md`.

## Phase 3 — Gates & Quality Controls
- [x] Add CI workflow for gate checks in `.github/workflows/agent-gates.yml`.
- [x] Enforce Gate 1 document prerequisites in CI (`gate-spec-plan` job).
- [x] Enforce Gate 2 implementation criteria in CI (scope localization and cross-module communication rules).
- [x] Enforce Gate 3 test flow evidence format in CI via `scripts/workflow/enforce-test-flow.py`.
- [x] Enforce Gate 4 review payload validation in CI via `scripts/workflow/validate-agent-output.py`.
- [x] Enforce CI guard that review ruleset includes coding standards families (`STYLE-*`, `STRUCT-*`) in `.github/workflows/agent-gates.yml`.
- [x] Enforce Gate 5 docs link checks in CI via `scripts/check-doc-links.py`.
- [x] Enforce Gate 6 release-readiness evidence in CI (change impact summary, risks, rollback/mitigation where relevant).
- [x] Enforce docs standards lint in CI via `scripts/workflow/lint-doc-standards.py`.
- [x] Enforce canonical documentation links via `scripts/workflow/validate-canonical-links.py`.
- [x] Add changelog process rules to `docs/standards/documentation.md`.
- [x] Add basic workflow telemetry collector in `scripts/workflow/collect-workflow-metrics.py`.
- [ ] Enforce blocking merge rule based on required gate labels/status checks in repository branch protection.

## Phase 4 — Integration & Rollout
- [x] Add Linear initiative/subtask scaffolding automation in `scripts/workflow/linear-initiative-scaffold.py`.
- [x] Add optional post-create Linear sync/pull hooks (`--sync`, `--pull`) in `scripts/workflow/linear-initiative-scaffold.py`.
- [x] Add Linear task state transition script for gate completion updates (`scripts/workflow/linear-transition-task.sh`).
- [x] Create rollout initiative parent + standard gate subtasks in `.linear/data/tasks.json` (`Workflow rollout v1 pilots`).
- [x] Add execution subtasks under rollout initiative for pilot #1, pilot #2, retro, v1.1 adjustments, and compliance monitoring.
- [x] Extend central `run-tasks-agent` to v2 execute orchestration mode (hybrid Ralph execution loop + gate governance + Linear state transitions + artifact contract).
- [x] Add npm scripts for workflow validation and telemetry in `package.json`.
- [x] Update documentation index with workflow canonical links in `docs/README.md`.
- [x] Publish junior-facing user-vs-orchestrator example in `docs/workflow/junior-user-agent-example.md`.
- [x] Define Agent-Human Review checkpoint policy (when mandatory vs optional) and add a required output/evidence checklist.
- [x] Prepare pilot #1 module-change execution package (playbook, gate checklist template, and evidence skeleton under `artifacts/gates/pilot-1-module-change/`).
- [x] Publish operator-ready Pilot #1 execution checklist with command-level gate flow and expected outputs in `docs/workflow/pilots/pilot-1-execution-run-checklist.md`.
- [x] Prepare pilot #2 block-change execution package (playbook and evidence skeleton under `artifacts/gates/pilot-2-block-change/`).
- [x] Prepare rollout operation templates for branch protection verification, retro capture, v1.1 adjustments, and weekly compliance tracking in `docs/workflow/rollout/`.
- [x] Run pilot #1 on a real module change with full gate evidence captured.
- [x] Run pilot #2 on a real block change with full gate evidence captured.
- [x] Collect pilot metrics and friction points in a retro document.
- [x] Apply v1.1 adjustments to gate thresholds/rules based on pilot data.
- [x] Roll out workflow as default for new initiatives and monitor weekly compliance.
