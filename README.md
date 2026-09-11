# Teznevise WordPress Theme

**Version:** 1.9.31

**Author:** MAZ//ID (Maziyar) · **Brand:** [MΛZ / maziyarid/M-Z](https://github.com/maziyarid/M-Z)

RTL-first WordPress theme for [teznevise.ir](https://teznevise.ir/) — thesis, proposal, research, and statistical consulting.

## Teznevise Design System v2

The proprietary design-system contract lives in [`design-system/`](./design-system/). It defines the Persian-first editorial language, five-color semantic palette, local Vazirmatn typography, spacing and motion scales, `tz-*` component contracts, and the signature research-margin pattern.

The WordPress 2.0 branch deliberately keeps the proven PHP/builder DOM stable instead of adding canonical class aliases to structurally different elements. The public v2 runtime is owned by:

- [`assets/css/v2-foundation.css`](./assets/css/v2-foundation.css) — local Vazirmatn, canonical tokens, reset, typography and accessibility baseline.
- [`assets/css/v2-compat.css`](./assets/css/v2-compat.css) — WordPress-native mapping for the real header, mega menu, builder grids, process steps, FAQ, footer and floating controls.
- Maintained specialised sheets such as `blog.css`, `page-extras.css` and `wp-compat.css` remain available for their existing template contracts.
- Historical `hotfix-*.css` files remain in source until dependency/DB probes are complete, but the 2.0 runtime does not concatenate them.
- WordPress editor presets remain in [`theme.json`](./theme.json); Tailwind remains isolated to `react-app/`.
- Extend the owning v2 component/adapter stylesheet. Do not add another global `*-fix.css` or hotfix layer.

The [Premium v2 roadmap](./docs/PREMIUM-V2-ROADMAP.md) is incremental. WordPress remains server-rendered for speed, SEO, and compatibility; React/TypeScript is selective, and a future headless frontend replaces rather than duplicates the current TanStack runtime.

## Production source and deployment

- Production branch: `main`.
- Production theme path: `/home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise`.
- Deployment authority: VPS cron → cPanel VersionControl/update for `main` → cPanel VersionControlDeployment/create → `.cpanel.yml`.
- GitHub Actions deployment remains disabled intentionally. Do not re-enable it.
- `.cpanel.yml` excludes `.git` from the deployed theme directory.
- Never commit credentials, API tokens, SSH keys, passwords, or private uploads.

## Installation / setup

1. Install WordPress 6.9+ with PHP 8.3+; the theme has been reviewed for PHP 8.5 readiness.
2. Upload/activate the theme under Appearance → Themes.
3. Appearance → راه‌اندازی تزنویسه to seed recommended pages and builder defaults.
4. Optionally promote assets from `teznevise_work/` into `assets/`.
5. Appearance → Customize for homepage sections and contact details.
6. Assign menus: Primary, Mobile, Bottom, Footer.

## Development notes

- Theme version is the single source of truth in `functions.php` (`TEZNEVISE_VERSION`).
- `style.css`, `readme.txt`, and this README must stay aligned with that constant.
- Footer displays the version dynamically via the constant (no hardcoded strings).
- Integrity guards (`.github/workflows/`) lint PHP, verify style.css header, and ensure `header.php` symbols resolve.

grep -n "TEZNEVISE_VERSION" functions.php

The deployed SHA must match the intended `main` release SHA. Review cPanel VersionControl and VersionControlDeployment logs when the SHA does not advance.

## Versioning policy

1. Bump `TEZNEVISE_VERSION` in `functions.php` first.
2. Mirror the same value in `style.css` (Version header), `readme.txt` (Stable tag), and `README.md`.
3. Add a CHANGELOG entry.
4. Allow the VPS cron to pull the corrected `main` revision through cPanel VersionControl.

## React app

The TanStack/React companion app (wallet, dashboard, tools) lives in [`react-app/`](./react-app/).
