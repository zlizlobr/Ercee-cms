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
