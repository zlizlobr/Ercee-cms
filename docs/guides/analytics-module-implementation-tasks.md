# Analytics Module (Provider Registry) — Implementation Tasks (Varianta 1)

## Kontext
- Cíl: nový modul `analytics` s provider registry přístupem:
1. více měřících providerů (GA4, GTM, další do budoucna)
2. zapnutí/vypnutí per provider
3. abstraktní kontrakt pro providery (interface + base class)
4. admin UI: stránka v `Marketing` + operační stránka + widget v hlavním dashboardu
- Architektura musí respektovat `docs/guides/module-development-llm.md`.

## Task 1: Scaffold nového modulu `analytics`
- Popis: Založit modul `ercee/module-analytics` se standardní strukturou (provider, config, routes, tests, workflows, changelog, phpunit).
- Cílový výsledek (akceptační kritéria):
  - Existuje adresář `../ercee-modules/ercee-module-analytics/` se strukturou požadovanou v guide.
  - Modul obsahuje `.github/workflows/{ci,pr-check,release}.yml`, `phpunit.xml`, `tests/TestCase.php`, `tests/Unit/*`.
  - `composer.json` modulu má správný package name, namespace a provider.
- Závislosti: žádné.

## Task 2: Registrace modulu do CMS
- Popis: Připojit modul do hlavní aplikace přes Composer a `config/modules.php`.
- Cílový výsledek (akceptační kritéria):
  - V root `composer.json` je `ercee/module-analytics` v `require`.
  - `config/modules.php` obsahuje záznam `analytics` s providerem a verzí.
  - `php artisan module:list` zobrazuje modul `analytics` jako enabled.
- Závislosti: Task 1.

## Task 3: Návrh doménového modelu pro provider registry
- Popis: Vytvořit modely a migrace pro registry providerů místo jednoho GA/GTM single payloadu.
- Cílový výsledek (akceptační kritéria):
  - Existuje tabulka `analytics_settings` pro globální modulová nastavení (např. consent strategy, global debug, runtime flags).
  - Existuje tabulka `analytics_providers` pro jednotlivé providery (`provider_key`, `enabled`, `config`, `health_meta`, `sort_order`).
  - Existují doménové modely s `casts` pro JSON pole (`config`, `health_meta`).
  - Definované default hodnoty pro první start bez ručního seedu.
- Závislosti: Task 1.

## Task 4: Kontrakty providerů (interface + abstract base class)
- Popis: Zavést abstraktní API pro všechny měřící providery.
- Cílový výsledek (akceptační kritéria):
  - Existuje `AnalyticsProviderInterface` (např. `key()`, `label()`, `validateConfig()`, `buildFrontendPayload()`, `healthCheck()`).
  - Existuje `AbstractAnalyticsProvider` se sdílenými helpery (čtení configu, normalizace, common validation).
  - Kontrakt je nezávislý na konkrétním provideru (GA4/GTM implementují pouze svoje detaily).
- Závislosti: Task 3.

### Doporučení k seed datům (navázané na Task 4/5)
- Přidat idempotentní seeder (např. `AnalyticsProvidersSeeder`) pro inicializaci `analytics_providers` řádků jako `ga4`, `gtm` s default konfigurací.
- Seeder řeší pouze data v DB (`enabled`, `config`, `sort_order`, `health_meta`), nikoliv aplikační logiku providerů.
- Logika providerů zůstává v PHP třídách (`Ga4Provider`, `GtmProvider`), seeder pouze připraví výchozí stav prostředí.

## Task 5: Implementace providerů GA4 a GTM
- Popis: Přidat první dvě konkrétní implementace provider kontraktu.
- Cílový výsledek (akceptační kritéria):
  - Existuje `Ga4Provider` a `GtmProvider` implementující jednotný kontrakt.
  - Každý provider má vlastní validaci configu (formát `G-...`, `GTM-...`).
  - Každý provider vrací frontend payload jen pro povolená a validní data.
- Závislosti: Task 4.

## Task 6: Registry + service vrstva orchestrace
- Popis: Přidat service vrstvu pro správu provider záznamů a běh provider pipeline.
- Cílový výsledek (akceptační kritéria):
  - Existuje `AnalyticsProviderRegistry` (registrace provider tříd a lookup podle `provider_key`).
  - Existuje `AnalyticsSettingsService` a `AnalyticsProviderService` pro CRUD, validaci a enable/disable providerů.
  - Provider a service bindingy jsou registrované v `AnalyticsModuleServiceProvider::registerBindings()`.
