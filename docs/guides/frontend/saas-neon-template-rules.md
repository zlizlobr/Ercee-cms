# SaaS Neon Template Rules (Frontend + Admin)

This guide defines mandatory visual rules for Ercee CMS based on the selected SaaS Neon direction (`saas-neon.nucleusforgedesigns.com`) and the implemented local template.

Use this as the source of truth for new UI work in:

- `resources/views/frontend/*.blade.php`
- `resources/views/components/blocks/*.blade.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `resources/css/saas-neon.css`

## 1. Visual Principle

- Default mode is dark.
- White mode is the only allowed light variant.
- UI must keep a glass surface style: soft borders, blur, subtle glow, high contrast headings.
- Accent color is orange. Do not switch accent family per page/module.

## 2. Canonical Color Tokens

Always use the existing tokens from `resources/css/saas-neon.css`.

### Frontend tokens

- Background: `--sn-bg`
- Surface: `--sn-surface`
- Strong surface: `--sn-surface-strong`
- Text primary: `--sn-text`
- Text muted: `--sn-muted`
- Border/line: `--sn-line`
- Accent primary: `--sn-primary`
- Accent stronger: `--sn-primary-strong`
- Glow: `--sn-glow`

### Mode values

- Light mode (`:root`)
  - `--sn-bg: #ffffff`
  - `--sn-text: #0f172a`
  - `--sn-primary: #ea580c`
- Dark mode (`html.theme-dark`)
  - `--sn-bg: #05070f`
  - `--sn-text: #e2e8f0`
  - `--sn-primary: #fb923c`

## 3. Color Usage Rules

- Primary CTA: `saas-btn-primary` only.
- Secondary/ghost CTA: `saas-btn-secondary` only.
- Navigation links: `saas-nav-link` only.
- Cards and wrappers: `saas-shell` or `saas-block` only.
- Hero wrapper: `saas-hero` only.
- Do not use random Tailwind palette classes for main branding (example forbidden: `bg-purple-*`, `text-emerald-*`, `bg-pink-*`).
- Use direct `text-gray-*` classes only for legacy content blocks already in production. New components should prefer token-driven classes.

## 4. Required Structural Pattern (Frontend)

- Page root uses class `saas-neon`.
- Header and nav use glass container (`saas-shell`) with rounded edges.
- Main page includes:
  - hero shell with page title and optional description
  - content sections wrapped by `saas-block`
- Footer uses `saas-footer`.
- Theme switch persists in `localStorage` under `saas-theme`.

Reference implementation:

- `resources/views/frontend/layout.blade.php`
- `resources/views/frontend/page.blade.php`

## 5. Admin Part Rules (Filament)

- Default admin theme mode is dark.
- Admin primary color is orange.
- Admin surfaces keep same glass language: semi-transparent backgrounds + blur + soft border.
- Rounded controls are required (`0.75rem` family for buttons/items).
- Active nav item uses orange outline/glow.

Implementation source:

- `app/Providers/Filament/AdminPanelProvider.php`

## 6. Do / Don’t

- Do keep one accent family (orange) across frontend and admin.
- Do keep dark and white modes visually equivalent in spacing/layout.
- Do reuse existing classes from `resources/css/saas-neon.css` before adding new ones.
- Don’t add new theme files for one-off pages.
- Don’t hardcode unrelated color hex values in Blade templates.
- Don’t mix radically different visual systems between modules.

## 7. QA Checklist Before Merge

- `npm run build` passes.
- Dark mode works on frontend and admin.
- White mode toggles correctly on frontend and persists after reload.
- New page/block has no off-brand colors.
- CTA hierarchy is consistent (`saas-btn-primary` first action, `saas-btn-secondary` second action).
- Mobile header/menu remains readable in both modes.
