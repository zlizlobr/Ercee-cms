# Interni Ticket System (Custom ve CMS) - Implementacni Tasky

Tento dokument je detailni implementacni checklist pro 3. variantu: vlastni interni ticket system primo v Ercee CMS (Filament 3).
https://freescout.net
## Konvence
- Kazdy task obsahuje:
  - jasny popis
  - checkbox (`- [ ]`)
  - cilovy vysledek (akceptacni kriteria)
  - zavislosti
- ID tasku (`T01`, `T02`, ...) pouzivejte v PR, commit zprave a test reportu.

## Faze A - Analytika a navrh domeny

- [ ] **T01 - Scope a role model ticketingu**
  - Popis: Definovat podporovane use-casy (incident, dotaz, request), role (admin, support-agent, observer), pristupova prava a SLA pravidla.
  - Cilovy vysledek (akceptacni kriteria):
    - Existuje schvaleny dokument se seznamem use-casu, roli a prav.
    - Kazda role ma jasne urcene akce (`create`, `assign`, `reply`, `close`, `reopen`, `export`).
    - SLA metriky jsou definovane (first response time, resolution time) vcetne hranic.
  - Zavislosti: zadne.

- [ ] **T02 - Datovy model a stavovy automat**
  - Popis: Navrhnout entity `tickets`, `ticket_messages`, `ticket_assignments`, `ticket_watchers`, `ticket_tags`, `ticket_attachments`, `ticket_sla_events` a prechody stavu.
  - Cilovy vysledek (akceptacni kriteria):
    - Stavovy diagram pokryva alespon stavy `new`, `open`, `pending_customer`, `resolved`, `closed`.
    - Pro kazdy prechod je definovan trigger a validace.
    - Datovy model mapuje povinna a volitelna pole bez ambiguity.
  - Zavislosti: T01.

- [ ] **T03 - Prioritizace a triage pravidla**
  - Popis: Navrhnout pravidla pro prioritu (P1-P4), kategorii, dopad a automaticke routovani ticketu.
  - Cilovy vysledek (akceptacni kriteria):
    - Existuje tabulka priority matrix (dopad x urgence -> priorita).
    - Jsou definovana pravidla auto-assign (podle tagu/modulu/tymu).
    - Je popsano fallback chovani pri nenalezeni agenta.
  - Zavislosti: T01, T02.

## Faze B - Databaze a backend jadro

- [ ] **T04 - Vytvorit migrace pro ticketing**
  - Popis: Implementovat migrace vsech ticketing tabulek vcetne indexu a cizich klicu.
  - Cilovy vysledek (akceptacni kriteria):
    - `php artisan migrate` probehne bez chyby.
    - Existuji indexy na casto filtrovana pole (`status`, `priority`, `assignee_id`, `created_at`).
    - Foreign key omezeni odpovidaji navrhu datoveho modelu.
  - Zavislosti: T02.

- [ ] **T05 - Eloquent modely a relace**
  - Popis: Implementovat modely a relace, vcetne scopes pro bezne dotazy (`open`, `overdue`, `assignedTo`).
  - Cilovy vysledek (akceptacni kriteria):
    - Kazda hlavni entita ma model se spravnymi relacemi.
    - Unit test pokryva alespon 1 `hasMany`, 1 `belongsToMany`, 1 scope.
    - N+1 problem je osetren eager loadingem u listingu.
  - Zavislosti: T04.

- [ ] **T06 - Stavovy automat a domenni sluzby**
  - Popis: Implementovat centralni service/tridy pro prechody stavu ticketu, validaci prechodu a audit.
  - Cilovy vysledek (akceptacni kriteria):
    - Nevalidni prechod stavu konci deterministickou domenovou chybou.
    - Validni prechod vytvori audit zaznam.
    - Integration test overi nejmene 5 klicovych prechodu.
  - Zavislosti: T05.

