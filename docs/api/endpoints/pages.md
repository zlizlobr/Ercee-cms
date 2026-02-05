# Pages

## GET /api/v1/pages
List all published page slugs.

### Authorization
None.

### Request parameters
None.

### Successful response
```json
{
  "data": ["about", "contact"]
}
```

### Error responses
None.

## GET /api/v1/pages/{slug}
Fetch a published page by slug.

### Authorization
None.

### Path parameters
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| slug | string | yes | Page slug | - |

### Successful response
```json
{
  "data": {
    "id": 12,
    "slug": "about",
    "title": {
      "en": "About Us",
      "cs": "O nas"
    },
    "blocks": [
      {
        "type": "text",
        "position": 0,
        "data": {
          "heading": "Welcome",
          "body": "<p>Intro text</p>"
        }
      }
    ],
    "seo": {
      "title": "About Us",
      "description": "Learn more about us",
      "open_graph": {
        "title": "About Us",
        "description": "Learn more about us",
        "image": "pages/og/about.png"
      }
    },
    "published_at": "2024-01-15T12:34:56Z"
  }
}
```

`title` can be either a localized object (as shown) or a plain string, depending on how the page was stored.

### Block object
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| type | string | yes | Block type: `text`, `image`, `form_embed` | - |
| position | integer | no | Order index used for sorting | - |
| data | object | no | Block data payload | - |

### Block data fields
- `text`: `heading` (string), `body` (string, HTML)
- `image`: `image` (string), `alt` (string), `caption` (string)
- `form_embed`: `form_id` (string), `title` (string), `description` (string)

### Error responses
- `404 Not Found` - page not found
```json
{
  "error": "Page not found"
}
```
