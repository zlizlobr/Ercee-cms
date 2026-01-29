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
- [~] **Vydefinovat core domény** (ponechat): Content (Page, Navigation, Menu), Media, ThemeSetting; zmapovat do `/app/Domain/Content` a `/app/Domain/Media` a potvrdit hranice. (core i business domény jsou stále v `app/Domain/*`)
- [~] **Identifikovat business domény** pro modularizaci: `Form`, `Commerce`, `Funnel`, `Subscriber` + napojení na Filament resources. (domény existují, ale stále v core)
- [x] **Založit core namespace** – zatím ponecháno `App\`, připraveno pro budoucí extrakci do `Ercee\CmsCore\`.
- [x] **Zavést core Contracts layer** (interfaces, events, DTO) pro komunikaci s moduly → `app/Contracts/Module/`, `app/Contracts/Events/`, `app/Contracts/Services/`.
- [~] **Zavést Module Manager** (registry) v core: načtení modulů z configu, lifecycle hooky, DI registrace → `app/Support/Module/ModuleManager.php`. (základ hotov; chybí validace `version` a použití `dependencies` z `config/modules.php`)
- [x] **Definovat Composer autoload mapy** pro core a moduly (PSR‑4) a stabilizovat namespace konvence → `Modules\*` namespace.
- [~] **Stabilizovat event bus** (Laravel events) pro integrace: core emituje eventy → `ContentPublished`, `MenuUpdated`, `MediaUploaded`. (eventy existují, ale nikde nejsou dispatchované)

### Phase 2 – Modulový systém & registrace
- [x] **Zvolit formát registrace modulů**: `config/modules.php` implementováno.
- [~] **Implementovat registraci modulů**: `enabled`, `provider`, `version`, `dependencies`, `migrations`, `routes`, `policies`, `permissions` → `ModuleManager.php`. (enabled/provider/migrations/routes/policies/permissions fungují; chybí validace `version` a řešení `dependencies` z configu)
- [x] **Implementovat modulový service provider** → `BaseModuleServiceProvider.php` s interfaces pro `register()`, `boot()`, routes, events, policies.
- [x] **Zavést izolaci assets** (frontend/admin): modulové view/asset namespace + publish do `public/vendor/<module>` → v `BaseModuleServiceProvider`.
- [x] **Rozšířit DI registrace** → `ModuleServiceProvider.php` registruje ModuleManager a volá register/boot.
- [x] **Definovat modulové config merging** (Laravel `mergeConfigFrom`) s jasnými prefixy `module.<name>.*`.
- [x] **Zavést modulové migrations discovery** (oddělené od core migrations) → `HasMigrationsInterface`.
- [x] **Riziko**: kolize jmen v configu a views; zaveden prefix `module.<name>.*`.

### Phase 3 –
- [x] **Forms modul**: kompletní struktura v `modules/forms/` včetně Domain, Application a Filament Resources (FormResource, ContractResource). App aliasy zachovány pro zpětnou kompatibilitu.
- [x] **Funnel modul**: kompletní struktura v `modules/funnel/` včetně Domain, Application a Filament Resources (FunnelResource, FunnelRunResource). App aliasy zachovány pro zpětnou kompatibilitu.
- [x] **E‑commerce modul**: kompletní struktura v `modules/commerce/` včetně Domain a Filament Resources (ProductResource, OrderResource, PaymentResource, AttributeResource, TaxonomyResource, ProductReviewResource). App aliasy zachovány pro zpětnou kompatibilitu.
- [x] **Subscriber modul**: ponecháno v core jako sdílená entita (používaná všemi moduly). (`app/Domain/Subscriber`, `app/Filament/Resources/SubscriberResource`)
- [ ] **Custom blocks**: vyčlenit `app/Filament/Blocks/*` do modulů dle domény (form bloky do forms modulu, atd.).
- [ ] **Integrace**: izolovat `app/Infrastructure/*` (např. GitHub dispatch) do dedikovaných modulů/integrací.
- [x] **Zavést standard modulového repa** (struktura `src/`, `routes/`, `resources/`, `database/`, `config/`, `composer.json`) → implementováno.
- [ ] **Sladit migraci s cílovou strukturou**: aktualizovat nebo nahradit migrační skript tak, aby pracoval s novým core namespace a module registry.
- [ ] **Kontrolní checklist migrace**: symlink setup, composer path repo, autoload, provider registrace, migrace, testbench.
- [ ] **Riziko**: shared modely/relationships (např. Page ↔ Block) a hardcoded cesty/namespace ve skriptu; zavést contracts/DTO a parametrizaci migrace.

### Phase 4 – Admin extensibility
- [~] **Modulové admin registry**: `AdminExtensionInterface` umožňuje modulům registrovat Resources/Pages/Widgets. (interface + ModuleManager existují, ale nejsou napojeny na Filament panel)
- [~] **Modulové menu**: `getNavigationItems()` v `AdminExtensionInterface` pro přidávání položek. (sběr existuje v ModuleManageru, ale není použit v `AdminPanelProvider`)
- [~] **Permissions**: permissions definovány v modulech, zbývá implementovat seedery. (seedery zatím nevytvářejí permissions)
- [~] **UI components**: `getBlocks()` v `AdminExtensionInterface` pro registraci bloků. (ModuleManager sbírá bloky, ale `BlockRegistry` je nepoužívá a stále bere jen `app/Filament/Blocks`)
- [ ] **Riziko**: prefixy `module:<name>:` zavedeny v config/permissions. (v repu nevidím zavedené prefixy)

### Phase 5 – Release, versioning & update flow
- [ ] **Rozdělit repo**: core jako Composer package (samostatný repo), moduly jako samostatné repa nebo mono‑repo s path repositories.
- [ ] **Nastavit CI matrix**: core testy vs modulové testy; modulové testy běží s test fixture core.
- [ ] **Zavést semantic versioning** pro core i moduly (major při změně kontraktů).
- [ ] **Definovat upgrade guide**: kompatibilita core↔modul a minimální verze core v modulech.
- [ ] **Release flow**: tagování core, následné releasy modulů; automatisované composer constraints.
- [ ] **Developer workflow**: standardizovat lokální vývoj modulů (2 repo, symlink, dvojí git status) a uložit do core `docs`.
- [ ] **Release pipeline pro moduly**: CI šablony, tagging standard, `path repo` pro dev a VCS repo pro produkci.
- [ ] **Riziko**: lock‑in mezi verzemi; vynutit `requires` v `composer.json` modulů.

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
