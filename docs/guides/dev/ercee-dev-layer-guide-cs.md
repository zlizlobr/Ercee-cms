# Ercee Dev Layer Guide (CZ)

Tento guide popisuje, jak používat sdílenou Ercee dev-layer policy v runtime CMS a v CI.

Detaily canonical kontraktu jsou v:
- `docs/guides/dev/ercee-dev-layer-policy.md`
- `docs/guides/dev/ercee-dev-layer-policy.contract.json`

## Co se změnilo v CMS

- Centrální policy adapter: `App\Support\DevLayer\ErceeDevLayerPolicy`
- Centrální runtime konfigurace: `config/ercee_dev.php`
- Helper jen pro dev logy: `dev_debug()`
- Guard pro public debug výstupy: `App\Support\DevLayer\PublicDebugWriter`
- CI kontrola kontraktu: `scripts/workflow/validate-dev-layer-policy.php`

## Runtime env nastavení

Použij tyto env proměnné:

```env
ERCEE_RUNTIME_PROFILE=dev
ERCEE_DEV_LAYER=true
ERCEE_LOG_LEVEL=debug
ERCEE_PUBLIC_DEBUG=false
```

Profily:
- `dev`: debug logy povolené, public debug jen s explicitním opt-in.
- `staging`: debug logy vypnuté.
- `prod`: debug logy i public debug vypnuté.

## Pravidla pro kód

- Používej `dev_debug()` pro dočasnou diagnostiku v cestách, které mají v produkci zůstat tiché.
- Pro provozní události používej `Log::info|warning|error|critical`.
- Nepřidávej nové ad-hoc `Log::debug()` volání v business logice.
- Jakýkoli public debug výstup do souboru musí jít přes `PublicDebugWriter`.

## Validace a testy

Spusť validaci policy:

```bash
php scripts/workflow/validate-dev-layer-policy.php
```

Spusť cílené testy:

```bash
./vendor/bin/phpunit \
  tests/Unit/Support/DevLayer/ErceeDevLayerPolicyTest.php \
  tests/Unit/Support/DevLayer/PublicDebugWriterTest.php \
  tests/Feature/Support/DevLayerLoggingPolicyTest.php
```

## Rollout checklist

1. Nastav `ERCEE_RUNTIME_PROFILE` v každém prostředí (`dev|staging|prod`).
2. Nech `ERCEE_PUBLIC_DEBUG=false` jako default.
3. Migruj zbývající `Log::debug()` callsite na `dev_debug()`.
4. Nech zapnutou CI validaci konzistence kontraktu a behavior matrix.
