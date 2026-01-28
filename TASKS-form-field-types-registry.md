# Form field types registry (localized)

## Goals
- Replace inline field type options in `FormResource` with a registry pattern consistent with `FormIconRegistry`.
- Store field types in a JSON file under `resources/` so the frontend build can copy it (same flow as icons).
- Localize labels via `lang` files while keeping the JSON source of truth.
- Keep API stable for existing form schemas.

## Proposed steps (pending approval)
1. Add `resources/form-field-types.json` as the source of truth for field type keys (mirrors icon JSON flow so it can be copied to the frontend build).
2. Add a new registry class `App\Support\FormFieldTypeRegistry` that reads the JSON and maps keys to localized labels from `lang/`.
3. Add a language file for field type labels (e.g., `lang/cs/form-field-types.php`) keyed by field type.
4. (Optional) Add `lang/en/form-field-types.php` as fallback for missing locales.
5. Update `app/Filament/Resources/FormResource.php` to use `FormFieldTypeRegistry::options()` for the field type select.
6. (Optional) Add a small unit test for the registry (if a tests setup is present), otherwise skip.

## Notes / decisions to confirm
- Target locale(s): confirm primary locale (e.g., `cs`) and whether `en` should be included.
- Confirm JSON filename `resources/form-field-types.json` matches frontend build copy flow.
- File name for language file: confirm `form-field-types.php`.
- Whether to add a simple test.
