# Navigation & Menus

## GET /api/v1/navigation
Get navigation items for the default menu (`main`).

### Authorization
Required: `Authorization: Bearer <token>` (uses `API_PUBLIC_TOKEN`).

### Request parameters
None.

### Successful response
```json
{
  "data": [
    {
      "id": 1,
      "title": "Kontakt",
      "slug": "",
      "url": "#contact",
      "page_slug": null,
      "target": "_self",
      "children": []
    }
  ]
}
```

### Response schema
| field | type | description |
| --- | --- | --- |
| id | integer | Navigation item ID |
| title | string | Display title |
| slug | string | Item slug (can be empty) |
| url | string\|null | URL or anchor link, `null` if not set |
| page_slug | string\|null | Page slug when item links to a page (use with `GET /api/v1/pages/{slug}`), `null` otherwise |
| target | string | Link target (`_self`, `_blank`) |
| children | array | Nested navigation items (same structure) |

### Error responses
None. If the menu does not exist, the endpoint returns an empty array in `data`.

## GET /api/v1/navigation/{menuSlug}
Get navigation items for a specific menu by slug.

### Authorization
Required: `Authorization: Bearer <token>` (uses `API_PUBLIC_TOKEN`).

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| menuSlug | string | yes | Menu slug | - |

### Successful response
Same structure as `GET /api/v1/navigation`.

### Error responses
None. If the menu does not exist, the endpoint returns an empty array in `data`.

## GET /api/v1/menus/{menuSlug}
Get a menu (including its navigation items) by slug.

### Authorization
Required: `Authorization: Bearer <token>` (uses `API_PUBLIC_TOKEN`).

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| menuSlug | string | yes | Menu slug | - |

### Successful response
```json
{
  "data": {
    "id": 1,
    "name": "Main Navigation",
    "slug": "main",
    "items": [
      {
        "id": 1,
        "title": "Kontakt",
        "slug": "",
        "url": "#contact",
        "page_slug": null,
        "target": "_self",
        "children": []
      }
    ]
  }
}
```

### Response schema
| field | type | description |
| --- | --- | --- |
| id | integer | Menu ID |
| name | string | Menu name |
| slug | string | Menu slug |
| items | array | Navigation items (same schema as `GET /api/v1/navigation` items) |

### Error responses
- `404 Not Found` - menu not found
```json
{
  "error": "Menu not found"
}
```
