# Git Worktree + VS Code + LLM Dev Flow

Tento dokument popisuje doporučený workflow pro simulaci více vývojářů
(LLM) nad jedním Git repozitářem pomocí `git worktree`, VS Code a
**jedné sdílené databáze**.

------------------------------------------------------------------------

## Cíl

-   Paralelní práce více LLM nad jedním projektem
-   Každý LLM má vlastní větev a pracovní kopii
-   Izolované běžící aplikace (porty, sessions, cache)
-   **Jedna databáze**, spravovaná pouze z main větve

------------------------------------------------------------------------

## 1. Struktura složek

Předpoklad: hlavní repo je v `/usr/local/var/www/Ercee-cms`

Po vytvoření worktrees:

    /usr/local/var/www/
    ├── Ercee-cms/           # main branch (merge + DB migrace)
    │   └── .git/
    │
    ├── Ercee-cms-llm-codex/     # feature/llm-codex
    │   ├── app/
    │   ├── routes/
    │   ├── .env
    │   └── ...
    │
    ├── Ercee-cms-llm-claude/     # feature/llm-claude
    │   ├── app/
    │   ├── routes/
    │   ├── .env
    │   └── ...

------------------------------------------------------------------------

## 2. Vytvoření worktrees

Spusť z hlavního repa:

``` bash
git worktree add -b feature/llm-codex ../Ercee-cms-llm-codex
git worktree add -b feature/llm-claude ../Ercee-cms-llm-claude
```

------------------------------------------------------------------------

## 3. VS Code

Otevři každý worktree v samostatném okně:

``` bash
code ../Ercee-cms-llm-codex
code ../Ercee-cms-llm-claude
```

Doporučení: - Každé okno = jiný LLM / jiný system prompt - Ideálně i
jiný model

------------------------------------------------------------------------

## 4. Instalace závislostí v každém worktree

Po vytvoření worktree je potřeba nainstalovat PHP (a případně JS)
závislosti:

``` bash
composer install
# volitelně, pokud běžíš frontend build:
npm install
```

Poznámka: Projekt očekává PHP 8.3+ (viz `docs/local-backend-setup.md`).
Pokud máš novější verzi (např. 8.5), `composer install` může spadnout
na nekompatibilním lockfile. V tom případě přepni PHP na 8.3 a zkus to
znovu.

------------------------------------------------------------------------

## 5. .env soubory (oddělené, ale jedna DB)

Každý worktree má **vlastní `.env`**, ale databáze je stejná.

Ercee CMS používá výchozí SQLite DB. Sdílený soubor DB je uložený v main
worktree a ostatní worktrees na něj odkazují přes `DB_DATABASE`.

Základ: v každé větvi zkopíruj `.env.example` do `.env` a pak změň jen
specifické hodnoty níže.

### Ercee-cms-llm-codex/.env (odlišné hodnoty)

``` env
APP_NAME="Ercee CMS LLM Codex"
APP_URL=http://localhost:8001

DB_CONNECTION=sqlite
DB_DATABASE=/usr/local/var/www/Ercee-cms/database/database.sqlite

SESSION_COOKIE=llm_codex_session
CACHE_PREFIX=llm_codex_cache
```

### Ercee-cms-llm-claude/.env (odlišné hodnoty)

``` env
APP_NAME="Ercee CMS LLM Claude"
APP_URL=http://localhost:8002

DB_CONNECTION=sqlite
DB_DATABASE=/usr/local/var/www/Ercee-cms/database/database.sqlite

SESSION_COOKIE=llm_claude_session
CACHE_PREFIX=llm_claude_cache
```

⚠️ **Migrace a změny DB schématu se provádí pouze z main worktree
(`Ercee-cms/`)**

------------------------------------------------------------------------

## 6. Spuštění artisan serverů

Každý worktree běží na vlastním portu:

``` bash
# LLM Codex
php artisan serve --port=8001

# LLM Claude
php artisan serve --port=8002
```

URL: - http://localhost:8001 - http://localhost:8002

------------------------------------------------------------------------

## 7. Pravidla pro práci s DB (DŮLEŽITÉ)

-   ❌ LLM worktrees **nesmí spouštět migrace**
-   ❌ LLM worktrees **nesmí měnit seedery**
-   ✅ DB změny pouze:
    -   ručně
    -   nebo z main větve

Doporučení: - změny DB řešit až po mergi - LLM promptem výslovně zakázat
práci s migracemi

------------------------------------------------------------------------

## 8. Git workflow

V každém worktree:

``` bash
git status
git commit -am "LLM Codex: refactor XYZ"
```

Merge prováděj z main repa:

``` bash
cd /usr/local/var/www/Ercee-cms
git checkout main
git merge feature/llm-codex
git merge feature/llm-claude
```

------------------------------------------------------------------------

## 9. Doporučené role LLM

  Větev   Role
  ------- -------------------------------
  llm-codex   refactor, typy, architektura
  llm-claude  feature, UX, edge cases
  main    merge, DB, finální rozhodnutí

------------------------------------------------------------------------

## 10. Checklist při startu

-   [ ] vytvořen worktree
-   [ ] unikátní `.env`
-   [ ] unikátní port
-   [ ] unikátní session + cache prefix
-   [ ] migrace jen z main

------------------------------------------------------------------------

## Výsledek

Tento setup simuluje práci více vývojářů/LLM nad jedním projektem: -
paralelní vývoj - minimum konfliktů - plná kontrola při mergi

Ty jsi tech lead 😎
