# Ercee Dev Layer Guide

This guide describes how to use the shared Ercee dev-layer policy in CMS runtime and CI.

Canonical contract details are in:
- `docs/guides/dev/ercee-dev-layer-policy.md`
- `docs/guides/dev/ercee-dev-layer-policy.contract.json`

## What changed in CMS

- Central policy adapter: `App\Support\DevLayer\ErceeDevLayerPolicy`
- Central runtime config: `config/ercee_dev.php`
- Dev-only log helper: `dev_debug()`
- Public debug guard: `App\Support\DevLayer\PublicDebugWriter`
- CI contract check: `scripts/workflow/validate-dev-layer-policy.php`

## Runtime env setup

Use these env variables:

```env
ERCEE_RUNTIME_PROFILE=dev
ERCEE_DEV_LAYER=true
ERCEE_LOG_LEVEL=debug
ERCEE_PUBLIC_DEBUG=false
```

Profiles:
- `dev`: debug logs allowed, public debug only with explicit opt-in.
- `staging`: debug logs disabled.
- `prod`: debug logs and public debug disabled.

## Coding rules

- Use `dev_debug()` for temporary diagnostics in code paths that should stay silent in production.
- Use `Log::info|warning|error|critical` for operational events.
- Do not add new ad-hoc `Log::debug()` calls in business logic.
- Any public debug file output must go through `PublicDebugWriter`.

## Validation and tests

Run policy validation:

```bash
php scripts/workflow/validate-dev-layer-policy.php
```

Run focused tests:

```bash
./vendor/bin/phpunit \
  tests/Unit/Support/DevLayer/ErceeDevLayerPolicyTest.php \
  tests/Unit/Support/DevLayer/PublicDebugWriterTest.php \
  tests/Feature/Support/DevLayerLoggingPolicyTest.php
```

## Rollout checklist

1. Set `ERCEE_RUNTIME_PROFILE` in each environment (`dev|staging|prod`).
2. Keep `ERCEE_PUBLIC_DEBUG=false` by default.
3. Migrate remaining `Log::debug()` callsites to `dev_debug()`.
4. Keep CI validation enabled for contract and matrix consistency.
