# Overview

Ercee CMS exposes a public content and commerce API, plus internal and webhook endpoints.

- Base URL: `https://<your-domain>/api`
- Version: `v1` (public endpoints are versioned under `/v1`)
- Data format: JSON for public and internal endpoints; webhooks return plain text bodies
- Authentication:
  - Public (`/v1`): **Bearer token required** (`Authorization: Bearer <token>`)
    - Exception: `POST /v1/forms/{id}/submit` (no auth required)
    - See [Authentication](./authentication.md) for setup instructions
  - Webhooks (`/webhooks/*`): IP whitelist (if configured) + Stripe signature header
  - Internal (`/internal/*`): API token (`Authorization: Bearer <token>` or `X-Api-Token`)
- Content-Type: `application/json`
