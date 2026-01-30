# Checkout

## POST /api/v1/checkout
Create a checkout session for a product.

### Authorization
None.

### Headers
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| Idempotency-Key | string | no | Optional idempotency key for safe retries | - |

### Body parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| product_id | integer | yes | Active product ID | - |
| email | string | yes | Customer email address | - |

### Successful response (created)
```json
{
  "data": {
    "order_id": 501,
    "redirect_url": "https://checkout.stripe.com/c/pay/cs_test_123"
  }
}
```

### Error responses
- `404 Not Found` - product not found or inactive
```json
{
  "error": "Product not found or inactive"
}
```

- `422 Unprocessable Entity` - validation failed
```json
{
  "error": "Validation failed",
  "errors": {
    "product_id": ["The selected product id is invalid."],
    "email": ["The email field is required."]
  }
}
```

- `429 Too Many Requests` - rate limit exceeded (10 requests per minute per IP)

### Notes
- When an idempotency key is replayed, the response includes `X-Idempotent-Replay: true`.
