# Refactor CMS → Core + Moduly (implementační plán)

## Úvod
Cíl: oddělit čisté a stabilní CMS core (obsah, menu, media, theming) od business logiky (e‑commerce, forms, funnel, integrace) tak, aby core bylo aktualizovatelné přes Composer a moduly byly verzované, rozšiřitelné a nezávislé na core updatech.

Architektonické principy:
- **Core** = minimální CMS doména + admin pro obsah + integrace pro modulový systém.
- **Moduly** = izolovaná business logika + vlastní admin/rozhraní + vlastní migrations/config/permissions.
- **Kontrakty** = stabilní API mezi core ↔ moduly (DI, events, contracts, registrace).

## Fáze refactoru

### Phase 1 – Infrastructure & Core stabilizace
Cíl: vymezit hranice core, připravit modulový runtime a package strukturu.

### Phase 2 – Modulový systém & registrace
Cíl: definovat kontrakty, registry a konfiguraci, aby moduly šly přidávat/odebírat bez zásahu do core.

### Phase 3 – Migrace business logiky do modulů
Cíl: přesun forms, ecommerce, funnel a souvisejících admin resources do modulů.

### Phase 4 – Admin extensibility
Cíl: moduly mohou přidávat menu, stránky, permissions a UI komponenty.

### Phase 5 – Release, versioning & update flow
Cíl: bezpečný update core, samostatné release modulu, CI a testy.

## Task list

