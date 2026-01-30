# Theme

## GET /api/v1/theme
Fetch theme settings with resolved menus, URLs, and image URLs.

### Authorization
None.

### Request parameters
None.

### Successful response
```json
{
  "data": {
    "global": {
      "logo": {
        "type": "text",
        "text": "Ercee",
        "image_url": null,
        "url": "/"
      },
      "cta": {
        "label": "Kontaktujte nas",
        "url": "/rfq"
      }
    },
    "header": {
      "logo": {
        "type": "text",
        "text": "Ercee",
        "image_url": "https://cdn.example.com/storage/logo.png",
        "url": "/"
      },
      "menu": {
        "id": 3,
        "name": "Main menu",
        "slug": "main-menu",
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
      },
      "cta": {
        "label": "Kontaktujte nas",
        "url": "/rfq"
      }
    },
    "footer": {
      "logo": {
        "type": "text",
        "text": "Ercee",
        "image_url": null
      },
      "company_text": "Poskytujeme komplexni reseni pro vase projekty.",
      "menus": {
        "quick_links": null,
        "services": null,
        "contact": null,
        "legal": null
      },
      "cta": {
        "label": "Kontaktujte nas",
        "url": "/rfq"
      },
      "copyright_text": "© 2026 Ercee. Vsechna prava vyhrazena."
    }
  }
}
```

### Response notes
- `logo.image_url` is resolved from the public storage disk and may be `null`.
- `logo.url` and `cta.url` are resolved either from a direct URL or a linked page.
- `header.menu` and `footer.menus.*` return a full menu object or `null` when not set.
- `copyright_text` uses `{year}` replacement when configured, otherwise defaults.

### Menu object
| name | type | required | description | default |
| --- | --- | --- | --- | --- |
| id | integer | yes | Menu ID | - |
| name | string | yes | Menu name | - |
| slug | string | yes | Menu slug | - |
| items | array | yes | Navigation items | [] |

Navigation items follow the same schema as `/api/v1/navigation`.

### Error responses
None.
