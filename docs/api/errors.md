# Errors

## JSON error format
Most JSON endpoints return errors in the following structure:

```json
{
  "error": "Human-readable error message",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

- `error` is always present for error responses.
- `errors` is present only for validation failures.

## Response headers
- `X-Request-ID` is returned on all API responses for log correlation.
- `X-Idempotent-Replay: true` is returned when an idempotency key causes a cached response replay.

## Plain text errors
`/webhooks/stripe` returns plain text bodies for both success and error responses.

## HTTP status codes
- `400 Bad Request` - invalid Stripe signature for webhooks
- `401 Unauthorized` - missing or invalid API token
- `403 Forbidden` - IP address not allowed or insufficient token ability
- `404 Not Found` - resource not found (page, menu, product, form, payment)
- `405 Method Not Allowed` - unsupported HTTP method
- `422 Unprocessable Entity` - validation failed
- `429 Too Many Requests` - rate limit exceeded
- `500 Internal Server Error` - API authentication not configured or internal processing failure
