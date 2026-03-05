# Ercee Dev Layer (Varianta 2) - Implementacni tasky

Canonical guide:
- `docs/guides/dev/ercee-dev-layer-guide.md`
- `docs/guides/dev/ercee-dev-layer-policy.md`

## Ercee Ecosystem

- CMS: `/usr/local/var/www/Ercee-cms`
- Frontend: `/usr/local/var/www/ercee-frontend`
- Modules: `/usr/local/var/www/ercee-modules`
- Each module has its own custom Git repository.
- Always identify the correct repo first.
- Do not mix CMS, frontend, and modules.
- Only work across repos when the task clearly requires it, and keep responsibilities explicit.

## Cile

- Zavest sdilenou dev vrstvu (policy contract) pro CMS, frontend (Astro) a npm tooling.
- Mit jednotne rizeni debug logu tak, aby `debug` logy byly povolene v dev a potlacene v produkci.
- Mit jednotne prepinani chovani pro `public` debug vystupy a dev-only zavislosti.

## Tasky

### 1) Definovat jednotny env contract pro cely ecosystem

- [x] Sepsat canonical seznam promennych: `ERCEE_DEV_LAYER`, `ERCEE_LOG_LEVEL`, `ERCEE_PUBLIC_DEBUG`, `ERCEE_RUNTIME_PROFILE`.
- Jasny popis: Vytvorit jednotny contract s presnou semantikou hodnot (typ, default, povolene hodnoty) a pravidly precedence.
- Cilovy vysledek (akceptacni kriteria): Existuje schvaleny dokument s tabulkou promennych, defaulty a 3 priklady konfigurace (`local/dev`, `staging`, `prod`).
- Zavislosti: Zadna.

### 2) Navrhnout mapovani behavior podle profilu

- [x] Definovat matici chovani pro `dev|staging|prod`.
- Jasny popis: Urcit, jak profil ovlivnuje log level, zapinani debug vypisu do `public`, Astro debug pluginy a npm scripty/dependency policy.
- Cilovy vysledek (akceptacni kriteria): Existuje behavior matrix, kde je pro kazdy profil uvedeno `ON/OFF` pro vsechny oblasti.
- Zavislosti: Task 1.

### 3) Vytvorit sdilenou policy vrstvu (spec + API)

- [x] Navrhnout a zdokumentovat minimalni API sdilene vrstvy (`isDevLayerEnabled()`, `canWriteDebugLogs()`, `isPublicDebugEnabled()`).
- Jasny popis: Vytvorit jazykove-neutralni specifikaci a kontrakt, aby PHP a Node implementace vracely konzistentni rozhodnuti.
- Cilovy vysledek (akceptacni kriteria): Specifikace obsahuje vstupy, vystupy a edge-case pravidla (chybejici env, nevalidni hodnota).
- Zavislosti: Task 1, Task 2.

### 4) Implementovat PHP adapter pro CMS (Laravel)

- [x] Pridat CMS adapter, ktery cte env contract a poskytuje rozhodnuti pro aplikaci.
- Jasny popis: V CMS vytvorit centralni config/service vrstvu (napr. `config/ercee_dev.php` + helper/sluzba), bez roztristene logiky po kodu.
- Cilovy vysledek (akceptacni kriteria): CMS kod ma jedno centralni misto, odkud se cte stav dev vrstvy; neexistuji ad-hoc primych `env()` volani v business logice.
- Zavislosti: Task 3.

### 5) Napojit Laravel logging pipeline na policy

- [x] Nastavit log channels/level tak, aby `debug` logy nesly do vystupu pouze pokud policy povoli debug.
- Jasny popis: Upravit `config/logging.php` a navaznou konfiguraci tak, aby produkce explicitne odfiltrovala `debug` a dev je umel zapisovat.
- Cilovy vysledek (akceptacni kriteria):
    - V dev profilu se `Log::debug()` zapise.
    - V prod profilu se stejny `Log::debug()` nezapise.
    - `info|warning|error` zustanou funkcni v obou profilech.
- Zavislosti: Task 4.

### 6) Pridat dev-only log helper pro konzistentni pouziti

- [x] Zavest helper/facadu pro zamerne dev-only debug logy.
- Jasny popis: Vytvorit API (napr. `dev_debug()`) pouzivane pro diagnosticke logy, aby byl zamer citelny a jednotny.
- Cilovy vysledek (akceptacni kriteria): Existuje helper + kratky coding standard, kdy pouzit `Log::debug()` a kdy `dev_debug()`.
- Zavislosti: Task 4, Task 5.

### 7) Osetrit `public` debug chovani podle policy

- [x] Zavest jednotny guard pro vsechny debug artefakty, ktere by mohly byt vystaveny v `public`.
- Jasny popis: Identifikovat vsechny CMS cesty, ktere zapisuji debug data do verejne dostupnych souboru, a omezit je policy rozhodnutim.
- Cilovy vysledek (akceptacni kriteria): Pri `prod` je public debug vystup vypnuty; pri `dev` je povoleny pouze explicitne.
- Zavislosti: Task 4, Task 2.

### 8) Pripravit Node/Astro adapter stejne policy logiky

- [x] Definovat implementacni task pro frontend repozitar s identickym rozhodovanim.
- Jasny popis: V Astro/Node vytvorit adapter se stejnymi vstupy a vystupy jako v CMS, aby se profil neresil odlisne.
- Cilovy vysledek (akceptacni kriteria): Existuje frontend task spec s mapovanim env -> behavior 1:1 proti CMS policy.
- Zavislosti: Task 3.

### 9) Zavest npm dependency/script policy

- [x] Definovat pravidla pro dev-only zavislosti a script gating podle profilu.
- Jasny popis: Urcit, ktere script kroky a pluginy se spousti jen v dev, a ktere jsou povinne v CI/prod buildu.
- Cilovy vysledek (akceptacni kriteria):
    - Dokumentovane pravidlo pro `dependencies` vs `devDependencies`.
    - Build pipeline ma jasne podminky pro dev/prod rezim.
- Zavislosti: Task 2, Task 8.

### 10) Dodat test coverage pro policy rozhodovani

- [x] Pridat unit/integration testy pro CMS policy adapter a logging/public guard.
- Jasny popis: Otestovat vsechny profily + edge casy (`missing env`, `invalid values`, konflikt hodnot).
- Cilovy vysledek (akceptacni kriteria): Testy validuji, ze v prod neni mozne omylem emitovat debug log/public debug vystup.
- Zavislosti: Task 5, Task 7.

### 11) CI kontrola proti regresim

- [x] Pridat CI check, ktery overi konzistenci env contractu a policy mapovani.
- Jasny popis: Automatizovane validovat, ze CMS a frontend adapter maji stejne mapovani a ze prod konfigurace nepovoluje debug kanal.
- Cilovy vysledek (akceptacni kriteria): CI failne pri nekonzistenci contractu nebo pri povolenem debug v prod profilech.
- Zavislosti: Task 8, Task 9, Task 10.

### 12) Rollout plan a migrace existujicich logu

- [x] Naplanovat migraci stavajicich `Log::debug` volani na novy standard.
- Jasny popis: Rozdelit migraci na faze (kriticke cesty, admin cesty, zbytek), definovat ownership a backout postup.
- Cilovy vysledek (akceptacni kriteria): Existuje rollout checklist, zodpovednosti a plan verifikace po nasazeni.
- Zavislosti: Task 6, Task 10, Task 11.
