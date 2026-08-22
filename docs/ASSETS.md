# Theme assets

Canonical production assets live under **`assets/`** at theme root.

| Path | Purpose |
|------|---------|
| `assets/css/tokens.css` | design tokens + local Vazirmatn `@font-face` |
| `assets/css/components.css` | bundled cards / motion / builder (generated) |
| `assets/css/pages.css` | bundled blog / page extras (generated) |
| `assets/css/chrome.css` | bundled header / nav / footer / bottom-nav (generated) |
| `assets/css/legacy-wpcode.css` | calculator leftovers — **conditional** |
| `assets/css/service-*.css` | service pages — **conditional, never homepage** |
| `assets/css/_archive/styles.css` | never enqueued |
| `assets/js/chrome.js` | one public JS bundle (generated) |
| `assets/fonts/*.woff2` | Vazirmatn Regular / Medium / Bold / ExtraBold |
| `assets/img/universities/` | official SVG/WebP partner crests (`SOURCES.txt`) |
| `assets/icons/sprite.svg` | optional SVG sprite |

Rebuild generated files:

```
python3 scripts/build-frontend-bundles.py
```

Source sheets (`redesign.css`, `react-parity.css`, …) stay in `assets/css/` for the rebuild. They are **not** enqueued.

## How assets load

`teznevise_resolve_asset()` checks, in order:

1. `assets/...` (theme production path)
2. `teznevise_work/assets/...` (reference fallback)

Do not resolve CSS from `teznevise_work/` for the public front. Public CSS is the four layered files above.

## Promote from admin

**Appearance → راه‌اندازی تزنویسه → کپی دارایی‌ها به assets/**

Copies missing files from `teznevise_work/assets` into `assets/` on the server (does not overwrite existing files).

`teznevise_work/` remains **reference only** after promote.
