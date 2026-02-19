# SaaS Neon Layout — Implementation Tasks

Reference: https://saas-neon.nucleusforgedesigns.com
Design rules: `docs/guides/frontend/saas-neon-template-rules.md`
Date: 2026-02-19

---

## Audit Summary

| Area | Status |
|------|--------|
| CSS design tokens (`saas-neon.css`) | ✅ Done |
| Frontend layout (`layout.blade.php`) | ✅ Done — multi-column footer implemented |
| Hero block (components/blocks/hero.blade.php) | ✅ Fixed — SaaS Neon tokens, badges, stats row |
| Admin panel base config | ✅ Expanded — modals, widgets, nav groups, inputs |
| 21 block components | ✅ All implemented — full SaaS Neon UI |

---

## Tasks

### A — Critical Layout Fixes

- [x] **A1** Fix `resources/views/components/blocks/hero.blade.php` — remove `from-blue-600 to-purple-700`, use `saas-hero` + SaaS Neon token classes
- [x] **A2** Expand footer in `resources/views/frontend/layout.blade.php` — multi-column: brand + nav links + legal, matches reference footer structure

---

### B — Frontend Block Implementations

All blocks must use `saas-shell`, `saas-block`, `saas-btn-primary`, `saas-btn-secondary` classes.
No `bg-gray-*`, `text-gray-*`, `from-blue-*` or hardcoded hex colors in Blade templates.

- [x] **B1** `page_hero` — inner-page hero: subtitle pill, large title, description, stats row, optional badges, optional background image
- [x] **B2** `faq` — accordion: subtitle + title + description header, Alpine.js `x-show` expand/collapse items
- [x] **B3** `stats_cards` — KPI grid: subtitle + title + icon + value + label, 2–4 col responsive grid
- [x] **B4** `service_highlights` — service cards: subtitle + title + description header, service card grid with icon + title + description + optional link
- [x] **B5** `image-cta` — split layout: left text column (subtitle, title, description, two CTA buttons), right background image panel
- [x] **B6** `process_steps` — numbered step cards: subtitle + title + description header, step cards with number badge + icon + title + description
- [x] **B7** `trust_showcase` — trust cards + CTA footer: subtitle + title + description, icon + title + description cards, bottom CTA strip with button
- [x] **B8** `technology_innovation` — tech feature grid: subtitle + title + description + optional image, feature items with icon + title + description, optional CTA
- [x] **B9** `capabilities_detailed` — capability cards: title + subtitle header, items with icon + image + title + description + feature bullet list
- [x] **B10** `support_cards` — support channel cards: subtitle + title + description header, cards with icon + title + description + optional link
- [x] **B11** `use_case_tabs` — tabbed use cases: subtitle + title + description header, Alpine.js tab switcher, each tab: industry name + challenge + solution + results bullets
- [x] **B12** `stats_showcase` — full-width dark stats banner: background image optional, large stat values in grid + optional logo strip below
- [x] **B13** `process_workflow` — workflow steps + benefits: subtitle + title, numbered step cards with optional image, benefits row below with icon + title + description
- [x] **B14** `image_grid` — photo/media grid: title + subtitle header, responsive masonry-style or even grid of images
- [x] **B15** `facilities_grid` — facility cards: title + subtitle, card grid with image + name + description + optional badge
- [x] **B16** `facility_standards` — standards list: title + subtitle, ordered list with icon + title + description items
- [x] **B17** `facility_stats` — facility KPI row: title + subtitle, large stat values in horizontal strip
- [x] **B18** `industries_served` — industry grid: title + subtitle, icon + industry name + description cards
- [x] **B19** `map_placeholder` — map embed placeholder: title + subtitle + address info card + styled iframe placeholder
- [x] **B20** `doc_categories` — docs category cards: title + subtitle, cards with icon + title + description + link
- [x] **B21** `documentation_search` — search hero: title + subtitle, search input with button, optional popular tags row

---

### C — Admin Panel Improvements

- [x] **C1** Expand inline CSS in `AdminPanelProvider.php` — add missing Filament selectors: `.fi-modal`, `.fi-dropdown-panel`, `.fi-nav-group-label`, `.fi-widget`, `.fi-header`, `.fi-breadcrumbs`
- [ ] **C2** (Optional) Create `resources/css/filament/admin-theme.css` for Filament theme compilation if inline CSS grows too large

---

## QA Checklist

- [ ] `npm run build` passes
- [ ] Dark mode correct on all new blocks
- [ ] White mode toggles correctly, no hardcoded dark colors visible
- [ ] No off-brand colors (`purple`, `blue`, `emerald`, `pink` gradients) in new blocks
- [ ] CTA hierarchy consistent (`saas-btn-primary` first, `saas-btn-secondary` second)
- [ ] Mobile responsive on all new blocks
- [ ] Admin glassmorphism renders in both dark and light mode
