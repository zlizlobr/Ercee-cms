# Authentication

## Public endpoints
Endpoints under `/v1` are public and do not require authentication.

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