- [ ] **T07 - SLA engine a casove udalosti**
  - Popis: Implementovat vypocet SLA deadline, detekci poruseni a logovani SLA udalosti.
  - Cilovy vysledek (akceptacni kriteria):
    - Pri zalozeni ticketu se ulozi first-response a resolution deadline.
    - Pri prekroceni deadline vznikne `sla_breach` udalost.
    - Test pokryva ruzne casove pasmo a vikendove pravidlo (pokud je definovano).
  - Zavislosti: T03, T06.

- [ ] **T08 - API/Service kontrakt pro ticket operace**
  - Popis: Definovat interni application service vrstvu (`createTicket`, `reply`, `assign`, `close`, `reopen`, `addWatcher`).
  - Cilovy vysledek (akceptacni kriteria):
    - Vsechny operace jsou dostupne pres jednotne service rozhrani.
    - Parametry a navratove typy jsou zdokumentovane v kodu (PHPDoc).
    - Chyby maji jednotny typ a mapovani na UI notifikace.
  - Zavislosti: T05, T06.

## Faze C - Filament admin UI

- [ ] **T09 - Filament Resource: Ticket**
  - Popis: Vytvorit `TicketResource` s tabulkou, filtry, fulltextem, bulk akcemi a sloupci SLA.
  - Cilovy vysledek (akceptacni kriteria):
    - Listing umoznuje filtrovani podle stavu, priority, assignee, tagu.
    - Fulltext vraci vysledky podle subject + message preview.
    - Bulk akce podporuji assign a zmenu priority.
  - Zavislosti: T08.

- [ ] **T10 - Filament page: Detail ticketu s timeline**
  - Popis: Implementovat detail ticketu s konverzacnim vlaknem, internimi poznamkami, prilohami a historii stavu.
  - Cilovy vysledek (akceptacni kriteria):
    - Detail zobrazi zpravy v chronologii a odlisuje interni/public odpoved.
    - Pridani odpovedi/refreshe stavu nevyzaduje reload celeho modulu.
    - Historie prechodu stavu je viditelna a auditovatelna.
  - Zavislosti: T09.

- [ ] **T11 - Filament forms a validace**
  - Popis: Pripravit formulare pro vytvoreni ticketu, odpoved, prirazeni, zmenu priority a tagovani.
  - Cilovy vysledek (akceptacni kriteria):
    - Formulare validuji povinna pole a max velikosti.
    - Chybove hlasky jsou lokalizovane (cs/en dle projektu).
    - Neplatny upload prilohy je odmitnut s jasnou hlaskou.
  - Zavislosti: T09, T10.

- [ ] **T12 - Dashboard widgety supportu**
  - Popis: Pridat widgety `Open Tickets`, `Overdue SLA`, `My Queue`, `Unassigned`.
  - Cilovy vysledek (akceptacni kriteria):
    - Widgety zobrazuji realna data podle role uzivatele.
    - Klik z widgetu otevre odpovidajici prefiltrovany listing.
    - Dotazy widgetu jsou optimalizovane (bez plnych scanu na velkych datech).
  - Zavislosti: T07, T09.

## Faze D - Notifikace, automatizace a integrace

- [ ] **T13 - Notifikace (in-app + email)**
  - Popis: Implementovat notifikace pro `assigned`, `new_reply`, `sla_warning`, `sla_breach`, `status_changed`.
  - Cilovy vysledek (akceptacni kriteria):
    - Agent dostane notifikaci pri prirazeni ticketu.
    - Watchers dostanou notifikaci pri nove odpovedi.
    - Typ notifikace lze zapnout/vypnout v nastaveni uzivatele.
  - Zavislosti: T08, T10.

- [ ] **T14 - Queue jobs a retry strategie**
  - Popis: Presunout tezsi operace (email notifikace, velke exporty, SLA evaluace) do fronty.
  - Cilovy vysledek (akceptacni kriteria):
    - Jobs jsou idempotentni.
    - Definovana retry politika a dead-letter handling.
    - Chyby jobu jsou viditelne v logu/monitoringu.
  - Zavislosti: T07, T13.

