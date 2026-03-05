# Ercee Dev Layer Policy Contract

## Scope

This document defines the shared dev-layer contract for the Ercee ecosystem:

- CMS (`/usr/local/var/www/Ercee-cms`)
- Frontend (`/usr/local/var/www/ercee-frontend`)
- npm tooling and CI policy gates

## 1) Canonical env contract

| Variable | Type | Allowed values | Default | Precedence note |
|---|---|---|---|---|
| `ERCEE_RUNTIME_PROFILE` | string | `dev`, `staging`, `prod` | derived from `APP_ENV` | Highest for profile selection |
| `ERCEE_DEV_LAYER` | bool | `true`, `false` | `true` | If `false`, dev-layer features are disabled |
| `ERCEE_LOG_LEVEL` | string | `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, `emergency` | profile default | Overrides `LOG_LEVEL` input |
| `ERCEE_PUBLIC_DEBUG` | bool | `true`, `false` | `false` | Only effective in `dev` and when dev layer is enabled |

### Profile fallback from `APP_ENV`

- `production|prod` => `prod`
- `stage|staging|preprod` => `staging`
- all other values => `dev`

### Precedence rules

1. Explicit `ERCEE_*` value has priority over legacy value.
2. Invalid explicit value is ignored and fallback is used.
3. `prod` profile can never emit debug logs (safety clamp).
4. `ERCEE_DEV_LAYER=false` disables debug logging decisions.
5. Public debug output is enabled only when all are true:
   - profile is `dev`
   - `ERCEE_DEV_LAYER=true`
   - `ERCEE_PUBLIC_DEBUG=true`

### Configuration examples

#### local/dev

```env
ERCEE_RUNTIME_PROFILE=dev
ERCEE_DEV_LAYER=true
ERCEE_LOG_LEVEL=debug
ERCEE_PUBLIC_DEBUG=false
```

#### staging

```env
ERCEE_RUNTIME_PROFILE=staging
ERCEE_DEV_LAYER=true
ERCEE_LOG_LEVEL=info
ERCEE_PUBLIC_DEBUG=false
```

#### production

```env
ERCEE_RUNTIME_PROFILE=prod
ERCEE_DEV_LAYER=true
ERCEE_LOG_LEVEL=debug
ERCEE_PUBLIC_DEBUG=false
```

Note: In production, effective log level is clamped to at least `info`.

## 2) Behavior matrix

| Area | dev | staging | prod |
|---|---|---|---|
| Debug logs | ON | OFF | OFF |
| Public debug artifacts | EXPLICIT ONLY | OFF | OFF |
| Astro debug plugins | ON | OFF | OFF |
| npm dev-only scripts | ON | OFF | OFF |
| CI/prod required scripts | ON | ON | ON |

## 3) Shared policy API

Language-neutral API contract:

- `isDevLayerEnabled(): bool`
- `canWriteDebugLogs(): bool`
- `isPublicDebugEnabled(): bool`

Edge-case rules:

- Missing env values => fallback defaults
- Invalid values => fallback defaults + collect invalid metadata
- Unknown runtime profile => derive from `APP_ENV`
- Production profile => debug logs always disabled

## 6) Logging usage standard

- Use `dev_debug()` for intentional dev-only diagnostics.
- Use `Log::info|warning|error|critical` for operational logs.
- Avoid new ad-hoc `Log::debug()` in business code.

## 7) Public debug guard

- Any debug artifact that writes into `public/` must use `PublicDebugWriter`.
- `PublicDebugWriter` writes only to `public/debug/*`.
- Path traversal is blocked.

## 8) Frontend adapter alignment

Frontend adapter must implement the same inputs and decisions 1:1.

See:

- `docs/guides/dev/ercee-frontend-dev-layer-task-spec.md`
- `docs/guides/dev/ercee-dev-layer-policy.contract.json`

## 9) npm dependency/script policy

- Runtime dependencies belong to `dependencies`.
- Debug tooling and local-only QA tools belong to `devDependencies`.
- Scripts guarded by dev profile:
  - local debug/inspect helpers
  - non-essential diagnostics
- Scripts required in CI/prod:
  - build
  - contract validation
  - test gates

## 12) Rollout and migration plan

### Phase 1: Critical runtime paths

- migrate existing CMS debug callsites to `dev_debug()`
- verify prod clamps and public debug OFF

### Phase 2: Admin and integration paths

- migrate remaining diagnostics in admin/application services
- enforce coding rule in code review checklist

### Phase 3: Ecosystem sync

- implement frontend adapter with same matrix
- enable cross-repo policy parity checks

### Ownership

- CMS owner: backend team
- Frontend owner: frontend team
- Policy contract owner: platform/architecture

### Backout

- set `ERCEE_DEV_LAYER=false`
- keep `ERCEE_RUNTIME_PROFILE=prod`
- deploy with `ERCEE_PUBLIC_DEBUG=false`

### Post-deploy verification

- verify prod logs do not include debug entries
- verify no public debug files are generated
- verify info/warning/error logs remain intact
