# Commerce Module Implementation Tasks

## Context

- Target module: `/usr/local/var/www/ercee-modules/ercee-module-commerce`
- Goal:
  - Add carousel block for product tips.
  - Enable admin sorting by product name.
  - Add stock evidence behavior (in stock/out of stock + quantity).
  - Add Commerce settings tab with `XML Feeds`.
  - Confirm tab priority support; implement if missing.

## Gate 1 - Spec/Plan

- [x] Confirm scope and constraints (no breaking API contracts, safe migrations).
- [x] Map initial impacted files/contracts.
- [x] Create/confirm Linear parent + subtasks for this change.
- [x] Mark Spec/Plan task as `Done` in Linear.

## Gate 2 - Implementace

- [x] Implement admin table sorting by product name in `ProductResource` table definition.
- [x] Add/confirm stock fields UX in product/variant admin (quantity + in stock/out of stock semantics).
- [x] Add or align stock domain logic (single source of truth for availability).
- [x] Implement `Settings` tab in commerce admin form/navigation.
- [x] Add `XML Feeds` sub-tab content and persistence contract.
- [x] Determine whether tab ordering is natively supported; if not, implement explicit tab priority mechanism.
- [x] Implement carousel "tip produktu" block registration and rendering integration.
- [x] Write implementace summary to `artifacts/gates/pilot-1-module-change/implementace/summary.md`.
- [x] Write `agent-output.json` to `artifacts/gates/pilot-1-module-change/implementace/agent-output.json`.

## Gate 3 - Test Gate

- [x] Run A-stage checks (preflight/verify for impacted scope).
- [x] Run B-stage checks (unit + contract checks in module).
- [x] Run C-stage checks when UI/release critical paths are affected.
- [x] Save test evidence to:
- [x] `artifacts/gates/pilot-1-module-change/test-gate/test-results.txt`
- [x] `artifacts/gates/pilot-1-module-change/test-gate/summary.md`
- [x] Resolve C-stage blocker (`astro check` fail in `../ercee-frontend`) and rerun Gate 3 to green status.

## Gate 4 - Ralph Review Gate

- [x] Run independent review-agent pass (separate from implementace loop).
- [x] Fix all `blocker` findings.
- [x] Capture findings and summary:
- [x] `artifacts/gates/pilot-1-module-change/ralph-review/review-findings.json`
- [x] `artifacts/gates/pilot-1-module-change/ralph-review/summary.md`

## Gate 5 - Docs Gate

- [x] Update module docs for new block, stock behavior, and settings/XML feed tab usage.
- [x] Add changelog note for release-relevant behavior changes.
- [x] Run docs validation checks.
- [x] Write docs gate summary to `artifacts/gates/pilot-1-module-change/docs-gate/summary.md`.

## Gate 6 - Release Readiness

- [x] Write release impact/risk/rollback summary:
- [x] `artifacts/gates/pilot-1-module-change/release-readiness/summary.md`
- [x] Verify gate artifact completeness for `pilot-1-module-change`.
- [x] Mark all related Linear gate subtasks as `Done`.
