# Junior Workflow Postup (User -> Agenti)

Tento návod říká, co máš jako user poslat a co se děje automaticky ve workflow.

## 1) Musím spouštět nějaký příkaz pro agenty?

Ne. V běžném flow stačí poslat zadání v textu.
Orchestrátor (run-tasks-agent) řeší:

1. rozpad na parent task + gate subtasks,
2. volání specialist agentů (module/block/field/test/review/docs),
3. test/review loop,
4. evidence do `artifacts/gates/...`,
5. přepínání Linear tasků do `Done` po úspěšné gate.

## 2) Jak má vypadat správné zadání od tebe

Pošli vždy:

1. repo/modul kde se pracuje,
2. cílové změny (co má být nové/chování),
3. omezení (co nesahat, kompatibilita, termín),
4. Definition of Done (kdy je to hotové).

## 3) Šablona, kterou můžeš kopírovat

```md
Repo/modul:
- /usr/local/var/www/ercee-modules/ercee-module-commerce

Cíl:
- ...

Požadavky:
- ...

Omezení:
- ...

Definition of Done:
- ...
```

## 4) Tvůj commerce příklad přepsaný do workflow-ready zadání

Repo/modul:

- `/usr/local/var/www/ercee-modules/ercee-module-commerce`

Cíl:

1. Přidat nový block „carousel tip produktů“.
2. V admin tabulce přidat možnost řazení podle názvu.
3. Přidat evidenci skladu podobnou výchozímu WooCommerce modelu (in stock / out of stock + quantity).
4. Přidat do Commerce novou záložku `Settings`.
5. V `Settings` přidat tab `XML Feeds`.
6. Ověřit a zdokumentovat, zda lze nastavovat prioritu pořadí tabů; pokud ne, navrhnout a implementovat způsob.

Omezení:

1. Bez breaking změn API kontraktů.
2. Zachovat kompatibilitu existujících dat (migrace musí být bezpečná).
3. UI změny musí projít test gate a review gate.

Definition of Done:

1. Všechny 4 oblasti změn jsou implementované.
2. Test Gate je green (A -> B -> C dle kritičnosti).
3. Ralph Review Gate bez blocker findings.
4. Docs Gate doplněný (včetně changelog poznámky).
5. Release Readiness summary je vyplněné.

## 5) Co je „srozumitelný task“ vs „nesrozumitelný task“

Srozumitelný:

- „Přidej admin sorting podle názvu v seznamu produktů (ASC/DESC), default beze změny, bez dopadu na API.“

Nesrozumitelný:

- „Uprav admin list.“

## 6) Co budeš dělat ty během průběhu

1. Schválíš Spec/Plan.
2. U failu testů/review určíš prioritu fixu.
3. Na konci schválíš Release Readiness.

Všechno ostatní je orchestrace agentů.

