# Cookie Configuration

## GET /api/v1/cookies/config
Fetch cookie consent configuration for the frontend banner.

### Authorization
Required: `Authorization: Bearer <token>` (uses `API_PUBLIC_TOKEN`).

### Request parameters
None.

### Successful response
```json
{
  "data": {
    "banner": {
      "enabled": true,
      "title": "Tato stranka pouziva cookies",
      "description": "Pouzivame cookies pro zlepseni vaseho zazitku na strance.",
      "accept_all_label": "Prijmout vse",
      "reject_all_label": "Odmitnout vse",
      "customize_label": "Nastaveni",
      "save_label": "Ulozit nastaveni",
      "position": "bottom",
      "theme": "light"
    },
    "categories": {
      "necessary": {
        "name": "Nezbytne",
        "description": "Nezbytne cookies pro spravne fungovani webu.",
        "required": true,
        "default_enabled": true
      },
      "analytics": {
        "name": "Analyticke",
        "description": "Cookies pro analyzu navstevnosti.",
        "required": false,
        "default_enabled": false
      }
    },
    "services": {
      "necessary": [
        {
          "name": "Session",
          "description": "Session cookie pro spravu prihlaseni.",
          "cookie_pattern": "laravel_session"
        }
      ],
      "analytics": [
        {
          "name": "Google Analytics",
          "description": "Sledovani navstevnosti webu.",
          "cookie_pattern": "_ga*"
        }
      ]
    },
    "policy_links": {
      "privacy_policy": {
        "label": "Zasady ochrany osobnich udaju",
        "url": "/privacy-policy"
      },
      "cookie_policy": {
        "label": "Zasady cookies",
        "url": "/cookie-policy"
      }
    }
  },
  "meta": {
    "updated_at": "2026-02-12T14:30:00+00:00"
  }
}
```

### Response notes
- When no `CookieSetting` record exists, the endpoint returns built-in defaults.
- `banner.position` is one of: `bottom`, `top`, `center`.
- `banner.theme` is one of: `light`, `dark`.
- `categories` is a keyed object where each key (e.g. `necessary`, `analytics`) identifies the category.
- `categories.*.required = true` means the category cannot be disabled by the user.
- `services` is grouped by category key. Each entry is an array of services belonging to that category.
- `policy_links.*.url` is resolved from a linked page slug or a direct URL. May be `null` if unresolvable.
- Response is cached with a timestamp-based cache key. Cache is invalidated on save in CMS.

### Frontend integration
- TypeScript type: `CookieConfigResponse` in `src/shared/api/types.ts`
- API client: `getCookieConfig()` in `src/features/site/api/cookies.ts`
- Consent state is stored in `localStorage` under key `ercee_cookie_consent`
- On consent change, a `cookie-consent-updated` CustomEvent is dispatched on `window`
- Scripts with `data-consent-category="<key>"` attribute are activated only after consent is granted for that category

### Error responses
| status | body | when |
| --- | --- | --- |
| 503 | `{"error": "Service unavailable"}` | Database or cache unavailable |
| 500 | `{"error": "Internal server error"}` | Unexpected failure |
