# API Controller Rules

This repository uses a shared, defensive pattern for API endpoints. Follow these rules to keep endpoints consistent, robust, and production-safe.

## Scope
- These rules apply to all controllers under `app/Http/Controllers/Api`.
- Do not change public URLs or HTTP methods without updating docs and clients.

## Response Contracts
- Success responses must be JSON and use the `data` envelope.
- Error responses must be JSON and use the `error` (and optional `errors`) fields.
- Do not introduce new response shapes for existing endpoints.

## GET Endpoints
- All GET handlers must use `ApiController::safeGet()` to prevent unhandled exceptions.
- Do not add ad‑hoc `try/catch` blocks inside GET methods.
- Return `404` for missing resources.
- Return `422` for invalid inputs or query state (prefer Laravel validation).
- Return `503` for database/cache/external service outages (logged via `report()`).
- Return `500` only for truly unexpected failures.

## Write Endpoints (POST/PUT/PATCH/DELETE)
- Keep idempotency where applicable (`api.idempotency` middleware).
- Validate input with Form Request classes whenever possible.
- Preserve existing response shapes and status codes.

## Error Handling
- Use Laravel-native exceptions (`ValidationException`, `ModelNotFoundException`, `HttpException`) when appropriate.
- Let `ApiExceptionHandler` render API errors; do not bypass it for API routes.
- For service outages, log/report and return `503`.

## Rate Limiting
- Apply throttling per route (not globally).
- Use dedicated limiter keys to prevent cross-endpoint exhaustion.

## Documentation
- Any endpoint change requires a matching update in `docs/api`.
- If you add an alias endpoint, document it as a legacy alias.

## Patterns to Follow
- Controllers should be thin: orchestrate services and format responses.
- Business logic belongs in services or domain classes.
- Avoid clever abstractions; prefer clarity and maintainability.
