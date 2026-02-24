# Form Schema Rules

Tento dokument popisuje pravidla pro `forms.schema` a `forms.data_options`.

**Databaze**
- `forms.name` je povinne (string).
- `forms.schema` je povinne (json array).
- `forms.active` je boolean (default `true`).
- `forms.data_options` je volitelne (json object).

**Schema zaklad**
- `schema` je pole objektu "field".
- Kazdy `field` musi mit `type` a `label`.
- `name` je povinny jen u typu, ktery podporuje `name`.
- `section` je typ pro nadpisy, neprochazi validaci a muze mit prazdny `name`.
- `required` je boolean, pokud je `true`, backend validace pridava `required`, jinak `nullable`.

**Povolene typy**
- `section`
- `text`
- `email`
- `tel`
- `number`
- `password`
- `url`
- `date`
- `time`
- `datetime-local`
- `textarea`
- `select`
- `checkbox`
- `radio`
- `checkbox_cards`
- `file`
- `hidden`

**Atributy field**
- `label` (string, povinne)
- `name` (string, povinne jen pokud typ podporuje `name`)
- `type` (string, povinne)
- `required` (boolean, nepovinny)
- `placeholder` (string, jen pro typy s podporou `placeholder`)
- `helper_text` (string, jen pro typy s podporou `helper_text`)
- `icon` (string, jen pro typy s podporou `icon`)
- `options` (array, jen pro typy s podporou `options`)

**Options pro choice typy**
- `options` je pole objektu `{ label, value }`.
- `label` a `value` jsou povinne.
- `icon` je povoleny jen u typu `checkbox_cards`.

**Field renderer (volitelny atribut `renderer`)**

Kazdy field muze mit volitelny atribut `renderer`, ktery urcuje, jak frontend vyrenderuje input.
Pokud `renderer` chybi nebo odkazuje na neznamy renderer, frontend pouzije fallback `input`.

Naming pravidlo: `renderer` musi byt **snake_case** (napr. `event_cards`, `checkbox_cards`).
Kebab-case varianta (napr. `event-cards`) neni podporovana.

Mapovani na frontend soubor:
```
field.renderer = "event_cards"  →  src/features/forms/blocks/fields/event_cards.astro
field.renderer = "checkbox_cards"  →  src/features/forms/blocks/fields/checkbox_cards.astro
field.renderer = "textarea"  →  src/features/forms/blocks/fields/textarea.astro
```

**Podporovane renderery a jejich datovy kontrakt**

| renderer | popis | specificke atributy |
|---|---|---|
| `input` | standardni text input (default / fallback) | `placeholder`, `required`, `input_type` |
| `textarea` | viceradkovy textovy input | `placeholder`, `required` |
| `select` | rozbalovaci seznam | `options`, `required` |
| `checkbox` | jediny checkbox s textem | `placeholder` (text vedle checkboxu), `required` |
| `radio` | skupina radio tlacitek | `options`, `required` |
| `checkbox_cards` | karty s checkboxy (multi-select s ikonami) | `options` (s volitelnym `icon`), `required` |
| `event_cards` | velke karty pro vyber udalosti (vizualni single/multi-select) | `options` (s volitelnym `icon`), `max_selected`, `columns_per_row`, `required` |
| `hidden` | skryte pole (type="hidden") | — |

Specificke atributy rendereru:
- `max_selected` (integer, jen `event_cards`) — maximalni pocet zaskrtnuti; `0` nebo chybejici = bez limitu
- `columns_per_row` (integer 1–4, jen `event_cards`) — pocet sloupcu mrizky; default `2`
- `input_type` (string) — HTML input type pro `input` renderer (napr. `email`, `tel`, `date`); fallback na `text`

**Chovani pri neznamem rendereru**

Pokud `field.renderer` obsahuje hodnotu, ktera nema odpovidajici soubor v `fields/` slozce,
frontend automaticky pouzije renderer `input`. V development rezimu se do konzole zapise warning.

**Priklad konfigurace — event_cards**

```json
{
  "name": "event_type",
  "label": "Typ akce",
  "type": "event_cards",
  "renderer": "event_cards",
  "required": true,
  "max_selected": 2,
  "columns_per_row": 3,
  "helper_text": "Vyberte maximalne 2 moznosti",
  "options": [
    { "label": "Svatba", "value": "wedding", "icon": "rings" },
    { "label": "Narozeniny", "value": "birthday", "icon": "cake" },
    { "label": "Firemni akce", "value": "corporate", "icon": "briefcase" }
  ]
}
```

**Priklad konfigurace — checkbox_cards**

```json
{
  "name": "services",
  "label": "Sluzby",
  "type": "checkbox_cards",
  "renderer": "checkbox_cards",
  "required": false,
  "options": [
    { "label": "Web design", "value": "design", "icon": "palette" },
    { "label": "Vyvoj", "value": "development", "icon": "code" },
    { "label": "SEO", "value": "seo", "icon": "search" },
    { "label": "Marketing", "value": "marketing", "icon": "megaphone" }
  ]
}
```

**Jak pridat novy renderer end-to-end (CMS → agent → frontend)**

