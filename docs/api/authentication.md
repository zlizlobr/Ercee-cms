# Authentication

> **📖 Setup Guide**: See [API Token Setup Guide](../guides/setup/api-token-setup.md) for step-by-step configuration instructions.

## Public API endpoints (`/v1`)
Endpoints under `/v1` require bearer token authentication.

- Send `Authorization: Bearer <token>` in the request headers.
- The token value is configured via `services.api.public_token` (`API_PUBLIC_TOKEN`).
- Returns `401 Unauthorized` if the token is missing or invalid.
- Returns `500 Internal Server Error` if the token is not configured on the server.

### Exception: Form submissions
`POST /api/v1/forms/{id}/submit` does **not** require authentication and is publicly accessible without a bearer token. This endpoint is protected by:
- Rate limiting
- CAPTCHA validation (when implemented)

### Example authenticated request
```bash
curl -H "Authorization: Bearer your_api_public_token_here" \
     https://example.com/api/v1/pages
```

## Webhooks
`/webhooks/stripe` requires:
- IP whitelist check when `services.webhook_whitelist` is configured.
- Stripe signature verification using the `Stripe-Signature` header.

## Internal endpoints
`/internal/*` endpoints require an API token.

- Send `Authorization: Bearer <token>` or `X-Api-Token: <token>`.
- The token value is configured via `services.api.internal_token` (`API_INTERNAL_TOKEN`).
- Token abilities (scopes) can be configured via `services.api.token_abilities`.

## 401 vs 403
- `401 Unauthorized` - missing or invalid authentication token.
- `403 Forbidden` - authentication succeeded but access is blocked (e.g., missing token ability or IP not allowed).
