# Internal

## POST /internal/rebuild-frontend
Trigger a frontend rebuild via GitHub dispatch.

### Authorization
Requires `X-Rebuild-Token` header.

### Headers
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| X-Rebuild-Token | string | yes | Static rebuild token | - |

### Body parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| reason | string | no | Reason for rebuild | manual |

### Successful response
```json
{
  "message": "Frontend rebuild triggered successfully",
  "reason": "manual"
}
```

### Error responses
- `401 Unauthorized` - invalid token
```json
{
  "error": "Invalid token"
}
```

- `500 Internal Server Error` - rebuild token not configured
```json
{
  "error": "Rebuild token not configured"
}
```

- `500 Internal Server Error` - rebuild trigger failed
```json
{
  "error": "Failed to trigger rebuild",
  "message": "<error details>"
}
```
