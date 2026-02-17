# Junior Walkthrough: User + Agent Workflow (Analytics Example)

Tento dokument je názorný příklad, jak má junior postupovat jako uživatel workflow a co následně dělají agenti.
Příklad vychází z task listu v ` /usr/local/var/www/Ercee-cms/analytics-module-implementation-tasks.md`.

## 1. Co udělá junior jako user (minimum)

1. Pošle orchestrátoru zadání: "Zpracuj analytics module initiative podle task listu."
2. Neřeší ručně rozpad parent/subtasks ani gate pořadí.
3. Zkontroluje výstup orchestrátoru (shrnutí scope + vytvořené tasky).
4. Schválí Spec/Plan, nebo vrátí připomínky.
5. Při failu testů/review jen rozhodne prioritu fixů, neřeší ručně orchestraci loopů.
6. Na konci potvrdí release summary (impact, risks, rollback/mitigation).

## 2. Co udělá hlavní orchestrátor (run-tasks-agent v execute režimu)

1. Načte task list a vytvoří strukturu parent + subtasks v gate pořadí.
2. Volitelně po vytvoření spustí sync/pull do Linear (`.linear/scripts/sync.sh`, `.linear/scripts/pull.sh`).
3. Spustí Spec/Plan fázi.
4. Vybere správného worker agenta podle typu práce.
5. Po implementaci spustí test loop.
6. Pokud testy failnou, vrací task implementačnímu agentovi s fix instrukcí.
7. Když testy projdou, spustí review loop přes `review-agent`.
8. Pokud review vrátí blocker, vrací zpět do implementace a opakuje testy.
9. Když review projde (max major/minor), spustí `docs-editor-agent`.
10. Nakonec připraví Release Readiness výstup.
11. Po úspěšném dokončení každé gate přepne příslušný Linear subtask do `Done` (např. přes `scripts/workflow/linear-transition-task.sh`).

## 3. Co dělají worker agenti v tomto příkladu

## module-builder-agent

1. Připraví scope implementace analytics modulu.
2. Udělá změny v modulu podle schváleného scope.
3. Vrátí handoff výstup ve standardním formátu.

## test-runner-agent

1. Spustí A: preflight + verify.
2. Spustí B: unit + contract checks.
3. Spustí C: smoke/e2e pokud je změna release nebo UI critical.
4. Vrátí pass/fail report a případné blockery.

## review-agent

1. Zkontroluje změny podle pravidel (`ARCH`, `CONTRACT`, `TEST`, `SEC`, `DOCS`).
2. Vrátí findings se severity a fix požadavky.
3. Rozhodnutí:
4. blocker => návrat do implementace
5. major/minor => pokračování s fix-listem

## docs-editor-agent

1. Doplní dokumentaci ke změnám.
2. Ověří canonical linky a docs standard.
3. Připraví changelog poznámku.

## 4. Co si má junior hlídat nejvíc

1. Nepouštět implementaci bez schváleného Spec/Plan.
2. Neschvalovat release bez test evidence + review evidence + docs evidence.
3. Brát `blocker` findings jako stopku, ne jako doporučení.
4. Vždy chtít jednotný handoff formát (`Scope`, `Files changed`, `Tests run`, `Risks`, `Next handoff`).
5. Očekávat, že parent/subtasks + gate pořadí řeší orchestrátor, ne user ručně.

## 5. Minimální "hotovo" definice pro analytics příklad

1. Parent task + všechny required subtasky existují.
2. Implementace je hotová a otestovaná (A -> B -> C dle potřeby).
3. Review gate nemá blocker findings.
4. Docs gate je splněná (včetně changelog poznámky).
5. Release readiness obsahuje Change Impact, Risks, Rollback or Mitigation.

## 6. Příklad mark-as-done po gate

Po úspěšném dokončení gate může orchestrátor zavolat:

- `scripts/workflow/linear-transition-task.sh --task-id <local-subtask-id> --state-name Done --pull`
