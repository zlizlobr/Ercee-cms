# Astro Frontend Development Guide

This guide explains the architecture and development workflow for the Ercee public frontend built with Astro.

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

### Key Concepts

1. **Static Site Generation (SSG)** - Pages are pre-built at build time
2. **API Data Fetching** - Content fetched from Laravel API during build
3. **Client-side Interactivity** - Forms and checkout use client-side JS
4. **Automatic Rebuilds** - CMS changes trigger GitHub Actions rebuild

## Project Structure

```
ercee-frontend/
├── .github/
│   └── workflows/
│       └── build-deploy.yml      # CI/CD pipeline
├── public/
│   ├── favicon.svg               # Site favicon
│   └── robots.txt                # SEO robots file
├── src/
│   ├── components/
│   │   ├── blocks/               # CMS block renderers
│   │   │   ├── Text.astro        # Rich text content
│   │   │   ├── Image.astro       # Image with caption
│   │   │   ├── CTA.astro         # Call-to-action section
│   │   │   └── FormEmbed.astro   # Embedded form
│   │   ├── BlockRenderer.astro   # Block type dispatcher
│   │   └── Navigation.astro      # Site navigation
│   ├── layouts/
│   │   └── BaseLayout.astro      # Main HTML layout
│   ├── lib/
│   │   └── api.ts                # API client (typed)
│   ├── pages/
│   │   ├── index.astro           # Homepage
│   │   ├── [slug].astro          # Dynamic CMS pages
│   │   ├── 404.astro             # Not found page
│   │   ├── thank-you.astro       # Post-payment page
│   │   ├── products/
│   │   │   ├── index.astro       # Product listing
│   │   │   └── [id].astro        # Product detail
│   │   └── checkout/
│   │       └── [productId].astro # Checkout form
│   └── env.d.ts                  # TypeScript env types
├── .env.example                  # Environment template
├── astro.config.mjs              # Astro configuration
├── package.json                  # Dependencies
├── tailwind.config.mjs           # Tailwind CSS config
└── tsconfig.json                 # TypeScript config
```

## Component Reference

### Block Components

Block components render CMS content blocks. Each block type has its own component.

#### Text Block (`src/components/blocks/Text.astro`)

Renders rich HTML content.

```astro
---
import type { TextBlockData } from '../../lib/api';

interface Props {
  data: TextBlockData;
}

const { data } = Astro.props;
---

<div class="prose prose-lg max-w-none">
  <Fragment set:html={data.content} />
</div>
```

**API Data:**
```json
{
  "type": "text",
  "data": {
    "content": "<p>HTML content here...</p>"
  }
}
```

#### Image Block (`src/components/blocks/Image.astro`)

Renders image with optional caption.

**API Data:**
```json
{
  "type": "image",
  "data": {
    "url": "https://example.com/image.jpg",
    "alt": "Image description",
    "caption": "Optional caption"
  }
}
```

#### CTA Block (`src/components/blocks/CTA.astro`)

Renders call-to-action section with button.

**API Data:**
```json
{
  "type": "cta",
  "data": {
    "title": "Get Started",
    "description": "Optional description text",
    "button_text": "Sign Up",
    "button_url": "/signup"
  }
}
```

#### Form Embed Block (`src/components/blocks/FormEmbed.astro`)

Renders embedded form with client-side submission.

**API Data:**
```json
{
  "type": "form_embed",
  "data": {
    "form_id": 1
  }
}
```

### BlockRenderer Component

Dispatches blocks to appropriate components based on type.

```astro
---
import type { Block } from '../lib/api';
import Text from './blocks/Text.astro';
import Image from './blocks/Image.astro';
import CTA from './blocks/CTA.astro';
import FormEmbed from './blocks/FormEmbed.astro';

interface Props {
  blocks: Block[];
}

const { blocks } = Astro.props;
---

<div class="space-y-8">
  {blocks.map((block) => {
    switch (block.type) {
      case 'text':
        return <Text data={block.data} />;
      case 'image':
        return <Image data={block.data} />;
      case 'cta':
        return <CTA data={block.data} />;
      case 'form_embed':
        return <FormEmbed data={block.data} />;
      default:
        return null;
    }
  })}
</div>
```

## API Client

The API client (`src/lib/api.ts`) provides typed functions for fetching data.

### Available Functions

| Function | Description | Build/Runtime |
|----------|-------------|---------------|
| `getAllPages()` | Get all page slugs | Build time |
| `getPage(slug)` | Get page by slug | Build time |
| `getNavigation()` | Get navigation tree | Build time |
| `getProducts()` | Get all products | Build time |
| `getProduct(id)` | Get product by ID | Build time |
| `getForm(id)` | Get form schema | Build time |
| `submitForm(id, data)` | Submit form | Runtime (client) |
| `initiateCheckout(productId, email)` | Start checkout | Runtime (client) |

### Type Definitions

```typescript
interface Page {
  id: number;
  slug: string;
  title: string;
  blocks: Block[];
  seo: SeoMeta | null;
  published_at: string | null;
}

interface Block {
  type: 'text' | 'image' | 'cta' | 'form_embed';
  position: number;
  data: TextBlockData | ImageBlockData | CtaBlockData | FormEmbedBlockData;
}

interface NavigationItem {
  id: number;
  label: string;
  url: string;
  target?: string;
  children?: NavigationItem[];
}

interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  currency: string;
  image_url?: string;
  is_active: boolean;
}
```

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

1. **Define type in `api.ts`:**

```typescript
export interface VideoBlockData {
  video_url: string;
  autoplay: boolean;
}

export interface Block {
  type: 'text' | 'image' | 'cta' | 'form_embed' | 'video'; // Add new type
  // ...
}
```

2. **Create component:**

```astro
<!-- src/components/blocks/Video.astro -->
---
import type { VideoBlockData } from '../../lib/api';

interface Props {
  data: VideoBlockData;
}

const { data } = Astro.props;
---

<div class="aspect-video">
  <iframe src={data.video_url} allowfullscreen></iframe>
</div>
```

3. **Add to BlockRenderer:**

```astro
import Video from './blocks/Video.astro';

// In switch statement:
case 'video':
  return <Video data={block.data} />;
```

4. **Add block type in Laravel CMS:**

Update `Page::blockTypes()` and Filament form schema.

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
