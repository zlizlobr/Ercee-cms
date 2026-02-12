# Astro Frontend Development Guide

This guide explains the architecture and workflow for the public Astro frontend.

**Repository:** [github.com/zlizlobr/ercee-frontend](https://github.com/zlizlobr/ercee-frontend)

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        BUILD TIME                                │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────────┐  │
│  │ Astro Build │───▶│ Fetch API    │───▶│ Generate Static   │  │
│  │             │    │ (Pages, Nav) │    │ HTML/CSS/JS       │  │
│  └─────────────┘    └──────────────┘    └───────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        RUNTIME                                   │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────────┐  │
│  │ Static Site │    │ Client-side  │───▶│ API Calls         │  │
│  │ (CDN)       │    │ JavaScript   │    │ (Forms, Checkout) │  │
│  └─────────────┘    └──────────────┘    └───────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Project Structure (current)

```
ercee-frontend/
├── .github/
│   └── workflows/
│       └── build-deploy.yml      # CI/CD pipeline
├── public/
│   ├── favicon.svg
│   └── robots.txt
├── src/
│   ├── components/
│   │   ├── blocks/               # CMS block renderers
│   │   ├── home/                 # Home-only sections
│   │   ├── react/                # Interactive React widgets
│   │   └── ui/                   # Shared UI atoms
│   ├── layouts/
│   │   └── BaseLayout.astro
│   ├── lib/
│   │   └── api/
│   │       ├── client.ts         # fetch wrapper
│   │       ├── index.ts          # barrel exports
│   │       ├── types.ts          # shared API types
│   │       └── endpoints/        # per-endpoint mappers
│   ├── pages/
│   │   ├── index.astro
│   │   ├── [slug].astro
│   │   ├── 404.astro
│   │   ├── thank-you.astro
│   │   ├── products/
│   │   │   ├── index.astro
│   │   │   └── [id].astro
│   │   └── checkout/
│   │       └── [productId].astro
│   ├── i18n/
│   │   ├── cs.ts
│   │   ├── en.ts
│   │   └── index.ts
│   └── styles/global.css
├── .env.example
├── astro.config.mjs
├── package.json
├── tailwind.config.mjs
└── tsconfig.json
```

## CMS block mapping

CMS returns raw builder blocks from `Page::getBlocks()`. The frontend maps the data in:
`src/features/content/api/pages.ts`.

- **Mapped blocks**: `text`, `image`
- **Pass-through blocks**: any other type (e.g. `hero`, `stats`, `testimonials`, `feature_grid`)

If you add a new CMS block, update the mapping and types so Astro gets the shape it expects.

## Block components

Block renderer: `src/components/BlockRenderer.astro` + `src/shared/blocks/registry.ts`

Supported components today:

- `Text.astro`
- `Image.astro`
- `Hero.astro`
- `Stats.astro`
- `Testimonials.astro`
- `FeatureGrid.astro`

Block types and data contracts are defined in `src/shared/api/types.ts`.

## API client

The API client lives in `src/shared/api/`:

- `client.ts` - fetch wrapper with base URL
  - `client.ts` - API client setup
  - `types.ts` - shared types

### Available functions

| Function | Description | Build/Runtime |
|----------|-------------|---------------|
| `getAllPages()` | Get page slugs | Build time |
| `getPage(slug)` | Get page by slug | Build time |
| `getNavigation()` | Get navigation tree | Build time |
| `getProducts()` | Get products | Build time |
| `getProduct(id)` | Get product | Build time |
| `getForm(id)` | Get form schema | Build time |
| `submitForm(id, data)` | Submit form | Runtime |
| `initiateCheckout(productId, email)` | Start checkout | Runtime |

## Page Types

### Static Pages (Build Time)

Pages generated at build time using `getStaticPaths()`.

**Example: Dynamic CMS Pages (`[slug].astro`)**

```astro
---
import { getPage, getAllPages } from '../lib/api';

export async function getStaticPaths() {
  const slugs = await getAllPages();
  return slugs.map((slug) => ({ params: { slug } }));
}

const { slug } = Astro.params;
const page = await getPage(slug);
---

<h1>{page.title}</h1>
```

### Client-Side Interactivity

Forms and checkout use client-side JavaScript for API calls.

**Example: Form Submission**

```html
<script>
  const form = document.getElementById('my-form');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const response = await fetch(`${API_BASE_URL}/forms/1/submit`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: '...' }),
    });
    // Handle response...
  });
</script>
```

## Adding New Features

### Adding a New Block Type

1. **CMS side (Ercee-cms):**
   - Add a block constant in `app/Domain/Content/Page.php`.
   - Create a block class in `app/Filament/Blocks/`.
   - Clear the block cache: `php artisan blocks:clear`.

2. **Astro types:**
   - Add the data shape in `src/shared/api/types.ts`.

3. **API mapping:**
   - Map raw CMS data in `src/features/content/api/pages.ts`.

4. **Rendering:**
   - Create component in `src/features/<domain>/blocks/`.
   - Register it in `src/shared/blocks/registry.ts`.

### Adding a New Page

1. **Static page:** Create `src/pages/my-page.astro`
2. **Dynamic route:** Create `src/pages/[param].astro` with `getStaticPaths()`

## Styling Guidelines

### Tailwind CSS Classes

The project uses Tailwind CSS. Common patterns:

```html
<!-- Container -->
<div class="container mx-auto px-4">

<!-- Prose for rich content -->
<div class="prose prose-lg max-w-none">

<!-- Card -->
<div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">

<!-- Button primary -->
<button class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">

<!-- Button outline -->
<button class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
```

### Color Palette

Primary colors defined in `tailwind.config.mjs`:

```javascript
primary: {
  50: '#f0f9ff',
  500: '#0ea5e9',
  600: '#0284c7',
  700: '#0369a1',
}
```

## Deployment

### GitHub Actions Workflow

The site is automatically deployed when:

1. **Push to main** - Direct code changes
2. **Repository dispatch** - CMS content changes
3. **Manual trigger** - Workflow dispatch

### Deploy Targets

Configure `DEPLOY_TARGET` variable:

- `cloudflare` - Cloudflare Pages
- `vercel` - Vercel

### Required Secrets

| Secret | Description |
|--------|-------------|
| `API_BASE_URL` | Laravel API URL |
| `SITE_URL` | Public site URL |
| `CLOUDFLARE_API_TOKEN` | Cloudflare API token |
| `CLOUDFLARE_ACCOUNT_ID` | Cloudflare account ID |
| `VERCEL_TOKEN` | Vercel API token |
| `VERCEL_ORG_ID` | Vercel organization ID |
| `VERCEL_PROJECT_ID` | Vercel project ID |

## Troubleshooting

### Build Fails with API Error

**Problem:** Build fails because API is unreachable.

**Solution:**
1. Ensure Laravel API is running
2. Check `API_BASE_URL` is correct
3. Verify CORS allows the build server

### Pages Not Updating

**Problem:** Content changes don't appear on site.

**Solution:**
1. Check GitHub Actions workflow ran
2. Verify `GITHUB_TOKEN` has `repo` scope
3. Check Laravel queue worker is running
4. Look at `TriggerFrontendRebuildJob` logs

### Form Submission Fails

**Problem:** Form returns CORS error.

**Solution:**
1. Add frontend domain to `CORS_ALLOWED_ORIGINS`
2. Clear Laravel config cache: `php artisan config:clear`

## LLM Development Instructions

When working with this codebase as an LLM assistant:

### Do:

- Follow existing patterns for new components
- Use TypeScript types from `api.ts`
- Keep components simple and focused
- Use Tailwind CSS for styling
- Handle loading and error states
- Test with `npm run build` before committing

### Don't:

- Add client-side JavaScript unless necessary
- Create new API endpoints without updating CMS
- Modify build configuration without understanding impact
- Use inline styles (use Tailwind classes)
- Commit `.env` files

### Common Tasks:

1. **Add new block type:** Follow "Adding a New Block Type" section
2. **Modify styling:** Edit Tailwind classes or `tailwind.config.mjs`
3. **Add new page:** Create file in `src/pages/`
4. **Update API client:** Modify `src/lib/api.ts` with types
5. **Fix build errors:** Check `npm run build` output

### Code Quality:

```bash
# Type check
npm run astro check

# Build test
npm run build

# Preview locally
npm run preview
```
