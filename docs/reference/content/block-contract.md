# Block Contract Documentation

This document defines the data contract for CMS builder blocks and how they are rendered in Astro.

## Overview

Pages use Filament Builder to manage content blocks. Each block has a `type` and `data` structure that is returned by the API as-is.

## Block Data Structure

All blocks follow this format when stored in the database:

```json
[
  {
    "type": "block_type_name",
    "data": {
      // block-specific fields
    }
  }
]
```

## CMS Block Types (current)

### Hero Block (`hero`)

Full-width hero section with background image and dual CTA support.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | string | Yes | Main headline (max 160 chars) |
| `subtitle` | string | No | Subtitle text (max 160 chars) |
| `description` | string | No | Description (max 500 chars) |
| `background_media_uuid` | uuid | No | Media UUID for background image |
| `primary.label` | string | No | Primary CTA button label (max 80 chars) |
| `primary.link.page_id` | int | No | Primary CTA linked page ID |
| `primary.link.url` | string | No | Primary CTA custom URL |
| `primary.link.anchor` | string | No | Primary CTA anchor |
| `secondary.label` | string | No | Secondary CTA button label |
| `secondary.link.*` | object | No | Same structure as primary.link |
| `stats` | array | No | Array of `{value, label}` stat items |

**Example:**
```json
{
  "type": "hero",
  "data": {
    "title": "Welcome to Our Site",
    "subtitle": "Industry Leader",
    "description": "Discover amazing things",
    "background_media_uuid": "a1b2c3d4-...",
    "primary": {
      "label": "Get Started",
      "link": { "page_id": null, "url": "/signup", "anchor": null }
    },
    "secondary": {
      "label": "Learn More",
      "link": { "page_id": 5, "url": null, "anchor": "features" }
    },
    "stats": [
      { "value": "500+", "label": "Clients" }
    ]
  }
}
```

> **Link data convention:** All link fields use nested `{page_id, url, anchor}` objects. In the admin UI these are rendered by `LinkPicker`. Media fields store UUIDs resolved by `BlockMediaResolver` at API response time.

### Text Block (`text`)

Rich text content with optional heading.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `heading` | string | No | Section heading (max 255 chars) |
| `body` | string | Yes | Rich HTML content |

**Example:**
```json
{
  "type": "text",
  "data": {
    "heading": "About Us",
    "body": "<p>Our company was founded...</p>"
  }
}
```

### Image Block (`image`)

Single image with caption support.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `image` | string | Yes | Path to image file |
| `alt` | string | Yes | Alt text for accessibility (max 255 chars) |
| `caption` | string | No | Image caption (max 255 chars) |

**Example:**
```json
{
  "type": "image",
  "data": {
    "image": "pages/images/team-photo.jpg",
    "alt": "Our team at the annual meeting",
    "caption": "Team meeting 2024"
  }
}
```

### CTA Block (`cta`)

Call-to-action section with button.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | string | Yes | CTA headline (max 255 chars) |
| `description` | string | No | Supporting description (max 500 chars) |
| `button_text` | string | Yes | Button label (max 100 chars) |
| `button_url` | string | Yes | Button destination URL (max 255 chars) |
| `style` | enum | No | Button style: `primary`, `secondary`, `outline` (default: `primary`) |

**Example:**
```json
{
  "type": "cta",
  "data": {
    "title": "Ready to get started?",
    "description": "Join thousands of happy customers today.",
    "button_text": "Sign Up Now",
    "button_url": "/register",
    "style": "primary"
  }
}
```

### Form Embed Block (`form_embed`)

Embeds a dynamic form from the Forms module.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `form_id` | integer | Yes | ID of the form to embed |
| `title` | string | No | Custom title override (max 255 chars) |
| `description` | string | No | Custom description (max 500 chars) |

**Example:**
```json
{
  "type": "form_embed",
  "data": {
    "form_id": 1,
    "title": "Contact Us",
    "description": "Fill out the form below and we'll get back to you."
  }
}
```

## Astro Mapping

The Astro frontend maps CMS data in `ercee-frontend/src/lib/api/endpoints/pages.ts`.

- `text`: combines `heading` + `body` into a single HTML string (`content`)
- `image`: maps `image` to `url`
- `cta`: direct mapping
- `form_embed`: direct mapping

Other block types are passed through without mapping. If a block needs a different shape on the frontend, add a mapper.

## Rendering Pipeline

### Public Frontend (Astro)

1. `Page::getBlocks()` returns builder blocks via the API.
2. Astro maps data in `ercee-frontend/src/lib/api/endpoints/pages.ts`.
3. Blocks render via `ercee-frontend/src/components/BlockRenderer.astro`.

### Admin Preview (Blade)

Preview is rendered by:
- `resources/views/filament/pages/preview.blade.php`
- `resources/views/filament/products/preview.blade.php`

Block preview uses Blade components in:
- `resources/views/components/blocks/`
- fallback: `resources/views/frontend/blocks/`

## Adding New Block Types

1. Add constant to `App\Domain\Content\Page` (e.g., `BLOCK_TYPE_GALLERY = 'gallery'`).
2. Create a block class in `app/Filament/Blocks/` and clear cache: `php artisan blocks:clear`.
3. Add Astro types and mapping in `ercee-frontend/src/lib/api/types.ts` and `ercee-frontend/src/lib/api/endpoints/pages.ts`.
4. Add Astro component in `ercee-frontend/src/components/blocks/` and wire it in `BlockRenderer.astro`.
5. Add a Blade preview component in `resources/views/components/blocks/`.

## Migration Notes

The system supports both legacy Repeater format (`content.blocks`) and new Builder format (flat array). The `Page::getBlocks()` method handles both formats transparently.
