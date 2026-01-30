# Endpoint Title

Short description of the endpoint purpose.

## Endpoint

`METHOD /api/v1/...`

## Authentication

- Required: yes/no
- Method: bearer token / api token / none

## Request

### Headers

- `Content-Type: application/json`

### Body

```json
{
  "example": "value"
}
```

## Response

### Success

```json
{
  "data": {}
}
```

### Errors

| Status | Meaning |
|--------|---------|
| `400` | Example |

## Notes

- Rate limiting
- Idempotency
