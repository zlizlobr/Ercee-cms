# Block Command Implementation - Task Plan (Phase 1)

This document outlines implementation tasks for an Artisan-based scaffold command
that generates CMS blocks and Astro components from a JSON field schema.

## Goal

Create an Artisan command that:
- Accepts a block name and a JSON schema describing Filament fields.
- Generates CMS block class + localization + optional preview blade.
- Updates `app/Domain/Content/Page.php` constants and `blockTypes()`.
- Generates Astro component + auto-updates `types.ts` and `pages.ts`.
- Uses a registry map in Astro (not manual edits in `BlockRenderer.astro`).
- Writes to the Astro repo at `../ercee-frontend`.

## Phase 1 Scope

Only define structure, templates, and skeleton behavior. No full generator logic.

## Implementation Tasks

### 1) Command Skeleton

- Create `app/Console/Commands/MakeCmsBlock.php`.
- Signature proposal:
  - `make:cms-block {name} {schema?} {--schema-file=} {--force}`
- Parse JSON from `{schema}` or `--schema-file`.
- Normalize block name:
  - `Hero Banner` -> `hero_banner` (block type)
  - `Hero Banner` -> `HeroBanner` (class/component names)
- Validate schema:
  - Required keys: `fields`, `label`.
  - Supported field types map to Filament components.

### 2) JSON Schema Shape (Baseline)

Define a minimal schema contract (docs + validation), e.g.:

```json
{
  "label": "Gallery",
  "icon": "heroicon-o-squares-2x2",
  "order": 60,
  "fields": [
    { "type": "text", "name": "title", "label": "Title", "maxLength": 255 },
    { "type": "textarea", "name": "description", "label": "Description" },
    { "type": "file", "name": "images", "label": "Images", "multiple": true }
  ]
}
```

### 3) Template Assets (CMS)

Create stub templates in `resources/stubs/blocks/`:
- `block-class.stub` for `app/Filament/Blocks/*Block.php`
- `block-preview.stub` for `resources/views/components/blocks/*.blade.php`
- `lang-admin.stub` for `lang/*/admin.php` inserts (or use inline updates)

Template variables to support:
- `{{ blockType }}`
- `{{ className }}`
- `{{ label }}` / `{{ icon }}`
- `{{ order }}`
- `{{ schemaFields }}` (generated Filament schema)

### 4) Filament Field Mapping

Implement a mapping table from JSON field types to Filament components:
- `text` -> `TextInput`
- `select` -> `Select` (with `options`)
- `checkbox` -> `Checkbox`
- `toggle` -> `Toggle`
- `checkbox_list` -> `CheckboxList`
- `radio` -> `Radio`
- `datetime` -> `DateTimePicker`
- `file` -> `FileUpload` (support `multiple`, `image`, `directory`)
- `richtext` -> `RichEditor`
- `markdown` -> `MarkdownEditor`
- `repeater` -> `Repeater` (with nested fields)
- `builder` -> `Builder` (with nested blocks)
- `tags` -> `TagsInput`
- `textarea` -> `Textarea`
- `key_value` -> `KeyValue`
- `color` -> `ColorPicker`
- `toggle_buttons` -> `ToggleButtons`
- `slider` -> `Slider`
- `code` -> `CodeEditor`
- `hidden` -> `Hidden`

Start with a minimal subset and mark unsupported types as errors.

### 5) Page Constant + blockTypes()

Tasks:
- Insert `public const BLOCK_TYPE_<NAME> = '<block_type>';`
  into `app/Domain/Content/Page.php`.
- Add it to `blockTypes()` with label translation:
  - `__('admin.page.blocks.<block_type>')`
- Keep alphabetical or defined order.

### 6) Localization Inserts

Update:
- `lang/en/admin.php`
- `lang/cs/admin.php`

Add:
- `admin.page.blocks.<block_type>`
- `admin.page.fields.<field_name>` for each field

Strategy:
- Use simple string insertion near existing `page.blocks` and `page.fields` sections.

### 7) Astro Scaffolding

Target repo: `../ercee-frontend`

Create stub templates in CMS repo:
- `resources/stubs/astro/BlockComponent.astro.stub`
- `resources/stubs/astro/block-registry.stub` (if needed)

Tasks:
- Create `src/components/blocks/<Name>.astro`.
- Update a registry map (e.g. `src/components/blocks/registry.ts`) to include the new component.
- Update `src/lib/api/types.ts` with a new block type interface.
- Update `src/lib/api/endpoints/pages.ts` with automatic mapping.

### 8) Cross-Repo Writes (Safety)

Implementation tasks:
- Resolve Astro repo path with `realpath`.
- Abort if path missing or outside expected base.
- Optional `--dry-run` to print planned changes.
- Require `--force` to overwrite existing files.

### 9) Testing Notes (Later Phase)

- Add a fixture schema and a command test.
- Verify generated PHP compiles.
- Verify Astro types compile with `tsc`.

## Skeleton Workflow (User Story)

1. User runs:
   ```
   php artisan make:cms-block "Feature Grid" --schema-file=block.json
   ```
2. Command generates CMS block + preview + localization.
3. Command generates Astro component + types + mapping updates.
4. Optional: prints follow-up commands:
   - `php artisan blocks:clear`
   - `pnpm lint` in frontend

## Open Questions

- Should mapping in `pages.ts` always pass-through, or auto-map fields?
- Should the command edit a registry map (recommended), or rely on dynamic import?
- How should nested/repeater fields be represented in Astro types?
