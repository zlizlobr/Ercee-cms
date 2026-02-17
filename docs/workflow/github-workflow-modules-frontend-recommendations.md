# GitHub Workflow Doporučení: Moduly + Frontend

## 1) Co už je v CMS workspace hotovo

- [x] Agent gate workflow je zavedený (`.github/workflows/agent-gates.yml`).
- [x] CI pro CMS je zavedené (`.github/workflows/ci.yml`).
- [x] CI nyní toleruje chybějící module checkouty přes fallback stub (warning místo failu).
- [x] Gate evidence, šablony, runbooky a workflow governance jsou zavedené.
- [x] Pilot #1 a #2 byly připravené a projeté v gate flow.

## 2) Cíl doporučení

- [ ] Mít konzistentní GitHub workflow v každém modul repo (`/usr/local/var/www/ercee-modules/*`).
- [ ] Mít samostatně robustní workflow pro public část (`/usr/local/var/www/ercee-frontend`).
- [ ] Minimalizovat falešné CI pády (cross-repo, private access, review blocking chaos).

## 3) Standard pro každý modul repo

Použít pro každý modul stejný základ (vychází z `.github/workflows/module-ci.yml.template`):

- [ ] Workflow `ci.yml`: `pull_request` + `push` na `main`.
- [ ] Job `code-quality`: Pint + PHPStan pouze nad změněnými PHP soubory.
- [ ] Job `tests`: PHP 8.3 povinně, PHP 8.4 volitelně (např. jen na push).
- [ ] Composer install bez interakce, cache `vendor`.
- [ ] Fail-fast jen pro skutečné chyby kvality/testů, ne pro chybějící cizí repa.
- [ ] Jednotné názvy checků napříč moduly (snadné branch protection).

## 4) Doporučené workflow per modul

### 4.1 `ercee-module-forms`

- [ ] Zapnout povinné: Pint, PHPStan, Unit/Feature tests module části.
- [ ] Přidat smoke integraci proti CMS core (path repository).
- [ ] Kontrolovat kompatibilitu field type registrace (kontrakt test).

### 4.2 `ercee-module-commerce`

- [ ] Zapnout povinné: Pint, PHPStan, Unit/Feature tests module části.
- [ ] Přidat testy migrací a BC guard pro tabulky (`products`, sklad, settings).
- [ ] Přidat smoke test API/admin route registrace.

### 4.3 `ercee-module-funnel`

- [ ] Zapnout povinné: Pint, PHPStan, Unit tests.
- [ ] Odstranit závislost na konkrétním branch ref v cizích CI (`ref` nenastavovat natvrdo).
- [ ] Přidat minimální integrační test provider bootu.

### 4.4 `ercee-module-llm`

- [ ] Zapnout povinné: Pint, PHPStan, Unit tests.
- [ ] Přidat fake/mock provider test matrix (OpenAI/Claude/Gemini resolver logika).
- [ ] Zakázat reálné síťové volání v CI (jen fake transport).

### 4.5 `ercee-module-theme-builds`

- [ ] Zapnout povinné: Pint, PHPStan, Unit tests.
- [ ] Přidat testy request validation + queue/job flow.
- [ ] Přidat smoke test endpointu build triggeru.

## 5) Workflow pro `ercee-frontend` (public část)

- [ ] Workflow `ci.yml`: `pull_request` + `push` na `main`.
- [ ] Povinné checky: install, typecheck, lint, unit tests, build.
- [ ] Přidat samostatný `data-contract-check` (validace dat z CMS API payloadů).
- [ ] Přidat guard pro TS chyby (žádné `continue-on-error` u typecheck).
- [ ] Přidat `changed-files` optimalizaci pro rychlost (lint/test jen tam, kde jde).
- [ ] Přidat cache pro package manager (`pnpm`/`npm`) a build cache.

## 6) Cross-repo pravidla (CMS -> modules -> frontend)

- [ ] V CMS CI držet module checkout tolerantní (fallback stub + warning).
- [ ] Nepoužívat hard fail na chybějící private modul, pokud není nutný pro daný test scope.
- [ ] Když je potřeba tvrdá integrace, spouštět ji v separátním nightly workflow.
- [ ] Synchronizaci docs/API do frontend držet v dedikovaném workflow (`sync-api-docs-to-frontend.yml`).

## 7) GitHub nastavení v browseru (přesný postup)

Pro **každý repo** (CMS, každý modul, frontend):