- Závislosti: Task 5.

## Task 7: Filament Page 1 — Marketing Settings (provider konfigurace)
- Popis: Vytvořit stránku v `Marketing` pro správu providerů a jejich konfiguračních polí.
- Cílový výsledek (akceptační kritéria):
  - Stránka je v admin menu pod `Marketing`.
  - Umožňuje editovat config per provider (GA4/GTM) a přepínat `enabled`.
  - Uložení persistuje do `analytics_providers` a vrací notifikaci.
- Závislosti: Task 6.

## Task 8: Filament Page 2 — Analytics Operations
- Popis: Vytvořit operační stránku pro health, diagnostiku a runtime kontrolu providerů.
- Cílový výsledek (akceptační kritéria):
  - Stránka zobrazuje status jednotlivých providerů (enabled/disabled, valid/invalid, last check).
  - Obsahuje diagnostické akce (např. `validate all providers`, `run health check`).
  - Health metadata se zapisují do `health_meta`.
- Závislosti: Task 6, Task 7.

## Task 9: Registrace pages a navigace v module provideru
- Popis: Napojit obě stránky přes `getPages()` a zajistit správné umístění v IA.
- Cílový výsledek (akceptační kritéria):
  - `AnalyticsModuleServiceProvider` vrací nové stránky v `getPages()`.
  - Navigace odpovídá IA: konfigurace v `Marketing`, operations odděleně.
  - Není potřeba hardcoded zásah do core `AdminPanelProvider`.
- Závislosti: Task 7, Task 8.

## Task 10: Dashboard widget do hlavního Filament dashboardu
- Popis: Přidat modulový widget (např. `AnalyticsHealthWidget`) přes `getWidgets()`.
- Cílový výsledek (akceptační kritéria):
  - Widget je vidět na hlavním dashboardu spolu s existujícími widgety.
  - Zobrazuje minimálně: počet aktivních providerů, počet nevalidních providerů, čas posledního health checku.
  - Data widgetu jdou přes service vrstvu (bez duplikace logiky).
- Závislosti: Task 6, Task 9.

## Task 11: Runtime výstup pro frontend (provider payload)
- Popis: Implementovat bezpečné skládání frontend payloadu z aktivních providerů.
- Cílový výsledek (akceptační kritéria):
  - Frontend má deterministický payload jen z `enabled` a valid providerů.
  - Render respektuje globální podmínky (např. consent strategy).
  - Do runtime se nedostanou neveřejné nebo interní metadata.
- Závislosti: Task 6, Task 7.

## Task 12: Oprávnění a autorizace
- Popis: Definovat a aplikovat permissions pro konfiguraci, operations a dashboard widget.
- Cílový výsledek (akceptační kritéria):
  - V provideru jsou permissions (např. `view_analytics_settings`, `update_analytics_settings`, `view_analytics_operations`, `view_analytics_widget`).
  - Přístupy ke stránkám a akcím respektují autorizaci.
  - Uživatel bez práv nevidí chráněné položky a akce.
- Závislosti: Task 7, Task 8, Task 10.

## Task 13: Testy modulu (unit + integrační minimum)
- Popis: Doplnit testy pro registry, provider kontrakty, service vrstvu a základní registraci modulu.
- Cílový výsledek (akceptační kritéria):
  - Min. 1 unit test (povinné dle guide), doporučeně testy pro `Ga4Provider`, `GtmProvider`, registry a payload builder.
  - Testy pokrývají: validace configu, enable/disable flow, fallback defaulty, health meta update.
  - `./vendor/bin/phpunit` v modulu projde.
- Závislosti: Task 6, Task 11, Task 12.

## Task 14: Dokumentace a release readiness
- Popis: Zapsat dokumentaci architektury provider registry a finální rollout checklist.
- Cílový výsledek (akceptační kritéria):
  - README modulu popisuje provider kontrakt, přidání nového provideru, permissions, admin stránky a widget.
  - CHANGELOG obsahuje počáteční release entry.
  - Je hotový rollout a rollback postup pro aktivaci analytics na produkci.
- Závislosti: Task 10, Task 11, Task 13.

## Doporučené pořadí realizace
1. Task 1
2. Task 2
3. Task 3
4. Task 4
5. Task 5
6. Task 6
7. Task 7
8. Task 8
9. Task 9
10. Task 10
11. Task 11
12. Task 12
13. Task 13
14. Task 14

