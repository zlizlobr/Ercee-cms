# Analýza kompatibility agents workflow s Claude Code

## 1. Současný stav: co funguje pro Codex

Agents repo (`/usr/local/var/www/agents/`) je postaveno na modelu **OpenAI Codex skills**:

- Každý agent = `SKILL.md` soubor (Codex "skill" formát s YAML frontmatter)
- Orchestrace přes `run-tasks-agent` s command triggers (`runTasks/ execute`)
- Agent invokace stylem `$module-builder-agent` (Codex skill reference)
- Konfigurace v `agents/openai.yaml` (OpenAI-specifický interface descriptor)
- "Ralph" = Codex execution engine pro implementaci + fix loop

---

## 2. Kompatibilita s Claude Code

### Co je přímo kompatibilní (funguje bez změn)

| Oblast | Proč funguje |
|--------|-------------|
| SKILL.md jako markdown instrukce | Claude Code čte `.md` soubory nativně, obsah je validní prompt |
| Gate governance logika | Čistě textová pravidla, LLM-agnostická |
| Evidence contract (artifacts/) | Souborový systém, nezávislý na LLM |
| Blocker/failure policy | Textové instrukce, fungují pro oba |
| Command matrix (php artisan, npm) | Shell příkazy, LLM-agnostické |
| Linear toolkit skripty | Bash/Python, nezávislé na LLM |
| `.linear/data/tasks.json` formát | JSON schema, LLM-agnostický |

### Co NEFUNGUJE / vyžaduje adaptaci

| Oblast | Problém | Závažnost |
|--------|---------|-----------|
| `$agent-name` invokace | Codex skill reference syntax — Claude Code tohle nezná | **Kritická** |
| `openai.yaml` interface | OpenAI-specifický formát, Claude Code ignoruje | **Střední** |
| YAML frontmatter v SKILL.md | Codex parsuje `---name/description---`, Claude Code to čte jen jako text | **Nízká** |
| "Ralph" jako execution engine | V Codexu = skill execution loop. V Claude Code neexistuje přímý ekvivalent | **Kritická** |
| `runTasks/` command triggers | Codex trigger pattern. Claude Code používá `/skill` nebo přímý prompt | **Vysoká** |
| Automatický agent handoff chain | Codex skills se volají navzájem. Claude Code nemá nativní multi-agent chain | **Vysoká** |
| Worker agent dispatch v orchestrátoru | `run-tasks-agent` invokuje workery přes Codex skill API | **Kritická** |

---

## 3. Doporučené úpravy (minimální změny pro dual-LLM support)

### 3.1 Přidej `CLAUDE.md` companion soubory (ne nahrazovat SKILL.md)

Každý agent dostane vedle `SKILL.md` také `CLAUDE.md`, který Claude Code nativně čte.

**Struktura:**
```
agents/
├── module-builder-agent/
│   ├── SKILL.md          # beze změny (Codex)
│   └── CLAUDE.md         # nový (Claude Code bridge)
├── review-agent/
│   ├── SKILL.md
│   └── CLAUDE.md
└── ...
```

**Obsah `CLAUDE.md`** — minimální bridge soubor:

```markdown
# Module Builder Agent

Tento soubor je Claude Code bridge pro module-builder-agent.
Pro plné instrukce čti SKILL.md v tomto adresáři.

## Jak použít v Claude Code

Když uživatel řekne "spusť module-builder-agent" nebo práce vyžaduje
modul-level implementaci, načti a dodržuj instrukce z ./SKILL.md.

## Mapování na Claude Code nástroje

- Shell příkazy z Command Matrix → použij Bash tool
- Čtení docs → použij Read tool
- Zápis artifacts → použij Write tool
- Handoff na test-runner-agent → uživatel spustí další prompt s kontextem
```

### 3.2 Přidej orchestrační instrukci do hlavního CLAUDE.md projektu

Do `/usr/local/var/www/Ercee-cms-llm-codex/.claude/claude.md` přidej sekci, která Claude Code naučí pracovat s agents workflow:

```markdown
## Agent Workflow

Tento projekt používá gate-based workflow s agenty definovanými v
/usr/local/var/www/agents/. Každý agent má SKILL.md s instrukcemi.

### Dostupní agenti

- module-builder-agent: implementace modulů (Gate 2)
- block-builder-agent: implementace CMS bloků (Gate 2)
- field-type-agent: implementace form field typů (Gate 2)
- test-runner-agent: testovací evidence (Gate 3)
- review-agent: nezávislé review (Gate 4)
- docs-editor-agent: dokumentace a changelog (Gate 5)
- run-tasks-agent: orchestrátor celého workflow (Gates 1-6)
- linear-task-agent: tvorba draft Linear tasků

### Jak pracovat s agenty

1. Přečti SKILL.md příslušného agenta
2. Dodržuj entry criteria, command matrix, failure policy
3. Zapisuj evidence do artifacts/gates/<initiative-id>/
4. Po dokončení gate informuj o výsledku a navrhni další krok (handoff)

### Gate sekvence

Gate 1 (Spec/Plan) → Gate 2 (Implementace) → Gate 3 (Test) →
Gate 4 (Review) → Gate 5 (Docs) → Gate 6 (Release Readiness)

### Tasky z Linearu

Čti .linear/data/tasks.json pro kontext iniciativ a subtasků.
```

### 3.3 Uprav `run-tasks-agent/SKILL.md` — přidej LLM-agnostický dispatch blok

