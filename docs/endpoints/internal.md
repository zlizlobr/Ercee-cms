# Internal

## POST /api/internal/rebuild-frontend
Trigger a frontend rebuild via GitHub dispatch.

### Authorization
Requires API token authentication via `Authorization: Bearer <token>` or `X-Api-Token`.

### Headers
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| Authorization | string | no | `Bearer <token>` | - |
| X-Api-Token | string | no | API token (alternative to Bearer) | - |

### Body parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| reason | string | no | Reason for rebuild | manual |

### Successful response
```json
{
  "data": {
    "reason": "manual"
  }
}
```

### Error responses
- `401 Unauthorized` - missing or invalid token
```json
{
  "error": "Unauthorized"
}
```

- `403 Forbidden` - insufficient token permissions
```json
{
  "error": "Forbidden - insufficient permissions"
}
```

- `500 Internal Server Error` - API authentication not configured
```json
{
  "error": "API authentication not configured"
}
```

- `500 Internal Server Error` - rebuild trigger failed
```json
{
  "error": "Failed to trigger rebuild"
}
```

### Rate limiting
30 requests per minute per token or IP.
