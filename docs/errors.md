# Errors

## JSON error format
Most JSON endpoints return errors in the following structure:

```json
{
  "error": "Human-readable error message",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "message": "Optional additional message"
}
```

- `error` is always present for error responses.
- `errors` is present only for validation failures.
- `message` is present only for specific internal errors.

## Plain text errors
`/webhooks/stripe` returns plain text bodies for both success and error responses.

## HTTP status codes
- `400 Bad Request` - invalid Stripe signature for webhooks
- `401 Unauthorized` - invalid or missing `X-Rebuild-Token`
- `403 Forbidden` - IP address not allowed for webhook
- `404 Not Found` - resource not found (page, menu, product, form, payment) or webhook signature invalid
- `422 Unprocessable Entity` - validation failed
- `429 Too Many Requests` - rate limit exceeded
- `500 Internal Server Error` - rebuild token not configured or internal processing failure
