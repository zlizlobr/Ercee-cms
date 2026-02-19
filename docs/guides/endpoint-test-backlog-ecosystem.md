# Endpoint Test Backlog (Ercee-cms)

Tento backlog prevadi test matrix na konkretni ukoly pro aktualni codebase.

Pravidla:
- endpoint neni "hotovy", pokud ma jen `assertJsonStructure`.
- pro write endpointy je povinna idempotence/retry branch.
- pro public endpointy je povinna negativni branch (`404/422/429/401/403` podle contractu).

## 1) API endpointy

| Endpoint | Aktualni stav | Chybejici testy | Cilovy test file |
|---|---|---|---|
| `GET /api/health` | [x] Pokryto behavior testy (`ModuleRoutesTest`) | - | `tests/Feature/Modules/ModuleRoutesTest.php` |
| `POST /api/internal/rebuild-frontend` | Castecne pokryto (`RebuildFrontendEndpointTest`) | [x] auth fail bez tokenu, [x] happy path dispatch rebuild, [ ] throttle branch | `tests/Feature/Internal/RebuildFrontendEndpointTest.php` |
| `GET /api/v1/analytics` | Bez testu | Contract payload, fallback precedence provider configu, nevalidni config branch | Novy `tests/Feature/AnalyticsEndpointTest.php` |
| `POST /api/v1/checkout` | Castecne pokryto (`CheckoutFlowTest`) | Idempotence key bez duplikace order/payment, rate-limit 429, retry behavior | `tests/Feature/CheckoutFlowTest.php` |
| `GET /api/v1/cookies/config` | Castecne pokryto | Compliance invariant (`necessary.required/default_enabled`), invalid policy links guardrails, race-like cache consistency | `tests/Feature/CookieConfigEndpointTest.php` |
| `GET /api/v1/forms/{id}` | [x] Pokryto behavior+contract testy (`ModuleRoutesTest`) | - | `tests/Feature/Modules/ModuleRoutesTest.php` |
| `POST /api/v1/forms/{id}/draft` | Bez testu | Happy path draft bez final side effects, validation 422, throttle 429 | Novy `tests/Feature/FormDraftSubmissionTest.php` |
| `POST /api/v1/forms/{id}/submit` | Castecne pokryto (`FormSubmissionTest`) | Idempotence key, rate-limit 429, retry deduplikace side effects | `tests/Feature/FormSubmissionTest.php` |
| `GET /api/v1/media` | Castecne pokryto | Deterministicke ordering/paging invarianty | `tests/Feature/Media/MediaApiTest.php` |
| `GET /api/v1/media/{uuid}` | Castecne pokryto | Payload integrity (returned uuid must match request) | `tests/Feature/Media/MediaApiTest.php` |
| `POST /api/v1/media/resolve` | Castecne pokryto | Poradi vystupu podle vstupu ids (pokud contract), duplicate ids behavior | `tests/Feature/Media/MediaApiTest.php` |
| `GET /api/v1/navigation` | Bez testu | Default menu fallback, only active nav items, deterministic ordering | Novy `tests/Feature/NavigationEndpointTest.php` |
| `GET /api/v1/navigation/{menuSlug}` | Bez testu | Existing menu payload, missing menu safe fallback/404, inactive items filtered | `tests/Feature/NavigationEndpointTest.php` |
| `GET /api/v1/menus/{menuSlug}` | Bez testu | Contract parity s `/navigation/{menuSlug}`, fallback branch | `tests/Feature/NavigationEndpointTest.php` |
| `GET /api/v1/pages` | [x] Pokryto (`PageEndpointContractTest`) | - | `tests/Feature/PageEndpointContractTest.php` |
| `GET /api/v1/pages/{slug}` | [x] Pokryto (`PageEndpointContractTest`) | - | `tests/Feature/PageEndpointContractTest.php` |
| `GET /api/v1/products` | Castecne pokryto (`ModuleRoutesTest`) | Inactive products filter, pricing invariants | `tests/Feature/Modules/ModuleRoutesTest.php` nebo novy `tests/Feature/ProductEndpointTest.php` |
| `GET /api/v1/products/{id}` | Castecne pokryto (`ModuleRoutesTest`) | 404/410 inactive or missing, stock invariant branch | `tests/Feature/Modules/ModuleRoutesTest.php` nebo `tests/Feature/ProductEndpointTest.php` |
| `GET /api/v1/taxonomies/mapping` | Castecne pokryto | Additional edge cases na nekonzistentni vazby | `tests/Feature/TaxonomyMappingEndpointTest.php` |
| `GET /api/v1/taxonomy-mapping` | Bez testu | Alias parity test proti `/taxonomies/mapping` | `tests/Feature/TaxonomyMappingEndpointTest.php` |
| `GET /api/v1/theme` | Castecne pokryto dobre | Full precedence matrix, failure branch pri media resolve miss, irrelevant update should not change payload | `tests/Feature/ThemeEndpointTest.php` |
| `POST /api/v1/theme-builds` | Bez testu | Validation 422, create + dispatch side effect, throttle | Novy `tests/Feature/ThemeBuilds/ThemeBuildStoreEndpointTest.php` |
| `GET /api/v1/theme-builds/{id}` | Bez testu | 404 missing id, allowed status transitions integrity | Novy `tests/Feature/ThemeBuilds/ThemeBuildStatusEndpointTest.php` |
| `GET /api/v1/theme-builds/{id}/download` | Bez testu | Download only for done status, processing/failed 4xx branch | `tests/Feature/ThemeBuilds/ThemeBuildStatusEndpointTest.php` |
| `POST /api/webhooks/stripe` | Castecne pokryto (`WebhookProcessingTest`) | Replay protection (same event id), out-of-order events handling | `tests/Feature/WebhookProcessingTest.php` |

