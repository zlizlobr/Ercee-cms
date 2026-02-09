# API Token Setup Guide

This guide explains how to configure the API public token for securing `/api/v1/*` endpoints.

## Overview

As of the latest security update, all `/api/v1/*` endpoints require bearer token authentication, except for:
- `POST /api/v1/forms/{id}/submit` (public form submission endpoint)

This token is used by your frontend application to authenticate API requests during build-time or server-side rendering.

## Security Model

- **Static bearer token**: A single shared secret between backend and frontend
- **Build-time authentication**: Token is used during frontend build process (SSG/SSR)
- **Not for client-side**: Token should never be exposed to browser clients
- **IP allowlisting**: Additional layer via infrastructure (Nginx/Cloudflare) planned for production

---

## Step 1: Generate a Strong Token

Use one of these methods to generate a secure random token:

### Option A: Using OpenSSL
```bash
openssl rand -base64 32
```

### Option B: Using PHP (in backend directory)
```bash
cd /path/to/ercee-cms
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

### Option C: Using Node.js (in frontend directory)
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('base64'))"
```

**Example output:**
```
dGhpc2lzYXJhbmRvbXRva2VuZm9ydGVzdGluZ3B1cnBvc2VzMTIzNDU2Nzg5MA==
```

⚠️ **Important**: Save this token securely. You'll need it for both backend and frontend configuration.

---

## Step 2: Configure Backend

### Local Development

1. Open `.env` file in your backend project:
   ```bash
   cd /path/to/ercee-cms
   nano .env
   ```

2. Add these lines (or update if they exist):
   ```env
   # API Authentication
   API_PUBLIC_TOKEN=paste_your_generated_token_here
   API_PUBLIC_TOKEN_NAME=public-api
   ```

3. Save and clear cache:
   ```bash
   php artisan cache:clear
   ```

### Production / CI/CD

1. **DO NOT commit the token to git** - it's already in `.gitignore`

2. Set the token as an environment variable in your deployment platform:
   - **GitHub Actions**: Repository Settings → Secrets → New secret
   - **GitLab CI**: Settings → CI/CD → Variables
   - **Vercel/Netlify**: Project Settings → Environment Variables
   - **Docker**: Pass via `docker run -e API_PUBLIC_TOKEN=...`

3. Example GitHub Actions secret setup:
   ```yaml
   # .github/workflows/deploy.yml
   env:
     API_PUBLIC_TOKEN: ${{ secrets.API_PUBLIC_TOKEN }}
   ```

---

## Step 3: Configure Frontend

### Local Development

1. Open `.env` file in your frontend project:
   ```bash
   cd /path/to/ercee-frontend
   nano .env
   ```

2. Add the **same token** you used for backend:
   ```env
   # API Configuration
   API_BASE_URL=http://localhost:8000/api/v1

   # API Authentication (Bearer token for /api/v1/* endpoints)
   # This token must match API_PUBLIC_TOKEN on the backend
   API_PUBLIC_TOKEN=paste_your_generated_token_here
   ```

3. **Important for Astro projects**: Ensure `astro.config.mjs` includes vite define config:
   ```js
   export default defineConfig({
     // ... other config
     vite: {
       define: {
         'import.meta.env.API_BASE_URL': JSON.stringify(process.env.API_BASE_URL),
         'import.meta.env.API_PUBLIC_TOKEN': JSON.stringify(process.env.API_PUBLIC_TOKEN),
       },
     },
   });
   ```

4. Restart your frontend dev server:
   ```bash
   npm run dev
   ```

### Production Build

1. Set the environment variable in your build configuration:

   **Vercel**:
   ```
   Project Settings → Environment Variables
   API_PUBLIC_TOKEN = your_token_here
   ```

   **Netlify**:
   ```
   Site Settings → Build & Deploy → Environment
   API_PUBLIC_TOKEN = your_token_here
   ```

   **GitHub Actions**:
   ```yaml
   - name: Build frontend
     env:
       API_PUBLIC_TOKEN: ${{ secrets.API_PUBLIC_TOKEN }}
     run: npm run build
   ```

2. Ensure the token is available during **build time**, not just runtime (for SSG/ISR)

---

## Step 4: Verify Configuration

### Test Backend

1. **Without token** (should return 401):
   ```bash
   curl http://localhost:8000/api/v1/pages
   ```
   Expected response:
   ```json
   {"error": "Unauthorized"}
   ```

2. **With correct token** (should work):
   ```bash
   curl -H "Authorization: Bearer your_token_here" \
        http://localhost:8000/api/v1/pages
   ```
   Expected: JSON response with page data

3. **Form submission without token** (should work):
   ```bash
   curl -X POST http://localhost:8000/api/v1/forms/1/submit \
        -H "Content-Type: application/json" \
        -d '{"email":"test@example.com"}'
   ```
   Expected: Success or 501 (if forms not implemented yet)

### Test Frontend

1. Start your frontend dev server:
   ```bash
   cd /path/to/ercee-frontend
   npm run dev
   ```

