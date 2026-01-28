# LLM Guide: Create a New Custom Post Type (End-to-End)

## Purpose
This guide tells an LLM exactly how to add a **new custom post type** and wire it through the **JSON-driven field type system** and related dependencies. Follow these steps in order. Do not skip steps.

## Fast Path (Recommended) — Artisan Command
Use the command to generate the JSON entry and translations in one go:
```sh
php artisan make:form-field-type <type_key>
```

Required inputs (prompted if not provided as options):
- `category` (`input|choice|section`)
- `renderer` (`input|textarea|select|checkbox|radio|hidden|section|checkbox_cards`)
- `supports` (comma-separated list)
- `label`/`description` for **en** and **cs**

Optional inputs:
- `--input-type=` (only when `renderer=input`)
- `--options=` (JSON string, e.g. `{"required":true,"kind":"label_value"}`)
- `--patch-frontend` (auto-insert renderer skeleton into ContactForm.astro)
- `--frontend-path=` (override frontend repo path; default `../ercee-frontend`)
- `--force` to overwrite an existing type
- `--dry-run` to preview changes without writing files

Example:
```sh
php artisan make:form-field-type checkbox_cards \
  --category=choice \
  --renderer=checkbox_cards \
  --supports=name,required,helper_text,options \
  --options='{"required":true,"kind":"label_value"}' \
  --patch-frontend \
  --label-en="Checkbox cards" \
  --description-en="Multiple options displayed as cards with checkboxes." \
  --label-cs="Checkbox karty" \
  --description-cs="Více možností jako karty s checkboxem."
```

After running the command, continue with **Step 5** if you introduced a **new renderer**.

## Golden Rules
- The **API remains the source of data** (values, required flags, options, etc.).
- The **JSON registry is the source of render patterns** and available field types.
- **Localization must be added** for labels and descriptions.
- **Build pipeline must copy JSON** to the frontend public assets.
- No tests are required unless explicitly asked.

## Step 1 — Add the Field Type to JSON
File: `resources/form-field-types.json`

Add a new object entry with:
- `label_key` and `description_key`
- `renderer` (how it renders in Astro)
- optional `input_type` (for `renderer: "input"` only)
- `supports` (controls CMS UI visibility)
- `options` only if the field needs options

Template:
```json
"<type_key>": {
  "category": "input" | "choice" | "section",
  "label_key": "<type_key>.label",
  "description_key": "<type_key>.description",
  "renderer": "input" | "textarea" | "select" | "checkbox" | "radio" | "hidden" | "section" | "checkbox_cards",
  "input_type": "text" | "email" | "...",
  "supports": ["name", "required", "placeholder", "helper_text", "options", "icon"],
  "options": { "required": true, "kind": "label_value" },
  "defaults": {}
}
```

Notes:
- Use `input_type` only when `renderer` is `input`.
- If the field needs selectable options, include `options` and add `"options"` to `supports`.

## Step 2 — Add Localized Labels and Descriptions
Files:
- `lang/cs/form-field-types.php`
- `lang/en/form-field-types.php`

Add the label/description entries:
```php
'<type_key>' => [
    'label' => '...',
    'description' => '...'
],
```

Rules:
- Keys must match `label_key` / `description_key` in JSON.
- Keep descriptions short and clear (one sentence).

## Step 3 — Ensure CMS UI Visibility is Driven by JSON
File: `app/Support/FormFieldTypeRegistry.php`

The registry already reads:
- `renderer`
- `input_type`
- `supports`
- `label_key`, `description_key`

If you add new support flags, make sure `supports()` checks are used in the CMS form (`FormResource`) to show/hide settings.

## Step 4 — Update FormResource UI (if needed)
File: `app/Filament/Resources/FormResource.php`

Confirm the field controls are using:
- `FormFieldTypeRegistry::supports($type, '<feature>')`

Add new visibility rules only when you introduce a **new support flag**.

## Step 5 — Frontend Render Mapping (Astro)
File: `/usr/local/var/www/ercee-frontend/src/components/blocks/ContactForm.astro`

Rules:
- The API data is used for values; JSON is used only to pick the renderer.
- Render logic must use `renderer` from JSON.
- If the renderer is new (e.g., `checkbox_cards`), implement its HTML block in the renderer switch/branch.

If you add a new renderer:
1) Add it to `resources/form-field-types.json`.
2) Add its render block in `ContactForm.astro`.

## Step 6 — Keep the Frontend JSON in Sync
Frontend build copies JSON from CMS:
- `form-field-types.json` must be copied during build.

File: `/usr/local/var/www/ercee-frontend/.github/workflows/build-deploy.yml`

Confirm this block includes:
```sh
cp cms/resources/form-field-types.json public/form-field-types.json
```

## Step 7 — Verify the End-to-End Flow (Manual)
- Create a new form in CMS and use the new field type.
- Check that the field appears in CMS with correct labels.
- Check the frontend renders the correct UI.

## Example: Add `checkbox_cards`
1) JSON:
```json
"checkbox_cards": {
  "category": "choice",
  "label_key": "checkbox_cards.label",
  "description_key": "checkbox_cards.description",
  "renderer": "checkbox_cards",
  "supports": ["name", "required", "helper_text", "options"],
  "options": { "required": true, "kind": "label_value" },
  "defaults": {}
}
```

2) Lang:
```php
'checkbox_cards' => [
    'label' => 'Checkbox cards',
    'description' => 'Multiple options displayed as cards with checkboxes.',
],
```

3) Frontend renderer:
- Add a `checkbox_cards` branch in `ContactForm.astro`.

## Common Mistakes to Avoid
- Forgetting to add localization keys.
- Adding a new field type in JSON but not adding a renderer in Astro.
- Changing API shape instead of using JSON for render-only metadata.
- Not copying JSON into frontend public assets during build.