## 2) Public web endpointy

| Endpoint | Aktualni stav | Chybejici testy | Cilovy test file |
|---|---|---|---|
| `GET /` | Bez behavior testu | Render only published/home content, fallback when no published home | Novy `tests/Feature/FrontendRoutesTest.php` |
| `GET /{slug}` | Bez behavior testu | 404 missing/unpublished slug, payload/render parity | `tests/Feature/FrontendRoutesTest.php` |
| `GET /products` | Bez behavior testu | Public listing excludes inactive products | Novy `tests/Feature/StorefrontRoutesTest.php` |
| `GET /products/{id}` | Bez behavior testu | Missing/inactive product branch | `tests/Feature/StorefrontRoutesTest.php` |
| `GET /checkout/{productId}` | Bez behavior testu | Inactive product blocked, happy path with active product | `tests/Feature/StorefrontRoutesTest.php` |
| `GET /payment/return` | Bez behavior testu | Status mapping (`paid/failed/pending`) to user-facing branch | `tests/Feature/StorefrontRoutesTest.php` |
| `GET /thank-you` | Bez behavior testu | Guardrails for invalid flow context | `tests/Feature/StorefrontRoutesTest.php` |
| `GET /media/{path}` | Bez testu | 404 for missing file, cache headers only for existing file | Novy `tests/Feature/MediaServeRouteTest.php` |
| `GET /lang/{locale}` | Bez testu | Supported locale updates session, unsupported locale does not mutate session | Novy `tests/Feature/LocaleSwitchRouteTest.php` |

## 3) Admin a preview endpointy

| Endpoint skupina | Aktualni stav | Chybejici testy | Cilovy test file |
|---|---|---|---|
| `GET /admin/pages/{page}/preview` | Bez testu | Guest redirect/401, authenticated preview reflects draft content | Novy `tests/Feature/Admin/PagePreviewEndpointTest.php` |
| `GET /admin/products/{product}/preview` | Bez testu | Guest redirect/401, preview contract for draft product | Novy `tests/Feature/Admin/ProductPreviewEndpointTest.php` |
| Filament `/admin/*` resources/pages | Pokryto jen login smoke v Playwright | Per resource: `index access`, `permission deny`, `create validation`, `edit mutation guard` | Novy `tests/Feature/Admin/FilamentAccessMatrixTest.php` + rozsireni `tests/e2e/admin-login.spec.ts` |

## 4) Priorita implementace backlogu

1. P0: `checkout`, `forms submit`, `webhooks stripe`, `cookies config`, `theme`.
2. P1: `pages/navigation/products/taxonomy alias`, `forms draft`, `theme-builds`.
3. P2: public web routes a admin preview + filament access matrix.

## 5) Definition of Done pro kazdy TODO endpoint

- Test obsahuje behavior assertion (ne jen shape).
- Test obsahuje negativni branch.
- Write endpoint testuje idempotence/retry nebo explicitne dokumentuje proc neni relevantni.
- Side effects jsou overene (`assertDatabaseHas/Count`, `Event::assertDispatched`, status transitions).
