# Taxonomy Mapping

## GET /api/v1/taxonomies/mapping
Returns a mapping of product taxonomy types to their term slugs for frontend routing.

### Authorization
None.

### Query parameters
None.

### Successful response
```json
{
  "data": {
    "products": {
      "category": ["clothing", "electronics"],
      "tag": ["sale", "summer"],
      "brand": ["acme"]
    }
  }
}
```

### Response rules
- Only taxonomies linked to **active** products are included.
- Slugs are sorted alphabetically.

### Error responses
None.
<<<<<<< HEAD

### Notes
- `GET /api/v1/taxonomy-mapping` is supported as a legacy alias for this endpoint.
=======
>>>>>>> origin/main