1. **CMS** — definujte field v `schema` s atributem `renderer: "<renderer_name>"` (snake_case).
2. **Frontend** — vytvorte `src/features/forms/blocks/fields/<renderer_name>.astro` s Props `{ field: FormField }`.
   Soubor se automaticky objevi v registru pres `import.meta.glob('./fields/*.astro', { eager: true })`.
3. **Agent** — spustte `field-type-agent` (viz `/usr/local/var/www/agents/field-type-agent/CLAUDE.md`).
   Agent generuje Astro soubor do spravne cesty a aktualizuje navazne testy.
4. **Testy** — zkontrolujte, ze `ContactForm.registry.test.ts` obsahuje novy klic v mock modulech (nebo pridejte test).

**Backend validace (Form::getValidationRules)**
- `section` je preskocen.
- `required` -> `required`, jinak `nullable`.
- `email` -> `email`, `number` -> `numeric`, `url` -> `url`, ostatni -> `string`.

**data_options**
- `submit_button_text` (string)
- `success_title` (string)
- `success_message` (string)
- `sidebar` (array sekci)

**Sidebar sekce**
- `type` musi byt `contact_info`, `steps`, nebo `trust_indicators`.
- `title` je povinny string.
- `items` je pole objektu podle `type`.

**Sidebar items pro contact_info**
- `label` (povinny)
- `value` (povinny)
- `note` (nepovinny)
- `icon` (nepovinny)
- `tone` (nepovinny, hodnoty: `blue`, `teal`, `green`, `purple`, `emerald`)

**Sidebar items pro steps**
- `title` (povinny)
- `description` (povinny)
- `number` (nepovinny)
- `icon` (nepovinny)
- `tone` (nepovinny, hodnoty: `blue`, `teal`, `green`, `purple`, `emerald`)

**Sidebar items pro trust_indicators**
- `text` (povinny)
- `icon` (nepovinny)
- `tone` (nepovinny, hodnoty: `blue`, `teal`, `green`, `purple`, `emerald`)

**Konkretni priklad (Poptavka layout)**
```json
{
  "name": "Poptavka",
  "active": true,
  "schema": [
    { "name": "company", "label": "Firma", "type": "text", "required": true, "placeholder": "Nazev firmy" },
    { "name": "contact_name", "label": "Kontaktni osoba", "type": "text", "required": true, "placeholder": "Jmeno a prijmeni" },
    { "name": "email", "label": "E-mail", "type": "email", "required": true, "placeholder": "vas@email.cz" },
    { "name": "phone", "label": "Telefon", "type": "tel", "required": false, "placeholder": "+420 123 456 789" },
    {
      "name": "project_type",
      "label": "Typ projektu",
      "type": "radio",
      "required": true,
      "options": [
        { "label": "Novy web", "value": "new" },
        { "label": "Redesign", "value": "redesign" },
        { "label": "Rozsireni", "value": "extension" }
      ]
    },
    {
      "name": "budget",
      "label": "Rozpocet",
      "type": "select",
      "required": true,
      "options": [
        { "label": "do 100 tis.", "value": "lt-100" },
        { "label": "100 - 250 tis.", "value": "100-250" },
        { "label": "250 - 500 tis.", "value": "250-500" },
        { "label": "nad 500 tis.", "value": "gt-500" }
      ]
    },
    { "name": "deadline", "label": "Pozadovany termin", "type": "date", "required": false },
    { "name": "message", "label": "Popis projektu", "type": "textarea", "required": true, "placeholder": "Strucny popis cilu, funkci a pozadavku." },
    { "name": "consent", "label": "Souhlas se zpracovanim udaju", "type": "checkbox", "required": true }
  ],
  "data_options": {
    "submit_button_text": "Odeslat poptavku",
    "success_title": "Dekujeme!",
    "success_message": "Ozveme se vam do 24 hodin.",
    "sidebar": [
      {
        "type": "contact_info",
        "title": "Jak bude spoluprace probihat",
        "items": [
          { "label": "Email", "value": "hello@example.com", "note": "Odpovime do 24h", "icon": "mail", "tone": "blue" },
          { "label": "Telefon", "value": "+420 123 456 789", "note": "Po-Pa 9:00-17:00", "icon": "phone", "tone": "teal" },
          { "label": "Lokace", "value": "Praha / Brno", "note": "Schuzky i online", "icon": "map-pin", "tone": "green" }
        ]
      },
      {
        "type": "steps",
        "title": "Jak bude spoluprace probihat",
        "items": [
          { "title": "Zadani", "description": "Zpracujeme vase cilove body.", "number": "01", "tone": "blue" },
          { "title": "Navrh", "description": "Pripravime scope a harmonogram.", "number": "02", "tone": "teal" },
          { "title": "Realizace", "description": "Dodame web a nastavime mereni.", "number": "03", "tone": "green" }
        ]
      },
      {
        "type": "trust_indicators",
        "title": "Proc nam klienti veri",
        "items": [
          { "text": "Zkusenosti z 120+ webu", "icon": "check-circle", "tone": "green" },
          { "text": "Bezpecny a stabilni provoz", "icon": "shield", "tone": "blue" },
          { "text": "Jediny kontaktni bod", "icon": "chat", "tone": "purple" }
        ]
      }
    ]
  }
}
```