### Phase 1 – Infrastructure & Core stabilizace
- [x] **Vydefinovat core domény** (ponechat): Content (Page, Navigation, Menu), Media, ThemeSetting, Subscriber. Hranice potvrzeny — core domény v `app/Domain/Content`, `app/Domain/Media`, `app/Domain/Subscriber`; business domény v `app/Domain/*` jsou backward-compatible aliasy na modulové třídy.
- [x] **Identifikovat business domény** pro modularizaci: `Form`, `Commerce`, `Funnel` identifikovány a přesunuty do modulů. `Subscriber` ponechán v core. Aliasy v `app/Domain/*` zajišťují zpětnou kompatibilitu.
- [x] **Založit core namespace** – zatím ponecháno `App\`, připraveno pro budoucí extrakci do `Ercee\CmsCore\`.
- [x] **Zavést core Contracts layer** (interfaces, events, DTO) pro komunikaci s moduly → `app/Contracts/Module/`, `app/Contracts/Events/`, `app/Contracts/Services/`.
- [x] **Zavést Module Manager** (registry) v core: načtení modulů z configu, lifecycle hooky, DI registrace → `app/Support/Module/ModuleManager.php`. (validace `version` i `dependencies` s podporou `^`, `~`, `>=`, `<` constraints implementována)
- [x] **Definovat Composer autoload mapy** pro core a moduly (PSR‑4) a stabilizovat namespace konvence → `Modules\*` namespace.
- [x] **Stabilizovat event bus** (Laravel events) pro integrace: core emituje eventy → `ContentPublished` (z `PageObserver` při publikaci), `MenuUpdated` (z `MenuObserver` při uložení), `MediaUploaded` (z `MediaObserver` při vytvoření). Modulové eventy: `ContractCreated` (z `SubmitFormHandler`), `OrderPaid` (z `Order::markAsPaid()`).

### Phase 2 – Modulový systém & registrace
- [x] **Zvolit formát registrace modulů**: `config/modules.php` implementováno.
- [x] **Implementovat registraci modulů**: `enabled`, `provider`, `version`, `dependencies`, `migrations`, `routes`, `policies`, `permissions` → `ModuleManager.php`. (vše funguje včetně semver validace verzí a dependency version constraints)
- [x] **Implementovat modulový service provider** → `BaseModuleServiceProvider.php` s interfaces pro `register()`, `boot()`, routes, events, policies.
- [x] **Zavést izolaci assets** (frontend/admin): modulové view/asset namespace + publish do `public/vendor/<module>` → v `BaseModuleServiceProvider`.
- [x] **Rozšířit DI registrace** → `ModuleServiceProvider.php` registruje ModuleManager a volá register/boot.
- [x] **Definovat modulové config merging** (Laravel `mergeConfigFrom`) s jasnými prefixy `module.<name>.*`.
- [x] **Zavést modulové migrations discovery** (oddělené od core migrations) → `HasMigrationsInterface`.
- [x] **Riziko**: kolize jmen v configu a views; zaveden prefix `module.<name>.*`.

### Phase 3 – Migrace business logiky do modulů
- [~] **Forms modul**: plně funkční — resources, bloky, events registrovány. `FormController` migrován na `Modules\Forms\*`. Backward-compatible domain aliasy v `app/Domain/Form/` a `app/Filament/Resources/Form*` zůstávají kvůli Blade šablonám, testům a factories. Zbývá postupně migrovat tyto reference.
- [~] **Funnel modul**: plně funkční — resources registrovány, event listeners (`StartFunnelOnContractCreated`, `StartFunnelOnOrderPaid`) propojeny. Staré `app/Listeners/` odstraněny, registrace z `AppServiceProvider` odstraněna. Backward-compatible aliasy zůstávají.
- [~] **E‑commerce modul**: plně funkční — resources registrovány, `OrderPaid` dispatchován, `PaymentGatewayInterface` binding přesunut do `CommerceModuleServiceProvider`. `CheckoutController` a `WebhookController` migrovány na `Modules\Commerce\*`. Backward-compatible aliasy zůstávají.
- [x] **Subscriber modul**: ponechat v core jako sdílená entita (používaná všemi moduly). (aktuálně `app/Domain/Subscriber`, `app/Filament/Resources/SubscriberResource`)
- [x] **Custom blocks**: form bloky v `modules/forms/`, `BlockRegistry` integruje modulové bloky s deduplicací aliasů. Zbývající bloky v `app/Filament/Blocks/` jsou generické CMS bloky (Hero, Text, Image, CTA, FAQ atd.) — patří do core.
- [x] **Integrace**: `app/Infrastructure/` (GitHubDispatchService, FrontendRebuildService) ponechána v core — jedná se o core CMS infrastrukturu pro frontend rebuild, používanou všemi observery. Není důvod přesouvat do modulu.
- [x] **Zavést standard modulového repa** (struktura `src/`, `routes/`, `resources/`, `database/`, `config/`, `composer.json`) → implementováno.
- [ ] **Sladit migraci s cílovou strukturou**: aktualizovat nebo nahradit migrační skript tak, aby pracoval s novým core namespace a module registry.
- [ ] **Kontrolní checklist migrace**: symlink setup, composer path repo, autoload, provider registrace, migrace, testbench.
- [ ] **Riziko**: shared modely/relationships (např. Page ↔ Block) a hardcoded cesty/namespace ve skriptu; zavést contracts/DTO a parametrizaci migrace.

### Phase 4 – Admin extensibility
- [x] **Modulové admin registry**: `AdminExtensionInterface` umožňuje modulům registrovat Resources/Pages/Widgets. (napojeno na `AdminPanelProvider` — resources, pages, widgets z modulů se registrují přes `ModuleManager`)
- [x] **Modulové menu**: `getNavigationItems()` v `AdminExtensionInterface` pro přidávání položek. (integrováno do `AdminPanelProvider`)
- [x] **Permissions**: permissions definovány v modulech, `RolesAndPermissionsSeeder` používá `ModuleManager::getAllPermissions()` pro registraci s prefixy `module.<name>.<permission>`.
- [x] **UI components**: `getBlocks()` v `AdminExtensionInterface` pro registraci bloků. (`BlockRegistry` integruje modulové bloky přes `ModuleManager`, s deduplicací alias bloků)
- [x] **Riziko**: prefixy `module.<name>.<permission>` zavedeny v `ModuleManager::getAllPermissions()` a používány v `RolesAndPermissionsSeeder`.

### Phase 5 – Release, versioning & update flow
- [~] **Rozdělit repo**: aktuálně mono-repo s `path` repositories v `composer.json`. Moduly mají vlastní `composer.json` s `version`, `type: ercee-module`. Zbývá extrakce do samostatných git repozitářů.
- [x] **Nastavit CI matrix**: `ci.yml` rozšířen — core testy a modulové testy (Forms, Commerce, Funnel) běží separátně.
- [x] **Zavést semantic versioning** pro core i moduly. Verze v `composer.json` i `ServiceProvider`. `ModuleManager` validuje shodu a dependency constraints.
- [ ] **Definovat upgrade guide**: kompatibilita core↔modul a minimální verze core v modulech.
- [ ] **Release flow**: tagování core, následné releasy modulů; automatisované composer constraints.
- [x] **Developer workflow**: standardizováno v `docs/developer-workflow.md` — struktura modulu, registrace, eventy, permissions, verzování, lokální dev, produkce.
- [~] **Release pipeline pro moduly**: CI šablony v `ci.yml`. `path repo` pro dev připraveno. Zbývá VCS repo setup pro produkci a tagging standard.
- [x] **Riziko**: lock‑in mezi verzemi; `requires` zavedeny v `composer.json` modulů (funnel vyžaduje `ercee/module-forms: ^1.0`, `ercee/module-commerce: ^1.0`).

## Návrh modulového API

### Povinné prvky modulu
- `ModuleServiceProvider` implementuje `ModuleInterface`:
  - `register()` – DI bindingy, config merging.
  - `boot()` – event listeners, routes, policies.
  - `admin()` – Filament resources/pages/widgets.
  - `migrations()` – registry migrací.
- `module.json`/`module.php`:
  - `name`, `version`, `description`, `provider`, `dependencies`, `permissions`, `adminMenu`.

### Registrace modulu
- Core načte `config/modules.php` a zaregistruje `provider` pro každý modul s `enabled=true`.
- Dependency resolver ověří verze core a dalších modulů.

### Komunikace s core
- Core emituje eventy: `ContentPublished`, `MediaUploaded`, `MenuUpdated`.
- Moduly se registrují přes event listeners; přímé vazby jen přes contracts/DTO.
- Sdílené služby přes DI kontejnery s kontrakty v core (`Contracts/*`).

## Migrace existující logiky

### Identifikace kandidátů
- Domény s vlastním databázovým modelem + vlastní admin UI = modul.
- Vše, co není Content/Media/Menu/ThemeSetting, přesunout mimo core.

### Příklady
- **Bloky**: `Filament/Blocks/*` → modul blocks (nebo tematické moduly).
- **Služby**: `Application/Commerce/*` → ecommerce modul.
- **Admin stránky**: `Filament/Resources/Funnel*` → funnel modul.

### Postup migrace
- Vytvořit modul, přesunout domain + application + admin + migrations.
- Nahradit přímé reference z core → contracts/events.
- Otestovat migraci na staging branch, spustit migrace a admin registrace.

## Composer & versioning

### Rozdělení repozitářů
- **Core**: `ercee/cms-core` (Composer package).
- **Moduly**: `ercee/module-forms`, `ercee/module-ecommerce`, `ercee/module-funnel`.
- Alternativa: mono‑repo s `path` repositories pro lokální dev.

### Závislosti
- Moduly mají `require: ercee/cms-core:^X.Y`.
- Core nesmí vyžadovat moduly (jen contracts a eventy).

### Semantic versioning
- **Major**: změna modulových kontraktů/core API.
- **Minor**: nové features bez breaking změn.
- **Patch**: bugfix.

## Bezpečný update flow
- Core update pouze přes Composer; moduly zůstávají oddělené.
- Před update: zkontrolovat kompatibilitu modulů s core (`composer` constraints + release notes).
- Automatizované testy: core unit/integration + modulové smoke tests.
- Release proces: tag core → aktualizace modulových constraints → CI → deploy.
