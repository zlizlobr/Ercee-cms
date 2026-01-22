# Products

## GET /v1/products
List all active products.

### Authorization
None.

### Request parameters
None.

### Successful response
```json
{
  "data": [
    {
      "id": 10,
      "name": "Starter Plan",
      "price": 9900,
      "price_formatted": "99.00 CZK"
    }
  ]
}
```

`price` is stored in the smallest currency unit (e.g., cents).

### Error responses
None.

## GET /v1/products/{id}
Fetch a single active product by ID.

### Authorization
None.

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| id | integer | yes | Product ID | - |

### Successful response
```json
{
  "data": {
    "id": 10,
    "name": "Starter Plan",
    "price": 9900,
    "price_formatted": "99.00 CZK"
  }
}
```

### Error responses
- `404 Not Found` - product not found or inactive
```json
{
  "error": "Product not found"
}
```
