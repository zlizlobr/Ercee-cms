# Products

## GET /api/v1/products
List all active products with optional filtering.

### Authorization
None.

### Query parameters
| name | type | required | description | example |
| --- | --- | --- | --- | --- |
| type | string | no | Filter by product type | `simple`, `virtual`, `variable` |
| category | string/array | no | Filter by category slug(s) | `electronics` or `electronics,clothing` |
| tag | string/array | no | Filter by tag slug(s) | `sale` |
| brand | string/array | no | Filter by brand slug(s) | `apple` |

### Successful response
```json
{
  "data": [
    {
      "id": 10,
      "name": "Premium T-Shirt",
      "slug": "premium-t-shirt",
      "type": "variable",
      "short_description": "High-quality cotton t-shirt available in multiple colors.",
      "price": 499.00,
      "price_formatted": "499.00 CZK",
      "image": "https://example.com/storage/products/thumbnails/tshirt.jpg",
      "categories": [
        {
          "id": 1,
          "name": "Clothing",
          "slug": "clothing"
        }
      ],
      "tags": [
        {
          "id": 5,
          "name": "Sale",
          "slug": "sale"
        }
      ],
      "brands": [
        {
          "id": 2,
          "name": "Premium Brand",
          "slug": "premium-brand"
        }
      ]
    }
  ]
}
```

### Product types
| type | description |
| --- | --- |
| `simple` | Standard product with single price |
| `virtual` | Digital/downloadable product |
| `variable` | Product with variants (size, color, etc.) |

### Error responses
None.

---

## GET /api/v1/products/{id}
Fetch a single active product by ID with full details.

### Authorization
None.

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| id | integer | yes | Product ID | - |

### Successful response

#### Simple/Virtual product
```json
{
  "data": {
    "id": 10,
    "name": "Digital Course",
    "slug": "digital-course",
    "type": "virtual",
    "short_description": "Learn web development in 30 days.",
    "description": "<p>Full HTML description with <strong>formatting</strong>.</p>",
    "price": 1999.00,
    "price_formatted": "1,999.00 CZK",
    "image": "https://example.com/storage/products/thumbnails/course.jpg",
    "gallery": [
      "https://example.com/storage/products/gallery/course-1.jpg",
      "https://example.com/storage/products/gallery/course-2.jpg"
    ],
    "categories": [
      {
        "id": 3,
        "name": "Courses",
        "slug": "courses"
      }
    ],
    "tags": [],
    "brands": [],
    "attributes": [
      {
        "name": "Duration",
        "values": ["30 days"]
      },
      {
        "name": "Level",
        "values": ["Beginner", "Intermediate"]
      }
    ],
    "seo": {
      "title": "Digital Course | Learn Web Development",
      "description": "Master web development with our comprehensive course.",
      "og_title": "Digital Course",
      "og_description": "Learn web development",
      "og_image": "products/og/course.jpg"
    },
    "reviews": {
      "count": 5,
      "average_rating": 4.6,
      "items": [
        {
          "id": 1,
          "author": "John Doe",
          "rating": 5,
          "content": "Excellent course!",
          "created_at": "2026-01-20T10:30:00+00:00"
        }
      ]
    }
  }
}
```

#### Variable product (with variants)
```json
{
  "data": {
    "id": 15,
    "name": "Premium T-Shirt",
    "slug": "premium-t-shirt",
    "type": "variable",
    "short_description": "High-quality cotton t-shirt.",
    "description": "<p>100% cotton premium quality t-shirt.</p>",
    "price": 0,
    "price_formatted": "0.00 CZK",
    "price_range": "399.00 CZK - 599.00 CZK",
    "image": "https://example.com/storage/products/thumbnails/tshirt.jpg",
    "gallery": [],
    "categories": [
      {
        "id": 1,
        "name": "Clothing",
        "slug": "clothing"
      }
    ],
    "tags": [],
    "brands": [],
    "attributes": [
      {
        "name": "Color",
        "values": ["Red", "Blue", "Black"]
      },
      {
        "name": "Size",
        "values": ["S", "M", "L", "XL"]
      }
    ],
    "variants": [
      {
        "id": 1,
        "sku": "TSHIRT-RED-S",
        "price": 399.00,
        "price_formatted": "399.00 CZK",
        "stock": 10,
        "in_stock": true,
        "attributes": [
          {
            "attribute": "Color",
            "value": "Red"
          },
          {
            "attribute": "Size",
            "value": "S"
          }
        ]
      },
      {
        "id": 2,
        "sku": "TSHIRT-BLUE-M",
        "price": 449.00,
        "price_formatted": "449.00 CZK",
        "stock": 5,
        "in_stock": true,
        "attributes": [
          {
            "attribute": "Color",
            "value": "Blue"
          },
          {
            "attribute": "Size",
            "value": "M"
          }
        ]
      }
    ],
    "seo": null
  }
}
```

### Response fields

| field | type | description |
| --- | --- | --- |
| id | integer | Product ID |
| name | string | Product name |
| slug | string | URL-friendly identifier |
| type | string | Product type: `simple`, `virtual`, `variable` |
| short_description | string\|null | Brief product description (plain text) |
| description | string\|null | Full product description (HTML) |
| price | float | Product price (0 for variable products) |
| price_formatted | string | Formatted price with currency |
| price_range | string | Price range for variable products only |
| image | string\|null | Main product image URL |
| gallery | array | Array of gallery image URLs |
| categories | array | Product categories |
| tags | array | Product tags |
| brands | array | Product brands |
| attributes | array | Product attributes with values |
| variants | array | Product variants (variable products only) |
| seo | object\|null | SEO metadata |
| reviews | object | Reviews with count, average, and items (if any) |

### Error responses
- `404 Not Found` - product not found or inactive
```json
{
  "error": "Product not found"
}
```
