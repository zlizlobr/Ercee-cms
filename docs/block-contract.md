# Block Contract Documentation

This document defines the data contract for page builder blocks used in the CMS.

## Overview

Pages use Filament Builder to manage content blocks. Each block has a `type` and `data` structure.

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

## Available Block Types

### Hero Block (`hero`)

Full-width hero section with background image support.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `heading` | string | Yes | Main headline (max 255 chars) |
| `subheading` | string | No | Supporting text (max 500 chars) |
| `background_image` | string | No | Path to background image |
| `button_text` | string | No | CTA button label (max 100 chars) |
| `button_url` | string | No | CTA button URL (max 255 chars) |

**Example:**
```json
{
  "type": "hero",
  "data": {
    "heading": "Welcome to Our Site",
    "subheading": "Discover amazing things",
    "background_image": "pages/heroes/hero-bg.jpg",
    "button_text": "Get Started",
    "button_url": "/signup"
  }
}
```

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

## Rendering Pipeline

### Frontend Rendering

1. `Page::getBlocks()` retrieves blocks from the `content` JSON column
2. Frontend view iterates blocks using `<x-dynamic-component>`
3. Each block type maps to a Blade component in `resources/views/components/blocks/`

```blade
@foreach($page->getBlocks() as $block)
    <x-dynamic-component
        :component="'blocks.' . str_replace('_', '-', $block['type'])"
        :block="$block"
    />
@endforeach
```

### Filament Preview

Each block type has a preview Blade view in `resources/views/filament/blocks/` configured via `->preview()` on the Builder Block.

## Adding New Block Types

1. Add constant to `App\Domain\Content\Page` (e.g., `BLOCK_TYPE_GALLERY = 'gallery'`)
2. Add to `Page::blockTypes()` array
3. Create Builder\Block in `PageResource::form()`
4. Create Blade component: `resources/views/components/blocks/gallery.blade.php`
5. Create Filament preview: `resources/views/filament/blocks/gallery.blade.php`
6. Update this documentation

## Migration Notes

The system supports both legacy Repeater format (`content.blocks`) and new Builder format (flat array). The `Page::getBlocks()` method handles both formats transparently.
