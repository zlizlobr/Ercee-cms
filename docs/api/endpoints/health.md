# Health

## GET /api/health
System health check endpoint.

### Authorization
None.

### Request parameters
None.

### Successful response
```json
{
  "status": "ok",
  "checks": {
    "database": true,
    "cache": true
  },
  "modules": {
    "forms": "1.0.0",
    "commerce": "1.0.0",
    "funnel": "1.0.0"
  },
  "php": "8.3.0",
  "laravel": "12.0.0"
}
```

### Degraded response (503)
```json
{
  "status": "degraded",
  "checks": {
    "database": false,
    "cache": true
  },
  "modules": {
    "forms": "1.0.0",
    "commerce": "1.0.0",
    "funnel": "1.0.0"
  },
  "php": "8.3.0",
  "laravel": "12.0.0"
}
```

### Response fields
| field | type | description |
| --- | --- | --- |
| status | string | `ok` or `degraded` |
| checks.database | boolean | Database connectivity |
| checks.cache | boolean | Cache driver connectivity |
| modules | object | Loaded modules with version strings |
| php | string | PHP version |
| laravel | string | Laravel framework version |
