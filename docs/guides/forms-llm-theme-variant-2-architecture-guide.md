# Forms + LLM + Theme Builds: Varianta 2 (Balanced) Architecture Guide

Toto je detailni, junior-friendly navrh propojeni modulu `forms`, `llm` a `theme-builds`.
Varianta 2 je kompromis: ma dobry audit a stabilitu, ale jeste neni enterprise "tezka".

## Proc to delame

Kdyz uzivatel odesle formular, chceme z jeho odpovedi:

1. Vygenerovat navrh `theme.json` pres LLM.
2. Vytvorit realny frontend build (zip artifact).
3. Mit jasny stav, co se stalo (uspech/chyba/retry), aby to videl admin.

## Slovnicek (pro juniora)

- `Contract`: zaznam o odeslanem formulari (lead).
- `ContractCreated`: event, ktery se vyvola po uspesnem submitu.
- `Prompt`: instrukce pro LLM (co ma vygenerovat).
- `Theme JSON`: strukturovany JSON s design tokeny a nastavenim tematu.
- `Theme Build`: proces, ktery vezme `theme.json` a vyrobi `dist.zip`.
- `Queue job`: asynchronni ukol na pozadi.
- `Idempotence`: ochrana proti tomu, aby se stejna vec nespustila vicekrat.

## Co uz dnes existuje (reference)

### Repo mapa (local + git)

| Komponenta | Local path | Git repo |
| --- | --- | --- |
| CMS core | `/usr/local/var/www/Ercee-cms` | <https://github.com/zlizlobr/Ercee-cms> |
| Forms module | `/usr/local/var/www/ercee-modules/ercee-module-forms` | <https://github.com/zlizlobr/ercee-module-forms> |
| LLM module | `/usr/local/var/www/ercee-modules/ercee-module-llm` | <https://github.com/zlizlobr/ercee-module-llm> |
| Theme Builds module | `/usr/local/var/www/ercee-modules/ercee-module-theme-builds` | <https://github.com/zlizlobr/ercee-module-theme-builds> |
| Funnel module | `/usr/local/var/www/ercee-modules/ercee-module-funnel` | <https://github.com/zlizlobr/ercee-module-funnel> |
| Frontend | `/usr/local/var/www/ercee-frontend` | <https://github.com/zlizlobr/ercee-frontend> |

### Konkretni vstupni body v kodu

- Forms submit + `ContractCreated`:
  - Local: `/usr/local/var/www/ercee-modules/ercee-module-forms/src/Application/SubmitFormHandler.php`
  - Git: <https://github.com/zlizlobr/ercee-module-forms/blob/main/src/Application/SubmitFormHandler.php>
- Forms API:
  - Local: `/usr/local/var/www/ercee-modules/ercee-module-forms/routes/api.php`
  - Git: <https://github.com/zlizlobr/ercee-module-forms/blob/main/routes/api.php>
- LLM orchestrace:
  - Local: `/usr/local/var/www/ercee-modules/ercee-module-llm/src/Services/LlmManager.php`
  - Git: <https://github.com/zlizlobr/ercee-module-llm/blob/main/src/Services/LlmManager.php>
- Theme Build API + worker:
  - Local: `/usr/local/var/www/ercee-modules/ercee-module-theme-builds/src/Http/Controllers/ThemeBuildController.php`
  - Git: <https://github.com/zlizlobr/ercee-module-theme-builds/blob/main/src/Http/Controllers/ThemeBuildController.php>
  - Local: `/usr/local/var/www/ercee-modules/ercee-module-theme-builds/src/Listeners/RunThemeBuild.php`
  - Git: <https://github.com/zlizlobr/ercee-module-theme-builds/blob/main/src/Listeners/RunThemeBuild.php>

## Navrh architektury (jednoduse)

Myslenka:

- Forms zustane vlastnik submitu.
- LLM modul zustane vlastnik komunikace s AI providery.
- Theme Builds zustane vlastnik build procesu.
- Nova "lepidlo" logika bude v orchestration vrstvach (Forms + CMS core).

To je dulezite: nebudeme michat zodpovednosti mezi moduly.

## Detailni flow krok za krokem

### Krok 1: Uzivatel odesle formular

- Endpoint: `POST /api/v1/forms/{id}/submit`.
- Pokud validace projde, vznikne `contract`.
- Forms dispatchne event `ContractCreated`.

### Krok 2: Spusti se listener pro generaci tematu

- Novy listener: `GenerateThemeOnContractCreated`.
- Ulozi zaznam do nove tabulky `theme_generations` se stavem `queued`.
- Naplanuje queue job `GenerateThemeFromContractJob`.

### Krok 3: Queue job pripravi prompt pro LLM

Job nacte:

- `contract.data` (odpovedi uzivatele),
- `form.schema` (struktura formulare),
- `form.data_options.flow_variant` (A/B varianta).

Job vytvori prompt podle `prompt_version` (napr. `v1`), aby byl stabilni a versionovany.

### Krok 4: LLM vrati navrh theme JSON

- Volani pres `LlmManager::complete()`.
- Chceme striktni JSON, ne volny text.
- Ukladame metadata: provider, model, correlation id.

### Krok 5: Validace a sanitizace

Po navratu z LLM:

1. Parse JSON.
2. Schema validation.
3. Policy sanitize (allowlist, regex, limity).

Kdyz validace neprojde:

- zaznam dostane `llm_error`,
- ulozi se `error_code`, `error_message`,
- optional fallback na default theme.