Na konec stávajícího SKILL.md přidej sekci (neměň existující obsah):

```markdown
## LLM-Agnostic Agent Dispatch

Pro invokaci worker agentů platí:

### Codex (OpenAI)
- Použij skill reference: `$module-builder-agent`, `$test-runner-agent` atd.
- Ralph execution loop pro implementaci a fix cycle.

### Claude Code
- Přečti SKILL.md příslušného agenta z /usr/local/var/www/agents/<agent>/SKILL.md
- Následuj instrukce jako inline prompt context
- Pro implementaci používej standardní Claude Code nástroje (Bash, Read, Write, Edit)
- Handoff = ukonči aktuální gate a informuj uživatele o dalším kroku
- Fix loop = opakuj instrukce z SKILL.md s kontextem chyby (max 2 pokusy)

### Obecně (oba)
- Gate governance, evidence contract a failure policy jsou identické
- Artifacts se zapisují na stejná místa
- Linear state transitions používají stejné skripty
```

### 3.4 Přidej `agents/claude-dispatch.md` — centrální dispatch dokument

Nový soubor, který Claude Code může číst pro pochopení celého workflow:

```markdown
# Claude Code Agent Dispatch Guide

## Princip

Claude Code nemá nativní multi-agent orchestraci jako Codex skills.
Místo toho pracuje s agenty jako s "prompt kontextem":

1. Přečti SKILL.md agenta
2. Internalizuj pravidla jako svůj prompt
3. Vykonej práci pomocí svých nástrojů
4. Zapiš výstupy ve stanoveném formátu
5. Informuj uživatele o handoff bodu

## Command Mapping

| Codex trigger | Claude Code ekvivalent |
|--------------|----------------------|
| `runTasks/ <id>` | "Naplánuj gate workflow pro task <id>" |
| `runTasks/ execute <id>` | "Spusť gate execution pro task <id>" |
| `$module-builder-agent` | Přečti agents/module-builder-agent/SKILL.md a následuj |
| `$test-runner-agent` | Přečti agents/test-runner-agent/SKILL.md a následuj |
| `$review-agent` | Přečti agents/review-agent/SKILL.md a následuj |
| `$docs-editor-agent` | Přečti agents/docs-editor-agent/SKILL.md a následuj |
| `linear/` | Přečti agents/linear-task-agent/SKILL.md a následuj |

## Omezení oproti Codex

- Žádný automatický agent chain (handoff je manuální/user-driven)
- Žádný "Ralph" execution engine (Claude Code je sám executor)
- Retry loop musí být explicitně promptnutý uživatelem nebo
  řízený z run-tasks-agent SKILL.md instrukcí
```

### 3.5 Přidej labels do `.linear/config/schema.json`

Label `llm-claude` už existuje ve schema — to je v pořádku. Ujisti se, že se používá v nových tascích kde Claude pracuje.

---

## 4. Co NEMĚNIT

| Soubor | Důvod |
|--------|-------|
| Obsah existujících SKILL.md | Jsou funkční pro Codex, Claude je čte jako plaintext instrukce |
| `openai.yaml` | Codex ho potřebuje, Claude ho ignoruje — není konflikt |
| Linear toolkit skripty | LLM-agnostické, fungují pro oba |
| Gate definition, governance docs | Čistě textová pravidla, fungují pro oba |
| Evidence contract / artifact paths | Souborový systém, nezávislý na LLM |
| Schema soubory | JSON schema, LLM-agnostický |

---

## 5. Implementační plán (seřazeno dle priority)

### Fáze A — Okamžité (< 1 hodina)

1. **Rozšiř `.claude/claude.md`** v hlavním projektu o Agent Workflow sekci (3.2)
2. **Vytvoř `agents/claude-dispatch.md`** — centrální dispatch guide (3.4)

### Fáze B — Krátké (1-2 hodiny)

3. **Přidej LLM-agnostický dispatch blok** do `run-tasks-agent/SKILL.md` (3.3)
4. **Vytvoř `CLAUDE.md` bridge** pro každého agenta (3.1) — 8 souborů, templateované

### Fáze C — Volitelné vylepšení

5. **Přidej Claude Code custom slash commands** (`.claude/commands/`) pro nejčastější agent triggery:
   - `.claude/commands/run-gate.md` — spuštění konkrétního gate
   - `.claude/commands/linear-task.md` — tvorba Linear tasků
6. **Přidej test** — ověř, že oba LLM produkují kompatibilní artifacts

---

## 6. Shrnutí

**Dobrá zpráva:** ~70 % workflow je už LLM-agnostické (gate pravidla, evidence, skripty, schema).

**Hlavní gap:** Codex má nativní skill dispatch a Ralph execution loop. Claude Code tohle řeší jinak — čte markdown instrukce jako prompt context a používá vlastní nástroje.

**Řešení s minimálními změnami:**
- Nepřepisovat SKILL.md — přidat companion `CLAUDE.md` soubory
- Přidat dispatch guide a rozšířit hlavní `claude.md`
- Handoff mezi agenty = user-driven (Claude Code nemá auto-chain)
- Volitelně: Claude Code slash commands pro ergonomii

**Výsledek:** Obě LLM pracují se stejným workflow, stejnými artifacts a gate pravidly. Liší se jen v mechanismu invokace agentů — Codex přes skills, Claude Code přes prompt context + nástroje.
