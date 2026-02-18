# Testing Flow For LLM Agents (CMS + Frontend + Modules)

This guide is an operational playbook for agents implementing changes across the Ercee ecosystem.
Always follow flow A -> B -> C.

## Scope
- CMS repo: `/usr/local/var/www/Ercee-cms`
- Frontend repo: `/usr/local/var/www/ercee-frontend`
- Module repos: `/usr/local/var/www/ercee-modules/ercee-module-*`

## A) Preflight + static gate (mandatory)

1. CMS/block scope:
```bash
cd /usr/local/var/www/Ercee-cms
npm run preflight:blocks
npm run verify:blocks
```

2. Field/public scope:
```bash
cd /usr/local/var/www/ercee-frontend
npm run preflight:forms-field
npm run verify:forms-field
```

`verify:*` must pass cleanly. If preflight fails, fix the environment first (`npm ci`).

## B) Unit + contract-like checks (mandatory)

Frontend minimum:
```bash
cd /usr/local/var/www/ercee-frontend
npm run test
```

CMS/module minimum:
```bash
cd /usr/local/var/www/Ercee-cms
./scripts/test-safe.sh

cd /usr/local/var/www/ercee-modules/<module>
./vendor/bin/phpunit
```

Safety rule (mandatory):
- Never run `php artisan test` directly in `/usr/local/var/www/Ercee-cms` when local sqlite is used.
- Always use `./scripts/test-safe.sh` so tests run on a cloned DB (`storage/testing/database.sqlite`).

Notes:
- Every bugfix must include a regression test.
- For CMS -> frontend API mapping changes, test parser/mapper behavior and edge cases.

## C) Runtime smoke / E2E (recommended PR gate, mandatory before release)

Blocks orchestrated from CMS:
```bash
cd /usr/local/var/www/Ercee-cms
npm run verify:blocks:e2e
```

Module/backend high-priority admin auth check:
```bash
cd /usr/local/var/www/Ercee-cms
npm run verify:backend-admin:e2e
```

Field/public only:
```bash
cd /usr/local/var/www/ercee-frontend
npm run verify:forms-field:e2e
```

## Decision policy
- Done = A + B passed.
- Release-critical or public UI changes = A + B + C passed.
- If C cannot run locally, record the blocker and run C in CI/nightly.

## Required links
- Unified strategy: `dev/todo/testing-unification-ercee-ecosystem.md`
- Blocks flow: `docs/cms-block-integration-guide.md`
- Field-type flow: `../ercee-modules/ercee-module-forms/docs/form-field-type-integration-guide.md`