## Headless/Public integrace (CMS + `ercee-frontend/src`)

### Task H1: API endpoint pro analytics payload
- Popis: Přidat read endpoint `GET /api/v1/analytics` vracející pouze veřejná runtime data z aktivních providerů.
- Cílový výsledek (akceptační kritéria):
  - Endpoint vrací payload ve tvaru `data.providers[]` + `meta.updated_at`.
  - Payload neobsahuje interní metadata (`health_meta`, interní diagnostiku, neveřejné klíče).
  - Endpoint je napojený na provider registry service (žádná duplikace logiky).
- Závislosti: Task 6, Task 11.

### Task H2: API dokumentace endpointu
- Popis: Zdokumentovat nový endpoint v backend docs.
- Cílový výsledek (akceptační kritéria):
  - Vznikne `docs/api/endpoints/analytics.md`.
  - Endpoint je zapsaný v `docs/api/README.md`.
  - Dokumentace obsahuje response schema a bezpečnostní poznámky (co se nikdy nevrací do frontendu).
- Závislosti: Task H1.

### Task H3: Frontend feature API vrstva
- Popis: V `ercee-frontend` přidat klienta `src/features/site/api/analytics.ts` + typy v `src/shared/api/types.ts`.
- Cílový výsledek (akceptační kritéria):
  - Existuje `getAnalyticsConfig()` přes `fetchApi('/analytics')`.
  - Typy pokrývají provider payload (GA4, GTM a rozšiřitelné pole pro další providery).
  - `src/features/site/api/index.ts` exportuje analytics API.
- Závislosti: Task H1.

### Task H4: Integrace do `BaseLayout.astro` (`<head>`)
- Popis: Načítat analytics payload v layoutu a renderovat script tagy do `<head>` kontrolovaně podle provider typu.
- Cílový výsledek (akceptační kritéria):
  - `src/layouts/BaseLayout.astro` načítá analytics data spolu s theme/cookies.
  - Script render je centralizovaný (helper/component), ne inline ad-hoc podmínky po celém layoutu.
  - Vyrenderují se jen skripty aktivních a valid providerů.
- Závislosti: Task H3.

### Task H5: Consent gating pro analytics skripty
- Popis: Napojit analytics skripty na existující consent mechaniku (`data-consent-category` + event `cookie-consent-updated`).
- Cílový výsledek (akceptační kritéria):
  - Analytics skripty mají správnou consent kategorii (`analytics` nebo `marketing` dle mapování).
  - Skripty se nespustí bez uděleného souhlasu.
  - Po změně consentu proběhne aktivace skriptů bez reloadu stránky.
- Závislosti: Task H4.

### Task H6: Build cache manifest rozšíření pro analytics změny
- Popis: Upravit `ercee-frontend/scripts/build-with-cache.mjs`, aby `globalStamp` zahrnoval i `GET /analytics` (a srovnatelně i cookies endpoint, který layout již používá).
- Cílový výsledek (akceptační kritéria):
  - `computeManifest()` načítá `meta.updated_at` pro `/analytics`.
  - Změna analytics konfigurace invaliduje routy deterministicky přes changed-routes mechaniku.
  - Nedochází k falešně “nezměněným” stránkám při změně globálních head dat.
- Závislosti: Task H1.

### Task H7: Rebuild strategie (incremental vs full)
- Popis: Definovat provozní pravidlo, kdy stačí inkrementální build a kdy je nutný full build.
- Cílový výsledek (akceptační kritéria):
  - Pro změnu analytics dat (CMS) se používá standardní `build-with-cache` flow, ne vynucený `FULL_BUILD=1`.
  - `FULL_BUILD=1` je vyžadován jen při změně layout/render logiky (kód `BaseLayout`, renderer komponent, routing).
  - Pravidlo je zapsané v docs a použitelné v CI/rebuild endpoint flow.
- Závislosti: Task H6.

### Task H8: Frontend docs a test flow update
- Popis: Aktualizovat frontend dokumentaci a test checklist o analytics endpoint + head runtime.
- Cílový výsledek (akceptační kritéria):
  - V `ercee-frontend/docs/api/endpoints/` je přidán endpoint `analytics`.
  - V architektuře/layout docs je popsán source analytics dat v `BaseLayout`.
  - V testing flow je krok ověřující render/gating analytics scriptů.
- Závislosti: Task H5, Task H7.
