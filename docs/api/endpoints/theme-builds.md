# Theme Builds

Async build API for creating a frontend build from a `theme.json` payload and downloading the generated `dist.zip`.

## Authentication

All endpoints require:

```
Authorization: Bearer <API_PUBLIC_TOKEN>
```

## POST /api/v1/theme-builds

Create a new build job. Payload can be the raw `theme.json` object or `{ "theme": { ... } }`.

Optional fields:
- `callback_url` (string): webhook URL invoked when the build finishes.

Example (raw theme.json):

```json
{
  "name": "Industrial Blue",
  "colors": {
    "primary": "#2563eb",
    "secondary": "#0f172a"
  },
  "gradients": {
    "hero": "linear-gradient(135deg, #2563eb 0%, #0f172a 100%)"
  }
}
```

Example (theme wrapper + callback):

```json
{
  "theme": {
    "name": "Industrial Blue",
    "colors": {
      "primary": "#2563eb",
      "secondary": "#0f172a"
    }
  },
  "callback_url": "https://example.com/webhooks/theme-build"
}
```

Response:

```json
{
  "id": 123,
  "status": "queued"
}
```

## GET /api/v1/theme-builds/{id}

Check build status.

Response:

```json
{
  "id": 123,
  "status": "running",
  "error_message": null,
  "created_at": "2026-02-09T12:34:56Z",
  "updated_at": "2026-02-09T12:35:10Z"
}
```

## GET /api/v1/theme-builds/{id}/download

Download the build zip when status is `success`.

Responses:
- `200`: Zip download
- `409`: Build still `queued` or `running`
- `422`: Build `error` (includes `error_message`)
- `404`: Build or artifact not found

## Webhook callback

If `callback_url` is supplied, the build worker will POST:

```json
{
  "id": 123,
  "status": "success",
  "download_url": "https://cms.example.com/api/v1/theme-builds/123/download",
  "error_message": null
}
```

The request includes an HMAC signature header:

```
X-Theme-Build-Signature: sha256=<hex_hmac>
```

Signature is computed over the raw JSON payload using `THEME_BUILD_WEBHOOK_SECRET`.