- [ ] **T15 - Cron/command pro periodickou SLA evaluaci**
  - Popis: Vytvorit artisan command pro periodicky prepocet SLA stavu otevrenych ticketu.
  - Cilovy vysledek (akceptacni kriteria):
    - Command lze spustit manualne i schedulovane.
    - Zpracovani je inkrementalni (neprochazi nepotrebne uzavrene tickety).
    - Existuje test scheduler integrace.
  - Zavislosti: T07, T14.

- [ ] **T16 - Integrace s internimi entitami CMS**
  - Popis: Umoznit navazat ticket na existujici entity (uzivatel, objednavka, formular, stranka).
  - Cilovy vysledek (akceptacni kriteria):
    - Ticket muze obsahovat polymorfni vazbu na cilovou entitu.
    - V detailu entity je viditelny seznam souvisejicich ticketu.
    - Otevirani ticketu z entity predvyplni kontext.
  - Zavislosti: T05, T09.

- [ ] **T17 - Audit log a forenzni stopa**
  - Popis: Logovat vsechny kriticke akce (zmena stavu, prirazeni, zmena priority, smazani prilohy).
  - Cilovy vysledek (akceptacni kriteria):
    - Kazda kriticka akce obsahuje `who`, `what`, `when`, `before`, `after`.
    - Audit zaznam je read-only pro bezne role.
    - Export auditu je mozny pro compliance review.
  - Zavislosti: T06, T10.

## Faze E - Opravneni, bezpecnost, compliance

- [ ] **T18 - Opravneni a policy vrstva**
  - Popis: Implementovat policy pravidla pro vsechny ticket operace a oddelit prava support/admin.
  - Cilovy vysledek (akceptacni kriteria):
    - Neautorizovana akce vraci `403`.
    - Support vidi jen vlastni scope dle role/team assignment.
    - Testy pokryvaji pozitivni i negativni autorizacni scenare.
  - Zavislosti: T01, T09.

- [ ] **T19 - Bezpecnost priloh**
  - Popis: Osetrit upload priloh (MIME whitelist, velikost, antivirus hook pokud je dostupny, bezpecne uloziste).
  - Cilovy vysledek (akceptacni kriteria):
    - Nepovolene typy souboru nelze nahrat.
    - Pristup k priloham respektuje policy pravidla.
    - Verejne URL nejsou dostupne bez autorizace.
  - Zavislosti: T11, T18.

- [ ] **T20 - GDPR/retence dat**
  - Popis: Definovat retencni politiku, anonymizaci osobnich udaju a mazani po expiraci.
  - Cilovy vysledek (akceptacni kriteria):
    - Existuje konfigurovatelna retencni doba ticketu.
    - Probiha anonymizace citlivych poli po uplynuti lhuty.
    - Dokumentovana procedura exportu a vymazu dat subjektu.
  - Zavislosti: T17, T18.

## Faze F - Testy a kvalita

- [ ] **T21 - Unit testy domeny**
  - Popis: Pokryt domenni logiku (stavy, SLA, priorita, validace prechodu).
  - Cilovy vysledek (akceptacni kriteria):
    - Kriticka domena ma unit pokryti na happy-path i edge-case.
    - Testy overuji i chybove vetve a domenove vyjimky.
    - Test suite bezi deterministicky bez flaky chovani.
  - Zavislosti: T06, T07.

- [ ] **T22 - Feature testy backend workflow**
  - Popis: Pokryt end-to-end backend tok: zalozeni ticketu -> prirazeni -> odpoved -> uzavreni -> reopen.
  - Cilovy vysledek (akceptacni kriteria):
    - Alespon 3 feature scenare pokryvaji standardni i krizove toky.
    - Test validuje spravne notifikace a audit.
    - Test validuje autorizaci podle role.
  - Zavislosti: T13, T18, T21.

