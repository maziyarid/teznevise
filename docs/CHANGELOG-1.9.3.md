# Teznevise 1.9.3 changelog

**Date:** 2026-08-21  
**Branch:** `main`  
**Why this release exists:** production fatals on `functions.php:192` (`inc/template-functions.php` missing) plus the attached UI/architecture audits.

## Site-down (TN-B01 / PHP log)

| Before | After |
|---|---|
| `functions.php` was an Underscores hybrid requiring `inc/template-functions.php`, `inc/jetpack.php`, `inc/woocommerce.php`, `inc/elementor.php`, `js/navigation.js`, `css/admin.css` — none of which exist. | Restored the 1.9.2 bootstrap (`e905f6d`): `TEZNEVISE_VERSION`, real module list, real CSS/JS bundles. Version **1.9.3**. |

Every `require` target in `functions.php` exists in the repository.

## Navigation screenshot

- Mega panel: 3 equal columns, wrapping Persian labels, no leftover empty third of the card.
- Top-level icons use self-hosted Font Awesome 7 (empty checkbox tofu was the missing webfont after the bootstrap regression).
- Header actions (search, coins, account) are 44×44.

## Tool AI chat

- Server-rendered HTML + vanilla `js/ai/chat.js` (no JSX, no `ReactDOM`).
- Opening assistant line: **اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری**
- Agent **name under every message**; model/agent pickers; thinking `<details>`; collaboration `single | collaborative | separate`.
- Admin: Settings → TezNevise AI for API keys, free-tier cap, signed-in cap, cost-per-message.
- Each agent row stores its own `api_endpoint`, `api_key`, `model`.
- REST `/teznevise-ai/v1/chat` generates server session IDs; history is **logged-in only**. Guest IP uses `REMOTE_ADDR` only. Defaults are insert-if-missing (no longer `REPLACE` over admin keys).
- Mounted under every single-tool template.

## Dashboard

- Tabs: wallet, profile, tickets, projects, **AI history**.
- Restyled pill tabs and chat log.

## UI audit P0s from the attached live audit

| ID | Fix |
|---|---|
| TZ-002 `/tel:` links | `teznevise_tel_href()` emits `tel:+98…` without `esc_url()` turning it into a path. |
| TZ-003 fake trust seals | Enamad/Samandehi only render when a real external URL is saved; no more link to `/privacy/`. |
| Header/icons mismatch | Real FA + chrome bundles enqueued again. |

Remaining audit items (canonical 301s, shortcode content on specific WP pages, author profiles) are content/CMS work and are listed in `docs/UI-UX-AUDIT-2026-08-21.md` — they are not PHP fatals.

## Intentionally not done in this release

- Mounting the full TanStack `react-app/` inside WordPress (still a separate Vercel app; WordPress remains the production frontend).
- Regenerating `react-app/package-lock.json` / Grok PWA tests (React prototype, not the live theme).
- Changing cPanel rsync (`--delete`) without a staging rehearsal.
