# Teznevise WordPress Theme

**Version:** 1.8.1

**Author:** MAZ//ID (Maziyar) · **Brand:** [MΛZ / maziyarid/M-Z](https://github.com/maziyarid/M-Z)

RTL-first WordPress theme for [teznevise.ir](https://teznevise.ir/) — thesis, proposal, research, and statistical consulting.

## Production source and deployment

- Production branch: `main`.
- Production theme path: `/home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise`.
- Deployment authority: VPS cron → cPanel VersionControl/update for `main` → cPanel VersionControlDeployment/create → `.cpanel.yml`.
- GitHub Actions deployment remains disabled intentionally. Do not re-enable it.
- `.cpanel.yml` excludes `.git` from the deployed theme directory.
- Never commit credentials, API tokens, SSH keys, passwords, or private uploads.

## Installation / setup

1. Install WordPress with PHP 8.0+ and WordPress 6.4+.
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