### Krok 6: Spusteni Theme Build

- Pri validnim outputu vytvorime build request.
- Doporuceni: interni service call, ne HTTP "backend vola sam sebe".
- Stav se zmeni na `build_running`.

### Krok 7: Dokonceni buildu

- Pri uspechu: `build_success`, ulozi se `theme_build_id` a `download_url`.
- Pri chybe: `build_error` + detail chyby.
- Admin u contractu vidi aktualni stav a muze retry.

## Jednoduchy priklad datoveho toku

Priklad vstupu z contractu:

```json
{
  "website_size": "five_pages",
  "trade_category": "elektrikar",
  "city_region": "Brno",
  "layout_preference": "modern_clean"
}
```

Priklad ciloveho LLM vystupu (zkracene):

```json
{
  "theme": {
    "name": "Modern Electric Brno",
    "colors": {
      "primary": "#1f2937",
      "accent": "#f97316"
    }
  }
}
```

## Datovy model (nova tabulka)

Nova tabulka: `theme_generations`.

Povinna pole:

- `id`
- `contract_id` (FK na `contracts`)
- `form_id` (FK na `forms`)
- `status` (`queued`, `llm_running`, `llm_success`, `llm_error`, `build_running`, `build_success`, `build_error`)
- `prompt_version` (napr. `v1`)
- `input_snapshot` (JSON)
- `llm_provider`
- `llm_model`
- `llm_correlation_id`
- `theme_json_validated` (JSON, nullable)
- `theme_build_id` (nullable)
- `download_url` (nullable)
- `error_code`, `error_message` (nullable)
- `attempt`, `max_attempts`
- `created_at`, `updated_at`

Doporucene indexy:

- `(contract_id)` pro rychle otevreni detailu contractu.
- `(status, updated_at)` pro monitoring fronty.
- `(theme_build_id)` pro lookup z build callbacku.

Idempotence:

- unikát `(contract_id, prompt_version)` zabrani duplicitnimu spusteni stejne verze.

## Zmeny podle repozitare (co presne upravit)

### 1) Forms module

Pridat:

- listener registraci:
  - `ContractCreated => GenerateThemeOnContractCreated`
- nove tridy:
  - `src/Listeners/GenerateThemeOnContractCreated.php`
  - `src/Jobs/GenerateThemeFromContractJob.php`
  - `src/Domain/Services/ThemePromptBuilder.php`
  - `src/Domain/Services/ThemeGenerationOrchestrator.php`
- model + migraci:
  - `src/Domain/ThemeGeneration.php`
  - `database/migrations/*_create_theme_generations_table.php`
- admin UI v Contract detailu:
  - status badge, error detail, retry action.

### 2) LLM module

Doplnit:

- helper na structured output (`JSON only`),
- metadata export (provider/model/token usage/correlation id),
- interface pro prompt profil:
  - `ThemeGenerationPromptInterface`.

### 3) Theme Builds module

Doplnit:

- interni service:
  - `ThemeBuildCreator::createFromThemePayload(array $theme, ?string $callbackUrl = null)`
- zachovat existujici API endpointy pro externi klienty beze zmen.

### 4) CMS core

Doplnit:

- policy/sanitizer vrstvu pro theme pravidla,
- optional read endpoint:
  - `GET /api/v1/contracts/{id}/theme-generation`.

## Prompt strategie (prakticky)

Vstupy:

- `contract.data`
- `form.schema`
- `form.data_options.flow_variant`

Vystup:

- striktny JSON objekt s klicem `theme`.

Guardrails:

- max delka textu,
- regex na barvy (hex),
- zakazane CSS patterny,
- fallback na default theme pri failu.

## Operacni pravidla

Queue:

- dedikovana fronta `theme-generation`.

Retry:

- LLM retry max `2` (exponential backoff).
- Build retry max `1`.

Timeout:

- LLM `30-60s`.
- Build dle `THEME_BUILD_TIMEOUT`.

Logovani:

- vzdy logovat `contract_id`, `theme_generation_id`, `llm_correlation_id`.

## Bezpecnost

- Nikdy nelogovat API key.
- Maskovat citliva pole (`email`) v `input_snapshot`.
- Validovat `callback_url`, pokud se pouzije webhook.

## Rollout plan (bezpecne nasazeni)

1. Faze A:
   - DB + model + listener + log-only mode.
2. Faze B:
   - LLM call + validation + dry-run (bez real buildu).
3. Faze C:
   - real build + admin status.
4. Faze D:
   - retry controls + monitoring dashboard.

## Rizika a mitigace

- Riziko: LLM vrati neplatny JSON.
- Mitigace: strict parser + schema validator + fallback.

- Riziko: build lock / fronta se zasekne.
- Mitigace: queue isolation + backoff + timeout.

- Riziko: stejny contract spusti flow vicekrat.
- Mitigace: idempotence constraint `(contract_id, prompt_version)`.

## Definition of Done (aby junior vedel, kdy je hotovo)

Hotovo je az kdyz plati vsechno:

1. Po submitu formulare vznikne zaznam v `theme_generations`.
2. Job korektne prepina stavy (`queued -> llm_running -> ...`).
3. Pri validnim LLM vystupu vznikne `theme_build_id`.
4. Pri chybe je v DB citelna chyba (`error_code`, `error_message`).
5. V adminu u contractu je videt status + retry.
6. Existuji testy aspon pro:
   - happy path,
   - invalid JSON z LLM,
   - build failure,
   - idempotence.