2. Check browser console for API errors
   - No 401 errors should appear
   - All `/api/v1/*` requests should include `Authorization: Bearer ...` header

3. Verify in Network tab:
   - Open DevTools → Network tab
   - Filter by "api/v1"
   - Click any request → Headers tab
   - Should see: `Authorization: Bearer <your_token>`

---

## Troubleshooting

### Error: "Public API token not configured"

**Problem**: Backend can't find `API_PUBLIC_TOKEN` in environment

**Solutions**:
1. Check `.env` file has the correct variable name
2. Run `php artisan cache:clear`
3. Restart your PHP server / queue workers
4. For production, verify environment variable is set in hosting platform

### Error: 401 Unauthorized

**Problem**: Token is missing or doesn't match

**Solutions**:
1. Verify token is **exactly the same** on backend and frontend (no extra spaces)
2. Check frontend `.env` has `API_PUBLIC_TOKEN` set
3. Restart frontend dev server after changing `.env`
4. For production builds, ensure token is available at build time

### Frontend requests not including token

**Problem**: API calls don't have Authorization header

**Solutions**:
1. Check `import.meta.env.API_PUBLIC_TOKEN` is available in `src/lib/api/config.ts`
2. Verify you're using the latest `fetchApi` client from `src/lib/api/client.ts`
3. Clear browser cache and rebuild: `npm run dev` or `npm run build`

### Form submission returns 401

**Problem**: Form endpoint should be public but requires token

**Solutions**:
1. Verify route in `routes/api.php` is **outside** the `middleware('api.public')` group
2. Check the endpoint exactly matches: `POST /api/v1/forms/{id}/submit`
3. Ensure backend middleware checks for form submission exception

### Error: "Public API token not configured" during Astro build

**Problem**: Astro build fails with `TypeError: Cannot read properties of null (reading 'data')` and backend returns 500 "Public API token not configured"

**Cause**: Astro doesn't automatically expose non-PUBLIC_ environment variables to `import.meta.env`

**Solution**:
1. Add vite define config to `astro.config.mjs`:
   ```js
   export default defineConfig({
     // ... other config
     vite: {
       define: {
         'import.meta.env.API_BASE_URL': JSON.stringify(process.env.API_BASE_URL),
         'import.meta.env.API_PUBLIC_TOKEN': JSON.stringify(process.env.API_PUBLIC_TOKEN),
       },
     },
   });
   ```

2. Ensure `.env` file has the token:
   ```env
   API_PUBLIC_TOKEN=your_token_here
   ```

3. Rebuild:
   ```bash
   npm run build
   ```

---

## Security Best Practices

### ✅ DO

- Generate a strong random token (at least 32 characters)
- Store token in environment variables, never in code
- Use secrets management in CI/CD (GitHub Secrets, GitLab Variables, etc.)
- Rotate token periodically (update both backend and frontend)
- Keep token confidential - treat it like a password

### ❌ DON'T

- Don't commit token to git repositories
- Don't expose token in client-side JavaScript
- Don't share token publicly or in screenshots
- Don't use weak/predictable tokens (e.g., "test123")
- Don't log the token value in application logs

---

## Additional Security (Production)

For production environments, consider additional security layers:

### 1. IP Allowlisting (Infrastructure)

Configure your reverse proxy (Nginx, Cloudflare, AWS WAF) to restrict `/api/v1/*` to specific IPs:

**Nginx example**:
```nginx
location /api/v1/ {
    # Allow frontend build server IP
    allow 203.0.113.10;
    # Allow CI/CD runner IP
    allow 198.51.100.20;
    # Deny all others
    deny all;

    proxy_pass http://backend;
}

# Keep form submission public
location /api/v1/forms/ {
    proxy_pass http://backend;
}
```

### 2. Rate Limiting

Already implemented in the application:
- `/api/v1/*` endpoints have rate limiting via middleware
- Form submission has separate rate limit (5 requests/min per IP)

### 3. CORS Configuration

Configure allowed origins in `.env`:
```env
CORS_ALLOWED_ORIGINS=https://www.example.com,https://staging.example.com
```

---

## Token Rotation

To rotate the token (recommended every 90 days):

1. Generate a new token (Step 1)
2. Update backend `.env` with new token
3. Update frontend `.env` with new token
4. For production:
   - Update secrets in CI/CD platform
   - Trigger new frontend build
   - Deploy backend with new token
5. Verify both environments are working with new token

---

## Related Documentation

- [API Authentication](../../api/authentication.md) - Overview of all authentication methods
- [API Overview](../../api/overview.md) - API structure and conventions
- [Form Endpoints](../../api/endpoints/forms.md) - Public form submission endpoint details

---

## Support

If you encounter issues not covered in this guide:

1. Check backend logs: `storage/logs/laravel.log`
2. Check frontend build logs
3. Review [API Authentication](../../api/authentication.md) documentation
4. Report issues: [GitHub Issues](https://github.com/your-org/ercee-cms/issues)