- [ ] **T23 - E2E testy Filament UI**
  - Popis: Pridat Playwright/E2E scenare pro klicove akce support agenta v administraci.
  - Cilovy vysledek (akceptacni kriteria):
    - E2E pokryva listing, filtr, detail, reply, assign, close.
    - Scenare jsou stabilni a opakovatelne ve CI.
    - Fail report obsahuje screenshot/trace.
  - Zavislosti: T10, T11, T22.

- [ ] **T24 - Performance baseline**
  - Popis: Zmerit vykon listingu a detailu na realistickem datasetu (napr. 50k ticketu).
  - Cilovy vysledek (akceptacni kriteria):
    - Definovany baseline (p95) pro listing a detail.
    - Identifikovane nejdrazsi dotazy + navrh index tuning.
    - P95 listing splnuje interni threshold (doplnte konkretni cislo).
  - Zavislosti: T09, T12, T21.

## Faze G - Dokumentace, rollout, provoz

- [ ] **T25 - Uzivatelska dokumentace pro support tym**
  - Popis: Sepsat provozni navod pro agenty (triage, SLA, eskalace, interni poznamky, best practices).
  - Cilovy vysledek (akceptacni kriteria):
    - Dokumentace pokryva kazdodenni workflow agenta.
    - Obsahuje troubleshooting sekci a eskalacni cestu.
    - Je odkazan z interniho README/knowledge base.
  - Zavislosti: T10, T13, T15.

- [ ] **T26 - Admin dokumentace a konfigurace**
  - Popis: Zdokumentovat nastaveni modulu (role, SLA pravidla, notifikace, scheduler, retence).
  - Cilovy vysledek (akceptacni kriteria):
    - Existuje checklist pro prvotni nasazeni a zmeny konfigurace.
    - Dokumentace obsahuje rollback postup.
    - Konfigurace je verzovatelna (config soubory + env klice).
  - Zavislosti: T15, T20.

- [ ] **T27 - Seed data a demo scenare**
  - Popis: Pripravit seedery pro realisticke testovaci tickety, role, tagy a SLA scenare.
  - Cilovy vysledek (akceptacni kriteria):
    - Jednim prikazem lze nahrat demo dataset.
    - Dataset obsahuje vsechny hlavni stavy a priority.
    - QA pouziva stejne scenare napric lokalnim i CI prostredim.
  - Zavislosti: T04, T09, T21.

- [ ] **T28 - Rollout plan (pilot -> produkce)**
  - Popis: Rozdelit rollout na pilotni fazi, omezenou produkci a plne nasazeni vcetne KPI sledovani.
  - Cilovy vysledek (akceptacni kriteria):
    - Existuje plan po tydnech s odpovednostmi.
    - KPI obsahuje alespon: first response time, resolution time, reopen rate.
    - Je definovan kill-switch/rollback scenar.
  - Zavislosti: T24, T25, T26.

- [ ] **T29 - Provozni monitoring a alerting**
  - Popis: Nastavit metriky a alerty pro chyby notifikaci, fronty, SLA breaches a pad scheduleru.
  - Cilovy vysledek (akceptacni kriteria):
    - Dashboard zobrazuje health ticket modulu.
    - Kriticke alerty maji routovani na odpovedny kanal.
    - Alerty jsou otestovane simulaci incidentu.
  - Zavislosti: T14, T15, T28.

- [ ] **T30 - Post-launch review a iterace**
  - Popis: Po 2-4 tydnech vyhodnotit KPI, bottlenecky a navrhnout backlog v2.
  - Cilovy vysledek (akceptacni kriteria):
    - Vznikne retrospektiva s daty pred/po nasazeni.
    - Je sepsan priorizovany seznam vylepseni pro dalsi iteraci.
    - Jsou potvrzeny nebo upraveny SLA cile dle realneho provozu.
  - Zavislosti: T28, T29.

## Doporucene MVP rez

Pokud chcete rychly start, prvni release omezte na: `T01-T12`, `T18`, `T21-T23`, `T25`, `T28`.
