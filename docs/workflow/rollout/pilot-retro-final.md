# Pilot Retro (Final: Pilot #1 + Pilot #2)

## Context

- Initiative: `local-c94aa39b` (`Workflow rollout v1 pilots`)
- Pilot #1: module change (`pilot-1-module-change`)
- Pilot #2: block change (`pilot-2-block-change`)

## Metrics

- Source: `artifacts/workflow-metrics.json`
- Current snapshot:
  - `initiatives_total`: 3
  - `release_readiness_artifacts_present`: 3
  - `release_readiness_ratio`: 1.0

## What Worked

- Full stage-gate execution is repeatable across module and block scopes.
- Gate artifacts are consistently generated and verifiable.
- Gate 3 sequence enforcement and review schema validation worked as intended.
- Cross-repo verification (`verify:blocks`) provides practical runtime confidence.

## Friction Points

1. Module-local dependency resolution in isolated module repo
- Symptom: module-local composer test bootstrap unavailable without workspace fallback.
- Impact: additional handling in Gate 3 A/B.
- Recommendation: standardize private package repository resolution for module-only runs.

2. Frontend verification sensitivity to generated artifacts/config drift
- Symptom: `verify:blocks` initially failed due linting of generated cache and test alias config.
- Impact: delayed C-stage completion.
- Recommendation: keep `.build-cache` lint ignore + alias/test config guardrails.

3. Linear transitions can fail transiently due network/API availability
- Symptom: task state transition occasionally fails with DNS/network error.
- Impact: manual retry needed for rollout bookkeeping.
- Recommendation: add retry strategy in orchestrator wrapper for transition script.

## Action Items (v1.1 Candidates)

- [x] Allow documented workspace bootstrap fallback in Gate 3 policy.
- [x] Keep frontend verification guardrails aligned with current block pipeline.
- [ ] Add retry/backoff wrapper for Linear transition failures.
- [ ] Add module test bootstrap standard for private dependency modules.
