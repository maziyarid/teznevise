# Theme assets

Canonical production assets live under **`assets/`** at theme root.

| Path | Purpose |
|------|---------|
| `assets/css/*.css` | redesign, motion, layout, polish, service pages |
| `assets/js/redesign.js` | motion, nav drawer, FAB, counters |
| `assets/img/logo.jpg` | brand logo fallback |
| `assets/icons/sprite.svg` | optional SVG sprite |

## How assets load

`teznevise_resolve_asset()` checks, in order:

1. `assets/...` (theme production path)
2. `teznevise_work/assets/...` (reference fallback)

## Promote from admin

**Appearance → راه‌اندازی تزنویسه → کپی دارایی‌ها به assets/**

Copies missing files from `teznevise_work/assets` into `assets/` on the server (does not overwrite existing files).

`teznevise_work/` remains **reference only** after promote.
