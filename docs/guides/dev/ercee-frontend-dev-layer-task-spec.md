# Frontend Dev Layer Adapter Task Spec

## Goal

Implement frontend (Astro/Node) dev-layer adapter with exactly the same contract and decisions as CMS.

## Source of truth

- `docs/guides/dev/ercee-dev-layer-policy.contract.json`
- `docs/guides/dev/ercee-dev-layer-policy.md`

## Required adapter API

- `isDevLayerEnabled(): boolean`
- `canWriteDebugLogs(): boolean`
- `isPublicDebugEnabled(): boolean`

## Required inputs

- `ERCEE_DEV_LAYER`
- `ERCEE_LOG_LEVEL`
- `ERCEE_PUBLIC_DEBUG`
- `ERCEE_RUNTIME_PROFILE`
- `APP_ENV` (fallback mapping only)

## Required behavior

- `prod` never allows debug logs.
- Public debug output is enabled only when:
  - profile `dev`
  - `ERCEE_DEV_LAYER=true`
  - `ERCEE_PUBLIC_DEBUG=true`
- Invalid values must fallback to canonical defaults.

## Acceptance criteria

- Frontend adapter decision table equals CMS matrix 1:1.
- Astro debug plugins toggle via adapter decision, not ad-hoc env checks.
- npm debug/dev scripts are gated by adapter decision.
- CI check fails when frontend matrix diverges from canonical contract.
