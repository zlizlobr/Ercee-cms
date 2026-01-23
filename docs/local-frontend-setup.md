# Local Frontend Development Setup

This guide explains how to set up and test the Astro frontend locally alongside the Laravel CMS.

## Prerequisites

- Node.js 20+
- npm or pnpm
- Laravel CMS running locally (see docs/local-backend-setup.md)

## Quick Start

### 1. Clone the Frontend Repository

```bash
cd /usr/local/var/www
git clone https://github.com/zlizlobr/ercee-frontend.git
cd ercee-frontend
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Configure Environment

```bash
cp .env.example .env
```

Edit `.env`:

```env
# Point to your local Laravel API
API_BASE_URL=http://localhost:8000/api/v1

# Local site URL
SITE_URL=http://localhost:4321
```

### 4. Start Development Server

Start the Laravel API and queue worker first (see docs/local-backend-setup.md).

**Astro Frontend:**

```bash
cd /usr/local/var/www/ercee-frontend
npm run dev
```

### 5. Access Applications

| Application | URL |
|-------------|-----|
| Astro Frontend | http://localhost:4321 |
| Laravel API | http://localhost:8000/api/v1 |
| Filament Admin | http://localhost:8000/admin |

## Development Workflow

### Making Content Changes

1. Edit content in Filament Admin (http://localhost:8000/admin)
2. Astro dev server auto-refreshes on file changes
3. For API data changes, restart Astro dev server or rebuild

### Testing Build

```bash
# Build static site
npm run build

# Preview built site
npm run preview
```

### Checking Types

```bash
npm run astro check
```

## Directory Structure

Keep both repositories side by side:

```
/usr/local/var/www/
├── Ercee-cms/           # Laravel CMS (this repo)
│   ├── app/
│   ├── config/
│   ├── routes/
│   └── ...
└── ercee-frontend/      # Astro Frontend
    ├── src/
    ├── public/
    └── ...
```

## CORS Configuration

For local development, configure Laravel CORS in `.env`:

```env
CORS_ALLOWED_ORIGINS=http://localhost:4321,http://127.0.0.1:4321
```

Or allow all origins (development only):

```env
CORS_ALLOWED_ORIGINS=*
```

## Testing Forms and Checkout

### Form Submission

1. Create a form in Filament Admin
2. Embed it on a page using Form Embed block
3. Visit the page on Astro frontend
4. Submit the form
5. Check Contracts in Filament Admin

### Checkout Flow

1. Create a product in Filament Admin
2. Configure Stripe in Laravel `.env`:
   ```env
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```
3. Visit `/products` on Astro frontend
4. Click product → Checkout
5. Complete Stripe test payment

### Stripe Webhook Testing

```bash
# Install Stripe CLI
brew install stripe/stripe-cli/stripe

# Login
stripe login

# Forward webhooks to local Laravel
stripe listen --forward-to localhost:8000/api/webhooks/stripe
```

## Common Issues

### API Connection Failed

**Symptoms:** Pages show "Content coming soon" or forms don't load.

**Solutions:**

1. Verify Laravel is running:
   ```bash
   curl http://localhost:8000/api/v1/pages
   ```

2. Check API_BASE_URL in `.env`:
   ```env
   API_BASE_URL=http://localhost:8000/api/v1
   ```

3. Restart Astro dev server after changing `.env`

### CORS Errors

**Symptoms:** Browser console shows CORS errors.

**Solutions:**

1. Add frontend URL to Laravel `CORS_ALLOWED_ORIGINS`
2. Clear Laravel config cache:
   ```bash
   php artisan config:clear
   ```

### Build Errors

**Symptoms:** `npm run build` fails.

**Solutions:**

1. Ensure Laravel API is running during build
2. Check for TypeScript errors: `npm run astro check`
3. Verify all page slugs exist in CMS

### Static Paths Error

**Symptoms:** Build fails with "getStaticPaths() returned empty array"

**Solutions:**

1. Create at least one published page in CMS
2. Check API response:
   ```bash
   curl http://localhost:8000/api/v1/pages
   ```

## Advanced: Testing GitHub Actions Locally

### Using act

```bash
# Install act
brew install act

# Run build workflow
act -j build -s API_BASE_URL=http://host.docker.internal:8000/api/v1

# Note: Requires Docker and Laravel running on host
```

### Manual Build Test

```bash
# Set environment
export API_BASE_URL=http://localhost:8000/api/v1
export SITE_URL=http://localhost:4321

# Build
npm run build

# Check output
ls -la dist/
```

## Testing Rebuild Webhook

### Simulate CMS Trigger

```bash
# Create a page in Filament Admin
# The PageObserver will dispatch TriggerFrontendRebuildJob

# Check job was dispatched
php artisan queue:work --once
```

### Manual API Test

```bash
# Generate a token
php artisan tinker
>>> Str::random(32)

# Add to .env
FRONTEND_REBUILD_TOKEN=your_generated_token

# Test endpoint
curl -X POST http://localhost:8000/api/internal/rebuild-frontend \
  -H "X-Rebuild-Token: your_generated_token" \
  -H "Content-Type: application/json" \
  -d '{"reason": "test"}'
```

## IDE Setup

### VS Code Extensions

Recommended extensions for Astro development:

- **Astro** - Official Astro support
- **Tailwind CSS IntelliSense** - Tailwind autocomplete
- **ESLint** - JavaScript linting
- **Prettier** - Code formatting

### VS Code Settings

Add to `.vscode/settings.json`:

```json
{
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "[astro]": {
    "editor.defaultFormatter": "astro-build.astro-vscode"
  },
  "tailwindCSS.includeLanguages": {
    "astro": "html"
  }
}
```

## Performance Testing

### Build Performance

```bash
# Time the build
time npm run build

# Expected: < 30 seconds for small sites
```

### Lighthouse Audit

```bash
# Build and preview
npm run build
npm run preview

# Run Lighthouse in Chrome DevTools
# Target scores: Performance 90+, SEO 90+
```

## Deployment Preview

Before deploying to production:

1. **Build locally:**
   ```bash
   npm run build
   ```

2. **Preview built site:**
   ```bash
   npm run preview
   ```

3. **Check all pages:**
   - Homepage loads
   - CMS pages render blocks
   - Products display correctly
   - Forms submit successfully
   - Checkout redirects to Stripe

4. **Verify no console errors**

5. **Test mobile responsiveness**
