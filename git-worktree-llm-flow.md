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

Předpoklad: hlavní repo je v `~/dev/my-project`

Po vytvoření worktrees:

    ~/dev/
    ├── my-project/           # main branch (merge + DB migrace)
    │   └── .git/
    │
    ├── my-project-llm-a/     # feature/llm-a
    │   ├── app/
    │   ├── routes/
    │   ├── .env
    │   └── ...
    │
    ├── my-project-llm-b/     # feature/llm-b
    │   ├── app/
    │   ├── routes/
    │   ├── .env
    │   └── ...

------------------------------------------------------------------------

## 2. Vytvoření worktrees

Spusť z hlavního repa:

``` bash
git worktree add ../my-project-llm-a feature/llm-a
git worktree add ../my-project-llm-b feature/llm-b
```

------------------------------------------------------------------------

## 3. VS Code

Otevři každý worktree v samostatném okně:

``` bash
code ../my-project-llm-a
code ../my-project-llm-b
```

Doporučení: - Každé okno = jiný LLM / jiný system prompt - Ideálně i
jiný model

------------------------------------------------------------------------

## 4. .env soubory (oddělené, ale jedna DB)

Každý worktree má **vlastní `.env`**, ale databáze je stejná.

### my-project-llm-a/.env

``` env
APP_NAME="MyProject LLM A"
APP_ENV=local
APP_URL=http://localhost:8001

DB_CONNECTION=mysql
DB_DATABASE=my_project
DB_USERNAME=root
DB_PASSWORD=secret

SESSION_COOKIE=llm_a_session
CACHE_PREFIX=llm_a_cache
```

### my-project-llm-b/.env

``` env
APP_NAME="MyProject LLM B"
APP_ENV=local
APP_URL=http://localhost:8002

DB_CONNECTION=mysql
DB_DATABASE=my_project
DB_USERNAME=root
DB_PASSWORD=secret

SESSION_COOKIE=llm_b_session
CACHE_PREFIX=llm_b_cache
```

⚠️ **Migrace a změny DB schématu se provádí pouze z main worktree
(`my-project/`)**

------------------------------------------------------------------------

## 5. Spuštění artisan serverů

Každý worktree běží na vlastním portu:

``` bash
# LLM A
php artisan serve --port=8001

# LLM B
php artisan serve --port=8002
```

URL: - http://localhost:8001 - http://localhost:8002

------------------------------------------------------------------------

## 6. Pravidla pro práci s DB (DŮLEŽITÉ)

-   ❌ LLM worktrees **nesmí spouštět migrace**
-   ❌ LLM worktrees **nesmí měnit seedery**
-   ✅ DB změny pouze:
    -   ručně
    -   nebo z main větve

Doporučení: - změny DB řešit až po mergi - LLM promptem výslovně zakázat
práci s migracemi

------------------------------------------------------------------------

## 7. Git workflow

V každém worktree:

``` bash
git status
git commit -am "LLM A: refactor XYZ"
```

Merge prováděj z main repa:

``` bash
cd ~/dev/my-project
git checkout main
git merge feature/llm-a
git merge feature/llm-b
```

------------------------------------------------------------------------

## 8. Doporučené role LLM

  Větev   Role
  ------- -------------------------------
  llm-a   refactor, typy, architektura
  llm-b   feature, UX, edge cases
  main    merge, DB, finální rozhodnutí

------------------------------------------------------------------------

## 9. Checklist při startu

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
