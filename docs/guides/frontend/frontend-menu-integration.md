# Frontend Menu Integration Tasks

## Overview
Tasks for integrating the new Menu system into the Astro public frontend.

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/navigation` | Get main menu items (default) |
| GET | `/api/v1/navigation/{slug}` | Get menu items by menu slug |
| GET | `/api/v1/menus/{slug}` | Get full menu with metadata |

### Response Format

```json
{
  "data": [
    {
      "id": 1,
      "title": "Home",
      "slug": "home",
      "url": "/",
      "target": "_self",
      "children": [
        {
          "id": 2,
          "title": "About",
          "slug": "about",
          "url": "/about",
          "target": "_self",
          "children": []
        }
      ]
    }
  ]
}
```

---

## Tasks

### 1. Update API Types in `src/lib/api.ts`

- [ ] Add `target` field to `NavigationItem` interface
- [ ] Ensure type matches new API response

```typescript
export interface NavigationItem {
  id: number;
  title: string;  // Changed from 'label'
  slug: string;
  url: string;
  target: '_self' | '_blank';
  children?: NavigationItem[];
}
```

### 2. Update Navigation Component

- [ ] Open `src/components/Navigation.astro`
- [ ] Use `item.title` instead of `item.label` (if changed)
- [ ] Add `target` attribute to links
- [ ] Handle anchor links (#section) correctly

```astro
<a
  href={item.url}
  target={item.target}
  rel={item.target === '_blank' ? 'noopener noreferrer' : undefined}
>
  {item.title}
</a>
```

### 3. Handle Different Link Types

Frontend should handle:
- `/page` - internal relative links
- `#section` - anchor links (same page)
- `/page#section` - internal with anchor
- `https://...` - external links

### 4. Mobile Navigation

- [ ] Ensure target attribute works in mobile menu
- [ ] External links should show indicator icon

### 5. Test Build

```bash
cd /usr/local/var/www/ercee-frontend
npm run build
```

### 6. Verify

- [ ] Navigation renders correctly
- [ ] Links work (internal, anchor, external)
- [ ] Target `_blank` opens new tab
- [ ] Mobile menu works

---

## Optional: Footer Menu

If you want a separate footer menu:

1. Create `footer` menu in CMS Admin
2. Fetch with `/api/v1/navigation/footer`
3. Create `FooterNavigation.astro` component

```astro
---
import { getNavigation } from '../lib/api';

// Fetch footer menu by slug
const footerItems = await fetch(`${API_BASE_URL}/navigation/footer`)
  .then(r => r.json())
  .then(d => d.data);
---
```

---

## Quick Start

1. In CMS Admin, go to **Content > Menus**
2. Edit "Main Navigation" menu
3. Add navigation items with:
   - Title
   - Link Type: Page or Custom URL
   - Target: Same window / New window
4. Save and test on frontend
