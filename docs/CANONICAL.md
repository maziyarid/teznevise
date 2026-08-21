# Teznevise Theme — Canonical Source

**Version:** 1.8.5  
**Date:** 2026-08-17  
**Repository:** maziyarid/teznevise  

## Single Source of Truth

The **repository root** is the canonical production WordPress theme.

- Theme files (`style.css`, `functions.php`, `header.php`, `footer.php`, `front-page.php`, `assets/`, `inc/`, `template-parts/`, `theme.json`) live at the root.
- `teznevise_work/` is **historical static HTML reference only**. It must not be deployed as the theme.
- All production changes happen in the root theme files.
- Version identity is recorded in `style.css` Theme header and asset query strings.

## Deployment Rule

Upload **only** the theme files from the repository root into `wp-content/themes/teznevise/` (or the chosen slug).  
Do **not** upload `teznevise_work/` or any historical folders into the production themes directory.

## Authority Documents

- `docs/CONVERSION-PLAN-1.0.md` — full conversion plan
- `docs/REQUIREMENTS.md` — gate requirements (Blocker=Yes must PASS)
- `docs/RELEASE_CHECKLIST.md` — executable release checklist
- `docs/PREMIUM-STACK-PLAN.md` — CSS collapse + one-frontend roadmap (1.8.4). Do not stack Next.js on TanStack.

This document is the equivalent of Fasdent’s `CANONICAL_SOURCE.md`.
