# Overview

Ercee CMS exposes a public content and commerce API, plus internal and webhook endpoints.

- Base URL: `https://<your-domain>/api`
- Version: `v1` (public endpoints are versioned under `/v1`)
- Data format: JSON for public and internal endpoints; webhooks return plain text bodies
- Authentication:
  - Public (`/v1`): no authentication
  - Webhooks (`/webhooks/*`): IP whitelist (if configured) + Stripe signature header
  - Internal (`/internal/*`): static token header (`X-Rebuild-Token`)
- Content-Type: `application/json`
