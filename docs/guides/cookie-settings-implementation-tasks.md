# Cookie Settings Integration Tasks (Varianta 2)

Cíl: zavést samostatnou doménu `CookieSettings` v CMS a napojit ji na Astro frontend přes dedikovaný endpoint.

## Task 01: Vytvořit datový model a migraci pro cookie nastavení
- [ ] Done
- Popis: Přidat model `App\Domain\Content\CookieSetting` a migraci (např. `cookie_settings`) pro uložení konfigurace consent banneru a kategorií cookies.
- Akceptační kritéria:
  - Existuje migrace pro tabulku `cookie_settings`.
  - Model má správné `fillable`/`casts` pro JSON struktury.
  - V databázi lze uložit jeden záznam konfigurace bez SQL chyb.
- Závislosti: žádné.

## Task 02: Definovat výchozí strukturu a validaci konfigurace
- [ ] Done
- Popis: Navrhnout a implementovat default strukturu (např. `banner`, `categories`, `services`, `policy_links`) a validační pravidla pro povinná pole.
- Akceptační kritéria:
  - V modelu nebo service vrstvě existuje metoda pro default config.
  - Backend validuje minimálně texty banneru, povinnou kategorii `necessary` a zap/vyp stavy kategorií.
  - Nevalidní payload vrací konzistentní chybu (422) s detailními poli.
- Závislosti: Task 01.

## Task 03: Rozšířit Filament Theme Settings o nový tab `Cookies`
- [ ] Done
- Popis: Do `ManageThemeSettings` přidat tab `Cookies` s formulářem pro editaci cookie konfigurace, ale data ukládat do samostatné domény `CookieSetting`.
- Akceptační kritéria:
  - V administraci je viditelný tab `Cookies`.
  - Pole v tabu odpovídají datové struktuře z Task 02.
  - Uložení funguje bez zásahu do `global/header/footer` dat.
- Závislosti: Task 01, Task 02.

## Task 04: Implementovat ukládání a načítání `CookieSetting` v CMS flow
- [ ] Done
- Popis: V `mount()` a `save()` flow napojit čtení/zápis konfigurace cookies na nový model; ošetřit inicializaci při prázdné DB.
- Akceptační kritéria:
  - `mount()` naplní formulář z `CookieSetting` nebo default hodnot.
  - `save()` persistuje změny do `cookie_settings`.
  - Po reloadu administrace zůstávají hodnoty konzistentní.
- Závislosti: Task 03.

## Task 05: Přidat API endpoint pro veřejnou cookie konfiguraci
- [ ] Done
- Popis: Přidat nový read-only endpoint (např. `GET /api/v1/cookies/config`) s výstupem připraveným pro frontend banner.
- Akceptační kritéria:
  - Endpoint vrací JSON ve stabilním kontraktu.
  - Pokud konfigurace neexistuje, vrací default konfiguraci.
  - Endpoint je dostupný bez autentizace a respektuje API konvence projektu.
- Závislosti: Task 01, Task 02, Task 04.

## Task 06: Přidat cache a invalidaci cookie konfigurace
- [ ] Done
- Popis: Zavést cache vrstvu pro endpoint konfigurace a invalidaci při změně v CMS.
- Akceptační kritéria:
  - Repeated request na endpoint používá cache.
  - Po uložení v CMS se cache invaliduje a endpoint vrací nové hodnoty.
  - Nedochází k návratu zastaralé konfigurace po save.
- Závislosti: Task 05.

## Task 07: Rozšířit frontend API klienta a TypeScript typy
- [ ] Done
- Popis: V Astro projektu přidat typy `CookieConfigResponse` a klienta `getCookieConfig()` pro nový endpoint.
- Akceptační kritéria:
  - Typy pokrývají všechny části JSON kontraktu.
  - `getCookieConfig()` vrací typově bezpečná data nebo fallback při chybě.
  - Build frontendu projde bez TypeScript chyb.
- Závislosti: Task 05.

## Task 08: Implementovat Cookie Consent banner komponentu v Astro
- [ ] Done
- Popis: Přidat UI komponentu banneru/modalu (accept all / reject optional / custom preferences) napojenou na `getCookieConfig()`.
- Akceptační kritéria:
  - Banner se zobrazuje pouze pokud není uložený consent stav.
  - Uživatel může uložit granular preference po kategoriích.
  - Consent stav se persistuje (cookie/localStorage) a respektuje při dalším načtení.
- Závislosti: Task 07.

## Task 09: Zavést script-gating podle consent kategorií
- [ ] Done
- Popis: Implementovat mechanismus, který načte nepovinné skripty (analytics/marketing) až po udělení souhlasu příslušné kategorie.
- Akceptační kritéria:
  - Bez souhlasu se analytics/marketing skripty nespouští.
  - Po udělení souhlasu se skripty aktivují bez reloadu, nebo dle definovaného chování.
  - Revokace souhlasu zastaví další tracking eventy.
- Závislosti: Task 08.

## Task 10: Přidat link na detailní nastavení cookies z patičky
- [ ] Done
- Popis: Do footeru přidat odkaz/tlačítko pro znovuotevření cookie preferencí (`Nastavení cookies`).
- Akceptační kritéria:
  - Odkaz je dostupný na všech stránkách s layoutem.
  - Kliknutí otevře stejné preference UI jako první banner.
  - Text odkazu je konfigurovatelný nebo lokalizovaný.
- Závislosti: Task 08.

## Task 11: Dokumentace API kontraktu a integračního flow
- [ ] Done
- Popis: Doplnit backend i frontend dokumentaci pro nový endpoint, datový kontrakt, consent storage a script-gating pravidla.
- Akceptační kritéria:
  - V `docs/api/endpoints/` je stránka pro cookie config endpoint.
  - Ve frontend docs je popsané použití `getCookieConfig()` a banner flow.
  - Dokumentace obsahuje troubleshooting pro fallback scénáře.
- Závislosti: Task 05, Task 07, Task 09.

## Task 12: Testy a QA scénáře
- [ ] Done
- Popis: Přidat backend testy endpointu + základní frontend test scénáře consent chování.
- Akceptační kritéria:
  - Backend testy pokrývají default payload, custom payload a cache invalidaci.
  - Frontend QA pokrývá first visit, accept all, reject optional, custom save, reopen preferences.
  - Je sepsaný checklist regresních kontrol před release.
- Závislosti: Task 06, Task 09, Task 10.