### 7.1 Branch protection / Rulesets

- [ ] `Settings -> Rules -> Rulesets` (preferovaně ruleset, ne mix s legacy pravidly).
- [ ] Target branch: `main`.
- [ ] Zapnout `Require a pull request before merging`.
- [ ] `Required approvals`: doporučeno `1` (pro solo režim dočasně `0`).
- [ ] `Require status checks to pass`: zapnout a vybrat jen existující check names.
- [ ] `Require conversation resolution before merging`: zapnout.
- [ ] `Require review from Code Owners`: zapnout jen pokud máš aktivní maintainery.

### 7.2 Důležité anti-blocking pravidlo

- [ ] Nekombinovat více konfliktních pravidel pro stejný branch (legacy Branch protection + Ruleset zároveň).
- [ ] Po změně pravidel refreshnout PR stránku (starý stav se někdy drží v UI cache).

### 7.3 Secrets/Variables

- [ ] CMS repo: `MODULES_REPO_TOKEN` (read access na private module repa).
- [ ] CMS repo: `FRONTEND_SYNC_TOKEN` + variable `FRONTEND_REPO` pro sync workflow.
- [ ] Frontend repo: případné read tokeny pouze pokud potřebuje cross-repo fetch.
- [ ] Modul repa: jen minimum secrets, ideálně bez sdílených high-scope PAT.

## 8) Doporučený rollout plán (pořadí)

- [ ] Krok 1: Finalizovat CMS ruleset (jedna pravda, žádné duplicitní review pravidlo).
- [ ] Krok 2: Nasadit jednotné `ci.yml` do `forms`, `commerce`, `funnel`, `llm`, `theme-builds`.
- [ ] Krok 3: Nasadit frontend CI (typecheck+build+contract).
- [ ] Krok 4: Přidat nightly cross-repo integrační běh.
- [ ] Krok 5: Po 1 týdnu vyhodnotit flaky checky a upravit scope/timeouts.

## 9) Quick checklist pro merge readiness

- [ ] Všechny required status checks jsou zelené.
- [ ] Ruleset nevyžaduje review navíc proti tvému záměru.
- [ ] Žádný check není required, který se v workflow nespouští.
- [ ] CI nepadá na chybějící private modul, pokud není v scope testu.
- [ ] Frontend má zelené typecheck + build + test.

## 10) Hardening: aby modulové testy nepadaly bez kompletního CMS

- [x] Rozdělit modul CI na `module-only` (required) a `cms-integration` (optional/conditional) joby.
- [x] `module-only` job držet plně nezávislý na CMS checkoutu a na cross-repo tokenech.
- [x] V `module-only` jobu spouštět minimálně: Pint, PHPStan, unit testy modulu.
- [x] `cms-integration` job spouštět pouze pokud je dostupný `CMS_REPO_TOKEN` nebo při `workflow_dispatch`.
- [x] Přidat guard: při chybějícím `CMS_REPO_TOKEN` označit integrační job jako `skipped` (warning), ne jako fail.
- [x] V integračním jobu nepoužívat `php artisan migrate` bez omezení; použít řízený scope migrací (allowlist).
- [x] Přidat do integračního jobu `.env` override: `MODULE_LOAD_MIGRATIONS=true` a konkrétní `MODULE_MIGRATION_ALLOWLIST=<module>`.
- [ ] V branch protection nastavit jako required pouze `module-only` checky; `cms-integration` nechat non-required.
- [ ] Přidat do README každého modulu krátkou sekci „CI režimy“ (co je required vs optional).
- [ ] Ověřit na 1 PR v každém modulu, že bez CMS přístupu merge neblokuje required checky.

## 11) Implementováno v tomto repu (templates)

- [x] Přepsán `.github/workflows/module-ci.yml.template` na režim `module-only required + cms-integration optional`.
- [x] Přidán `.github/workflows/frontend-ci.yml.template` pro public/frontend repozitář.
- [x] Přidán instalační skript `scripts/workflow/install-module-ci-template.sh`.
- [x] Přidán instalační skript `scripts/workflow/install-frontend-ci-template.sh`.
- [x] Přidán branch-protection automatizační skript `scripts/workflow/apply-module-frontend-branch-protection.sh`.
- [x] Přidán rollout runbook `docs/workflow/rollout/module-frontend-ci-rollout.md` (příkazy + required check names + GitHub nastavení).
