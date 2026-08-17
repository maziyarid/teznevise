# Teznevise WordPress Theme 1.0.0

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

## Structure

```
style.css          Theme header (v1.0.0)
functions.php      Setup, enqueue, contact helpers, asset resolver
theme.json         Design tokens
header.php / footer.php
front-page.php     Homepage (richest motion)
index.php / page.php / single.php / 404.php
template-parts/    mobile-nav, fab, bottom-nav, post-card
inc/helpers.php
docs/              CONVERSION-PLAN, REQUIREMENTS, RELEASE_CHECKLIST, CANONICAL
teznevise_work/    Static HTML reference + CSS/JS/logo assets (loaded as fallback)
```

## Styles & assets

All redesign CSS and motion JS live under `teznevise_work/assets/` and are enqueued automatically via `teznevise_resolve_asset()`:

- `redesign.css`, `layout-refinements.css`, `motion.css`
- `batch-fixes.css`, `ui-round2.css`, `site-polish.css`
- Service-specific CSS when those pages are viewed
- `redesign.js` (mobile nav, FAQ, SEO toggle, FAB, reveal animations)
- Logo: `teznevise_work/assets/img/logo.jpg`

## Motion

Homepage uses the full motion system:

- Hero cascade, ink-blots, particles, network rings, orbit tags
- `data-reveal` / `data-reveal-stagger` IntersectionObserver
- `prefers-reduced-motion` respected

## Docs

See `docs/CONVERSION-PLAN-1.0.md`, `docs/REQUIREMENTS.md`, and `docs/RELEASE_CHECKLIST.md` for the Fasdent-style completeness gate.
