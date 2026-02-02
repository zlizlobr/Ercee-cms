# Preview Frontend (Blade)

Public site is rendered by the Astro frontend in a separate repo. This file documents the **Blade preview** that is used inside Filament for quick content checks.

For the public frontend guide, see: `docs/astro-frontend-guide.md`.

## Preview pages

- Page preview: `resources/views/filament/pages/preview.blade.php`
- Product preview: `resources/views/filament/products/preview.blade.php`

Preview uses Tailwind via CDN and does not depend on built Vite assets.

## Block rendering in preview

Preview tries to render each CMS block using Blade components:

1. `resources/views/components/blocks/{block}.blade.php`
2. Fallback: `resources/views/frontend/blocks/{block}.blade.php`

If no component exists, the preview shows a warning card with the block type.

## Adding a preview template for a new block

1. Create the block in the CMS (see `docs/creating-custom-blocks.md`).
2. Add a preview Blade component in `resources/views/components/blocks/`.
3. If you have legacy templates, keep them in `resources/views/frontend/blocks/` and the preview will still pick them up.
