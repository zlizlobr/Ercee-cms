# Pilot 2 Block Change Implementation Tasks

## Context

- Pilot: `pilot-2-block-change`
- Initiative: `local-c94aa39b`
- Evidence subtask: `local-a9a2d8a8`
- Scope:
  - finalize `commerce_product_tips_carousel` block contract and defaults,
  - ensure CMS preview parity,
  - ensure frontend render mapping parity.

## Gate 1 - Spec/Plan

- [x] Scope approved and bounded to one block.
- [x] Impacted contracts and file areas identified.
- [x] Confirm/transition Spec/Plan task to `Done` in Linear for Pilot #2 flow.

## Gate 2 - Implementace

- [x] Finalize block schema/defaults in `ProductTipsCarouselBlock`.
- [x] Add/align CMS preview component for the block under `resources/views/components/blocks/`.
- [x] Add/align frontend block component and registry mapping.
- [x] Add/update frontend block type definition if required.
- [x] Write `artifacts/gates/pilot-2-block-change/implementace/summary.md`.
- [x] Write `artifacts/gates/pilot-2-block-change/implementace/agent-output.json`.

## Gate 3 - Test Gate

- [x] Run A-stage preflight.
- [x] Run B-stage tests for changed scope.
- [x] Run C-stage cross-repo check (`npm run verify:blocks`).
- [x] Write `artifacts/gates/pilot-2-block-change/test-gate/test-results.txt`.
- [x] Write `artifacts/gates/pilot-2-block-change/test-gate/summary.md`.

## Gate 4 - Ralph Review Gate

- [x] Run independent review-agent pass.
- [x] Fix blocker findings (if any).
- [x] Write `artifacts/gates/pilot-2-block-change/ralph-review/review-findings.json`.
- [x] Write `artifacts/gates/pilot-2-block-change/ralph-review/summary.md`.

## Gate 5 - Docs Gate

- [x] Update docs for block contract/render flow.
- [x] Add changelog notes in impacted repos if release-relevant.
- [x] Run docs validations.
- [x] Write `artifacts/gates/pilot-2-block-change/docs-gate/summary.md`.

## Gate 6 - Release Readiness

- [x] Write `artifacts/gates/pilot-2-block-change/release-readiness/summary.md`.
- [x] Verify pilot #2 artifacts completeness.
- [x] Mark related Pilot #2 Linear tasks to `Done`.
- [x] Resolve transient Linear API DNS/network issue and retry `scripts/workflow/linear-transition-task.sh --task-id local-a9a2d8a8 --state-name Done --pull`.
