# Application PHPDoc And Notes Guide

## Účel
Tento guide definuje jednotný standard pro:
- PHPDoc v `app/Application/*`
- "note pole" v dokumentaci use-case částí aplikace

Používej tento soubor jako canonical zdroj pravidel. V task/plán dokumentech pravidla neduplikuj, pouze odkazuj sem.

## Jak mě nasměrovat na standard
Při zadání použij větu:

`Použij standard z docs/app/Application/application-phpdoc-notes-guide.md.`

To znamená:
1. doplnit chybějící PHPDoc podle sekce níže,
2. držet jednotné note pole podle šablony níže.

## PHPDoc Standard (app/Application)
Platí pro `Contracts/`, `Content/`, `Commands/`, `Results/` a další use-case podsložky.

### Class/Interface Docblock
- Každá `class` / `interface` má stručný 1řádkový popis účelu.
- Popis má být behaviorální (co dělá), ne technicky vágní.

### Method Docblock
- Každá veřejná metoda má docblock.
- U neveřejných metod doplň docblock, pokud dělá netriviální transformaci nebo validaci.
- U metod s `array` daty používej přesné anotace (`array<string, mixed>`, shape).

### Povinné anotace
- `@param`:
  - když parametr nese business význam (např. command payload),
  - vždy u `array` parametrů s generiky/shape.
- `@return`:
  - vždy u metod vracejících `array`/shape,
  - u `bool`/`int` pokud je potřeba vysvětlit význam (např. exit code, success flag).
- `@var`:
  - u properties typu `array`, kde nativní typ nepopisuje obsah.
  - **must have** i u properties s business významem (včetně scalar/object typů), kde samotný název nebo nativní typ nevysvětluje účel hodnoty.
  - popis piš jednou větou: co hodnota reprezentuje v kontextu class (ne jen opakování typu).

### Doporučené typy
- Preferuj:
  - `array<string, mixed>`
  - `array<int, string>`
  - `array{key: type, ...}` (shape)
- Vyhýbej se neinformativnímu `array` bez upřesnění v PHPDoc.

## Note Pole Standard (docs/app/*)
Pro README/use-case poznámky používej jednotná pole v tomto pořadí:

1. `Purpose`: co use-case řeší
2. `Inputs`: vstupní command/DTO
3. `Outputs`: result DTO + success/failure varianty
4. `Rules`: validační/guard pravidla
5. `Dependencies`: doménové modely/služby
6. `Risks`: edge cases, technická omezení

### Mini šablona
```md
## Notes
- Purpose: ...
- Inputs: ...
- Outputs: ...
- Rules: ...
- Dependencies: ...
- Risks: ...
```

## Definition Of Done
- V `app/Application` nejsou chybějící PHPDocy u klíčových class/method.
- `array` parametry/návraty mají generiky nebo shape.
- Properties s business významem mají `@var` s jednovětým popisem významu hodnoty.
- Docs používají stejné note pole (`Purpose`, `Inputs`, `Outputs`, `Rules`, `Dependencies`, `Risks`).
