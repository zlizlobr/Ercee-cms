# Forms

## GET /api/v1/forms/{id}
Fetch an active form definition by ID.

### Authorization
Required: `Authorization: Bearer <token>` (uses `API_PUBLIC_TOKEN`).

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| id | integer | yes | Form ID | - |

### Successful response
```json
{
  "data": {
    "id": 7,
    "name": "Newsletter",
    "schema": [
      {
        "name": "first_name",
        "label": "First name",
        "type": "text",
        "required": true
      },
      {
        "name": "interests",
        "label": "Interests",
        "type": "select",
        "required": false,
        "options": [
          {"label": "Marketing", "value": "marketing"},
          {"label": "Product", "value": "product"}
        ]
      }
    ],
    "data_options": {
      "submit_button_text": "Send",
      "success_title": "Thank you",
      "success_message": "We will get back to you shortly."
    },
    "submit_button_text": "Send",
    "success_title": "Thank you",
    "success_message": "We will get back to you shortly."
  }
}
```

### Error responses
- `404 Not Found` - form not found or inactive
```json
{
  "error": "Form not found"
}
```

## POST /api/v1/forms/{id}/submit
Submit a form response.

### Authorization
None. This is the only `/api/v1/*` endpoint that does **not** require bearer token authentication.

### Headers
- `X-Form-Source` (optional) - submission source label; defaults to `form:{id}`.
- `Idempotency-Key` (optional) - idempotency key for safe retries.

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| id | integer | yes | Form ID | - |

### Body parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| email | string | no | Subscriber email address | - |
| _hp_field | string | no | Honeypot field; if filled, request is treated as spam | - |
| <field_name> | string | no | Form fields defined in the form schema | - |

### Successful response (created)
```json
{
  "data": {
    "contract_id": 123
  }
}
```

### Successful response (honeypot triggered)
```json
{
  "message": "Thank you for your submission."
}
```

### Error responses
- `404 Not Found` - form not found or inactive
```json
{
  "error": "Form not found"
}
```

- `422 Unprocessable Entity` - validation failed
```json
{
  "error": "Validation failed",
  "errors": {
    "email": ["Please provide a valid email address."],
    "first_name": ["The first name field is required."]
  }
}
```

- `429 Too Many Requests` - rate limit exceeded (5 requests per minute per IP)

### Notes
- When an idempotency key is replayed, the response includes `X-Idempotent-Replay: true`.
