# Webhooks

## POST /webhooks/stripe
Receive Stripe payment events.

### Authorization
- IP whitelist enforced when `services.webhook_whitelist` is configured.
- `Stripe-Signature` header is required.

### Headers
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| Stripe-Signature | string | yes | Stripe webhook signature | - |

### Request body
Stripe event payload (JSON).

Example (`checkout.session.completed`):
```json
{
  "id": "evt_1N0ZC6I7S3",
  "type": "checkout.session.completed",
  "data": {
    "object": {
      "id": "cs_test_abc123",
      "payment_intent": "pi_1N0ZC6I7S3",
      "customer_email": "jane@example.com",
      "amount_total": 9900
    }
  }
}
```

### Successful response
Plain text body:
```
OK
```

### Error responses
- `400 Bad Request` - invalid Stripe signature
```
Invalid signature
```

- `403 Forbidden` - IP address not allowed
```
IP address not allowed
```

- `404 Not Found` - payment not found or webhook signature invalid
```
Payment not found for transaction: <transaction_id>
```

- `500 Internal Server Error` - webhook processing failed
```
Webhook processing failed
```

### Rate limiting
100 requests per minute per IP.
