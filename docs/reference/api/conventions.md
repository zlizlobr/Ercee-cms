# Conventions

## HTTP Methods
- GET - read
- POST - create or action
- PUT - full replace
- PATCH - partial update
- DELETE - delete

## Naming
- snake_case for fields
- plural resource names (pages, products)
- ISO 8601 timestamps (e.g., `2024-01-15T12:34:56Z`)

## Headers
- `Content-Type: application/json`
- `Accept: application/json`
- `Stripe-Signature` (required for `/webhooks/stripe`)
- `X-Form-Source` (optional for form submissions)
- `Authorization: Bearer <token>` or `X-Api-Token` (required for `/internal/*`)
- `Idempotency-Key` (optional for safe retries on POST endpoints)
