# Navigation & Menus

## GET /v1/navigation
Get navigation items for the default menu (`main`).

### Authorization
None.

### Request parameters
None.

### Successful response
```json
{
  "data": [
    {
      "id": 1,
      "title": "Home",
      "slug": "home",
      "url": "/",
      "target": "_self",
      "children": []
    },
    {
      "id": 2,
      "title": "Services",
      "slug": "services",
      "url": "/services",
      "target": "_self",
      "children": [
        {
          "id": 3,
          "title": "Consulting",
          "slug": "consulting",
          "url": "/services/consulting",
          "target": "_self",
          "children": []
        }
      ]
    }
  ]
}
```

### Error responses
None. If the menu does not exist, the endpoint returns an empty array in `data`.

## GET /v1/navigation/{menuSlug}
Get navigation items for a specific menu by slug.

### Authorization
None.

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| menuSlug | string | yes | Menu slug | - |

### Successful response
Same structure as `GET /v1/navigation`.

### Error responses
None. If the menu does not exist, the endpoint returns an empty array in `data`.

## GET /v1/menus/{menuSlug}
Get a menu (including its navigation items) by slug.

### Authorization
None.

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| menuSlug | string | yes | Menu slug | - |

### Successful response
```json
{
  "data": {
    "id": 4,
    "name": "Main",
    "slug": "main",
    "items": [
      {
        "id": 1,
        "title": "Home",
        "slug": "home",
        "url": "/",
        "target": "_self",
        "children": []
      }
    ]
  }
}
```

### Error responses
- `404 Not Found` - menu not found
```json
{
  "error": "Menu not found"
}
```
