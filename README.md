# Teznevise WordPress Theme 1.1.0

RTL-first WordPress theme for [teznevise.ir](https://teznevise.ir/) — specialized thesis, proposal, and statistical analysis consulting.

**Primary brand token:** `#145D4A`  
**Language:** `fa_IR` (RTL)  
**Requires:** WordPress 6.4+, PHP 8.0+

## Installation

1. Upload the **repository root** contents into `wp-content/themes/teznevise/`  
   (or clone this repo directly as the theme folder).
2. Activate **Teznevise** under Appearance → Themes.
3. Set a static front page (Settings → Reading) if desired.
4. Assign menus under Appearance → Menus (Primary, Footer, Mobile, Bottom).
5. Edit homepage copy: **Appearance → Customize → تزنویسه — محتوای سایت**.
6. For service/contact pages: create Pages, assign template **صفحه خدمت** or **تماس / درخواست**, fill **فیلدهای تزنویسه** meta box.

## Editable backends

| Where | What |
|-------|------|
| Customizer → تزنویسه | Home hero, services, about, steps, CTA, contact NAP |
| Page editor → فیلدهای تزنویسه | Eyebrow, subtitle, CTAs, features, icons |

See `docs/EDITABLE-BACKENDS.md`.

## Page templates

| Template | Use for |
|----------|---------|
| صفحه خدمت (Service) | service-thesis, proposal, statistics, simulation |
| تماس / درخواسؠ (Contact) | contact, inquiry |
| Default `page.php` | about, privacy, tools, generic pages |

## Structure

```
style.css / functions.php / theme.json
front-page.php, home.php, page.php, page-service.php, page-contact.php
single.php, archive.php, search.php, 404.php
inc/defaults.php, customizer.php, page-meta.php, seo.php, helpers.php
template-parts/ mobile-nav, fab, bottom-nav, post-card
teznevise_work/  static reference + CSS/JS/logo (asset fallback)
docs/
```

## Styles & motion

CSS/JS resolve from `assets/` first, then `teznevise_work/assets/`:

- redesign, layout-refinements, motion, batch-fixes, ui-round2, site-polish
- Service CSS when those pages are viewed
- redesign.js (drawer, FAB, IntersectionObserver reveals)
- `prefers-reduced-motion` respected

## Docs

- `docs/CONVERSION-PLAN-1.0.md`
- `docs/REQUIREMENTS.md` / `RELEASE_CHECKLIST.md`
- `docs/EDITABLE-BACKENDS.md`
- `docs/CANONICAL.md`
