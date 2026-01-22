# Authentication

## Public endpoints
Endpoints under `/v1` are public and do not require authentication.

## Webhooks
`/webhooks/stripe` requires:
- IP whitelist check when `services.webhook_whitelist` is configured.
- Stripe signature verification using the `Stripe-Signature` header.

## Internal endpoints
`/internal/rebuild-frontend` requires the `X-Rebuild-Token` header.

- The token value is configured via `services.frontend.rebuild_token`.
- The token does not expire automatically; rotate it manually as needed.

## 401 vs 403
- `401 Unauthorized` - missing or invalid authentication token.
- `403 Forbidden` - authentication succeeded but access is blocked (e.g., IP not allowed).
