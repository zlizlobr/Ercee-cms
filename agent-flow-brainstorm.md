# Agent Flow Brainstorm

Tento dokument zachycuje navrzeny agentni workflow pro Ercee ekosystem.
Zvolena je varianta s pipeline a stage gates.

## Cile

- Mit oddelene agenty pro moduly, blocks, field types, testy a docs.
- Sjednotit predavani prace mezi agenty.
- Zajistit konzistentni kvalitu pres test gate a docs gate.

## Zvolena varianta

Pouzijeme pipeline agentu se stage gates:

1. Spec and Plan
2. Implementace
3. Test Gate
4. Ralph Review Gate (agent-only)
5. Docs Gate
6. Release Readiness Check

## Agenti a odpovednosti

### 1) module-builder-agent

- Vytvari a upravuje moduly podle:
  `docs/guides/module-development-llm.md`
- Resi scaffold, provider, registraci, dependency a event integrace.
- Dava vystup ve formatu:
  - scope zmen
  - changed files
  - open risks

### 2) block-builder-agent

- Vytvari a upravuje CMS blocky podle:
  `docs/cms-block-integration-guide.md`
  `docs/reference/content/block-contract.md`
- Hlida kontrakt `type + data`, preview a mapovani do frontendu.

### 3) field-type-agent

- Resi form field schema a validace podle:
  `docs/guides/forms/form-schema-rules.md`
- Hlida kompatibilitu schema, validation rules a data_options.

### 4) test-runner-agent

- Vynucuje test flow A -> B -> C podle:
  `docs/guides/testing-flow-llm.md`
- Reportuje:
  - co bylo spusteno
  - co proslo / neproslo
  - blockery a navrh dalsiho kroku

### 5) review-agent (Ralph loop)

- Bezi uvnitr Ralph loopu bez human vstupu.
- Kontroluje pravidla architektury, kontraktu a test coverage.
- Vystup:
  - findings podle severity (blocker, major, minor)
  - rule_id
  - required_fix
  - auto_fixable yes/no

### 6) docs-editor-agent

- Aktualizuje dokumentaci podle:
  `docs/standards/documentation.md`
- Hlida naming, strukturu a canonical odkazy.
- Udrzuje implementacni task dokumenty a changelog notes.

## Stage Gates

### Gate 1: Spec and Plan

- Definovany scope.
- Vybrany odpovedny agent.
- Identifikovany dotcene kontrakty (module/block/field/API).

### Gate 2: Implementace

- Kod je hotovy.
- Zmeny jsou lokalizovane v relevantnich vrstvach.
- Nejsou porusena pravidla cross-module komunikace.

### Gate 3: Test Gate

- A) preflight + verify
- B) unit + contract checks
- C) runtime smoke / e2e (pokud je releasova nebo UI kriticka zmena)

### Gate 4: Ralph Review Gate (agent-only)

- Povinny review krok uvnitr Ralph loopu.
- Pokud jsou blocker findings, task se vraci do implementace.
- Pokud jsou jen major/minor findings, projde s fix-listem do dalsiho kroku.

### Gate 5: Docs Gate

- Docs odpovidaji realne implementaci.
- Odkazy vedou na canonical soubory.
- Pridane/aktualizovane sekce podle standardu.

### Gate 6: Release Readiness

- Shrnuty dopad zmen.
- Zaznamenana rizika.
- Definovany rollback nebo mitigation postup (pokud relevantni).

## Agent-Human Review (mimo Ralph loop)

Tento krok je mimo Ralph orchestraci a slouzi pro hlubsi audit kodu.

- Spousti se po Gate 4 (Ralph Review Gate) a pred nebo po Docs Gate podle typu zmeny.
- Format: agent pripravi findings + navrh fixu, clovek rozhodne finalni prioritu.
- Vhodne pro:
  - komplexni refactory
  - architektonicke zmeny napric moduly
  - high-risk API/permission zmeny
  - release-critical zmeny

Doporuceny vystup:

```md
## Agent-Human Review Output
- Context:
- Agent findings:
- Human decisions:
- Required follow-up:
- Approved for release: yes/no
```

## Predavaci format mezi agenty

Kazdy agent predava vysledek v jednotnem bloku:

```md
## Agent Output
- Scope:
- Files changed:
- Tests run:
- Risks:
- Next handoff:
```

## Linear mapovani (main task + subtasky)

Pipeline je mapovana do Linearu jako initiative strom:

- 1 initiative = 1 parent task (main task)
- jednotlive gates = subtasky
- relace pres `parentId` (lokalni) a `parentLinearId` (po sync/pull)

Minimalni subtask sada pro kazdy parent:

1. Spec/Plan
2. Implementace
3. Test Gate
4. Ralph Review Gate (agent-only)
5. Docs Gate
6. Release Readiness

Volitelny subtask:

- Agent-Human Review (mimo Ralph, pro high-risk scope)

Pravidla proti prehnanemu mnozstvi tasku:

- nevytvaret task per soubor nebo per drobny refactor
- slucovat technicky pribuzne kroky do jednoho implementacniho subtasku
- v jednom planning requestu vytvorit prave jeden novy top-level task

## Minimalni rollout plan

1. Definovat SKILL.md pro 6 agentu.
2. Zavest jednotny handoff format.
3. Definovat minimalni ruleset pro `review-agent` (v1 core rules).
4. Pilotne pouzit flow na 1 zmene v modulech a 1 zmene v blocich.
5. Po pilotu upravit gates na zaklade friction bodu.

## Otevrene body

- Jak prisne vynucovat Gate 3 (C cast) v lokalnim vyvoji.
- Jak casto spoustet Agent-Human Review mimo Ralph (vzdy vs jen high-risk).
- Kde drzet sdilene sablony pro handoff (repo vs central skill assets).
- Kdo schvaluje vyjimky z pipeline pri urgentnim hotfixu.
