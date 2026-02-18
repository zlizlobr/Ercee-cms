# Hybrid Browser QA Plan (Varianta 3)

Cíl: zavést hybridní kontrolu chování agenta v prohlížeči kombinací deterministických Playwright testů a agentických smoke scénářů (`agent-browser` nebo alternativní knihovna).

## Task 01: Definovat scope kritických flow pro deterministic testy
- [ ] Done
- Popis: Vybrat 3-5 kritických UI flow, které musí být 100% stabilní (např. vytvoření stránky, otevření block pickeru, přidání bloku, uložení).
- Akceptační kritéria:
  - Existuje seznam kritických flow s prioritou P1/P2.
  - Každý flow má jasný expected outcome.
  - Je určeno, které flow půjdou do PR gate.
- Závislosti: žádné.

## Task 02: Definovat scope agentických smoke scénářů
- [ ] Done
- Popis: Vybrat 1-2 agentické scénáře simulující reálné chování (např. "vytvoř stránku a přidej vhodný blok podle zadání").
- Akceptační kritéria:
  - Je sepsaný seznam smoke scénářů s limitem běhu.
  - U každého scénáře je definice "pass/fail".
  - Je určeno, zda běží na PR nebo jen nightly.
- Závislosti: Task 01.

## Task 03: Zvolit knihovnu pro agentické běhy a ověřit PoC
- [ ] Done
- Popis: Porovnat `vercel-labs/agent-browser` vs. alternativu a udělat krátký PoC na jednom scénáři.
- Akceptační kritéria:
  - Je zdokumentované rozhodnutí (vybraná knihovna + důvod).
  - PoC projde alespoň 3x po sobě na stejném scénáři.
  - Známé limity řešení jsou sepsány.
- Závislosti: Task 02.

## Task 04: Připravit testovací prostředí a seed dat
- [ ] Done
- Popis: Stabilizovat test data pro lokální běh i CI (uživatel, demo page, základní konfigurace bloků).
- Akceptační kritéria:
  - Existuje repeatable setup script pro test běhy.
  - Testy neběží proti náhodnému produkčnímu stavu.
  - Setup je spustitelný jedním příkazem.
- Závislosti: Task 01.

## Task 05: Implementovat Playwright test harness
- [ ] Done
- Popis: Založit složku/e2e projekt, přidat config, fixtures, helpery a screenshot/video/traces.
- Akceptační kritéria:
  - Playwright testy se spustí lokálně příkazem z `package.json`.
  - Selhání ukládá artefakty (trace/screenshot/video).
  - Test runtime a retry policy jsou definované.
- Závislosti: Task 04.

## Task 06: Napsat deterministic P1 scénáře v Playwright
- [ ] Done
- Popis: Implementovat kritické scénáře jako stabilní scripted testy (včetně regresního scénáře pro block picker overlay).
- Akceptační kritéria:
  - P1 scénáře mají asserty na UI stav, ne jen "klikání".
  - Pokryt je minimálně scénář prázdného builderu + otevření pickeru.
  - Testy jsou zelené opakovaně (min. 5 běhů bez flaky failu).
- Závislosti: Task 05.

## Task 07: Implementovat agentické smoke runner scénáře
- [ ] Done
- Popis: Přidat runner pro agentické browser flow a minimálně 1 smoke scénář s logováním kroků.
- Akceptační kritéria:
  - Runner je spustitelný lokálně samostatným příkazem.
  - Výstup obsahuje kroky agenta + výsledek validace.
  - Při failu jsou dostupné artefakty pro debugging.
- Závislosti: Task 03, Task 04.

## Task 08: Zavést společný report formát a artefakty
- [ ] Done
- Popis: Sjednotit výstup deterministic i agentických běhů (JSON + lidský markdown summary).
- Akceptační kritéria:
  - Obě vrstvy testů produkují čitelný summary report.
  - Report obsahuje test name, status, duration, linky na artefakty.
  - Formát je použitelný v CI summary.
- Závislosti: Task 06, Task 07.

## Task 09: CI integrace pro Playwright PR gate
- [ ] Done
- Popis: Přidat job do CI pipeline, který blokuje merge při pádu deterministic P1 testů.
- Akceptační kritéria:
  - PR pipeline spouští Playwright P1 scénáře.
  - Pád P1 scénářů nastaví workflow na fail.
  - Artefakty jsou přístupné z CI jobu.
- Závislosti: Task 06, Task 08.

## Task 10: CI integrace pro agentické smoke testy
- [ ] Done
- Popis: Přidat agentické smoke testy jako neblokující job (doporučeně nightly nebo post-merge).
- Akceptační kritéria:
  - Smoke job běží automaticky podle definovaného plánu.
  - Výsledek je viditelný v dashboardu/reportu.
  - Je definované, kdo a jak řeší opakované smoke failury.
- Závislosti: Task 07, Task 08.

## Task 11: Definovat pravidla flaky test managementu
- [ ] Done
- Popis: Sepsat pravidla pro retry, quarantine, threshold a eskalaci regresí.
- Akceptační kritéria:
  - Existuje krátký policy dokument pro flaky testy.
  - Je jasně oddělen flaky fail vs. reálná regrese.
  - Je definována SLA reakce na červenou pipeline.
- Závislosti: Task 09, Task 10.

## Task 12: Pilot, vyhodnocení a rollout
- [ ] Done
- Popis: Spustit 1-2 týdenní pilot, vyhodnotit stabilitu a náklady, pak rozhodnout o plné aktivaci.
- Akceptační kritéria:
  - Pilot má metriku pass-rate, flaky-rate, průměrný runtime.
  - Je sepsané rozhodnutí "go/no-go" s konkrétními daty.
  - Po schválení je plán označen jako baseline QA procesu.
- Závislosti: Task 11.

