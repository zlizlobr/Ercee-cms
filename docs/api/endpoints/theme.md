# Theme

## GET /api/v1/theme
Fetch theme settings with resolved menus, URLs, and image URLs.

### Authorization
Required: `Authorization: Bearer <token>` (uses `API_PUBLIC_TOKEN`).

### Request parameters
None.

### Successful response
```json
{
  "data": {
    "global": {
      "logo": {
        "type": "image",
        "text": "Ercee",
        "image_url": "/media/a1b2c3d4/logo.png",
        "media": {
          "uuid": "a1b2c3d4",
          "url": "/media/a1b2c3d4/logo.png",
          "alt": "Site Logo",
          "title": "logo",
          "width": 200,
          "height": 60,
          "mime": "image/png",
          "focal_point": null,
          "variants": {
            "thumb": { "url": "/media/a1b2c3d4/conversions/logo-thumb.jpg", "width": 150, "height": 150 },
            "medium": { "url": "/media/a1b2c3d4/conversions/logo-medium.jpg", "width": 600, "height": 600 }
          }
        },
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
        "image_url": null,
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
- `logo.image_url` is resolved from MediaLibrary (`/media/...`) or legacy public storage. May be `null`.
- `logo.media` is present only when the logo uses a MediaLibrary item (`logo_media_uuid`). Contains full media object with variants.
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
