# Doporuceni pro Codex agent skills a pomocne nastroje

Tento dokument shrnuje navrhy, jak zjednodusit praci s dokumentaci, testy a opakujicimi se ukoly v tomto repu pomoci Codex skills a pomocnych skriptu.

## 1) Navrzene skills (AGENTS.md + SKILL.md)

### A) skill-docs-writer
**Ucel:** Standardizovane psani dokumentace (README, docs/*, changelogy) s konzistentni strukturou.
**Rozsah:**
- Tvorba a aktualizace `.md` souboru.
- Dodrzovani lokalniho stylu (tabulky, sekce, naming).
- Overeni existujicich sekci pred doplnenim.

**Navrh obsahu SKILL.md:**
- Pravidla pro formatovani (nadpisy, tabulky, bloky prikladu).
- Jak zapojovat nove endpointy do `README.md` a `docs/endpoints/*`.
- Struktura pro implementacni tasky (Scope, Kontext, Cile, Faze, QA, Otevrene body).

### B) skill-api-docs
**Ucel:** Dokumentace REST API a zmen v endpointu.
**Rozsah:**
- Vytvareni `docs/endpoints/*.md` souboru.
- Vkladani vzorovych request/response.
- Udrzovani konzistence s `routes/api.php`.

**Navrh obsahu SKILL.md:**
- Overit route v `routes/api.php`.
- Validovat strukturu odpovedi proti controlleru.
- Povinne sekce: Authorization, Request, Response, Error responses.

### C) skill-theme-sync
**Ucel:** Koordinace theme settings mezi CMS a frontendem.
**Rozsah:**
- CMS: model, observer, endpoint.
- Frontend: `getTheme()`, mapovani do komponent, fallbacky.

**Navrh obsahu SKILL.md:**
- Kontrolni seznam souboru (CMS + frontend).
- Mapovani dat (logo, CTA, menus).
- Doplnit QA kroky (manual check, build).

### D) skill-testing-guidelines
**Ucel:** Dodrzovani internich pravidel pro unit/feature testy.
**Rozsah:**
- Odkaz na `laravel-unit-testing-guidelines.md`.
- Rozliseni unit vs feature.

**Navrh obsahu SKILL.md:**
- Rozhodovaci strom pro typ testu.
- Minimalni vzory pro testy v `tests/Feature` a `tests/Unit`.

## 2) Doporucene scripts/ (pomocne nastroje)

### A) scripts/docs/check-endpoints.sh
**Ucel:** Zjistit, zda jsou endpointy pokryty v `README.md` a `docs/endpoints`.
**Chovani:**
- Vypise route z `routes/api.php`.
- Porovna s existujicimi `.md` soubory ve `docs/endpoints`.
- Varuje, pokud chybi dokumentace pro route.

### B) scripts/docs/new-endpoint-doc.sh
**Ucel:** Vytvorit sablonu dokumentace pro novy endpoint.
**Chovani:**
- Prijme slug a HTTP method.
- Vytvori `docs/endpoints/<name>.md` se zakladni strukturou.

### C) scripts/theme/verify-theme-endpoint.php
**Ucel:** Overit strukturu odpovedi `/api/v1/theme` proti ocekavanemu JSON schematu.
**Chovani:**
- Zavola endpoint (lokalne) a zkontroluje klice.

## 3) Doporucene sablony a assets

### A) docs/_templates/endpoint.md
**Ucel:** Sablona pro endpoint dokumentaci.
**Sekce:**
- Title
- Authorization
- Request parameters
- Successful response
- Error responses

### B) docs/_templates/implementation-tasks.md
**Ucel:** Sablona pro implementacni tasky.
**Sekce:**
- Kontext
- Cile
- Faze (1-4)
- QA
- Otevrene body

## 4) Nastaveni AGENTS.md (doporuzeni)

**Navrh minimalniho nastaveni:**
- Vypis aktivnich skills a jejich popisu.
- Pravidlo: pokud se pracuje s docs, vzdy pouzit `skill-docs-writer`.
- Pravidlo: pokud se pridava endpoint, pouzit `skill-api-docs`.
- Pravidlo: pokud se tyka theme, pouzit `skill-theme-sync`.

## 5) Navrh dalsich nastroju

- EditorConfig (`.editorconfig`) pro konzistentni formatovani MD.
- Markdown lint config (napr. `markdownlint`) + `npm run lint:docs`.
- Pre-commit hook pro kontrolu dokumentace endpointu.

## 6) Jak to zavest (doporuzeny postup)

1) Zalozit `skills/` s vyse uvedenymi skill definicemi.
2) Vytvorit sablony v `docs/_templates/`.
3) Pridat skripty do `scripts/` a popsat v `README.md`.
4) Zapnout minimalni lint kontroly pro dokumentaci.

## 7) Otevrene body

- Kde udrzovat sablony (repo vs sdileny Codex home)?
- Kdo bude vlastnikem aktualizace skills (dev lead vs dokumentarista)?
- Jak casto kontrolovat shodu `routes/api.php` vs docs?
