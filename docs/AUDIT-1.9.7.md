# Audit status after 1.9.7

The attached `main` branch audit targeted SHA `3eadc23` (regressed Underscores bootstrap). Production recovered in 1.9.3–1.9.6. This table is the 1.9.7 close-out against that document and the live bugs reported for this release.

| ID | Audit finding | Status in 1.9.7 |
|---|---|---|
| TN-B01 | Missing `inc/template-*.php` requires | **Fixed** in 1.9.3. Current `functions.php` only requires tracked files; `scripts/check-release.py` gates this. |
| TN-B02 | 1.9.2 bootstrap replaced | **Fixed**. `TEZNEVISE_VERSION`, module list, menus, and bundles are present. |
| TN-B03 | Real CSS/JS not enqueued | **Fixed**. tokens → components → pages → chrome → modernization → hotfix-196 → hotfix-197. |
| TN-B04 / B05 / H14 | React is a separate Nitro app | **Accepted architecture.** WordPress-first. `react-app/` is not enqueued. |
| TN-B06 | Raw JSX AI bundle | **Fixed.** Canonical script is vanilla `js/ai/chat.js`. |
| TN-B07 | Public chat history / session takeover | **Hardened.** History requires login and `user_id`. `save_session` refuses to attach a token to a different user and mints a new UUID. Guest quota uses HMAC of IP+UA, not spoofable `X-Forwarded-For`. |
| TN-B08 | First React user becomes admin | **N/A to WordPress.** React remains a prototype; not deployed. |
| TN-H01 / H02 / H09 / H10 | React lockfile, tests, env, PGLite | **Out of WP scope.** Not on the cPanel path. |
| TN-H03 | rsync without `--delete` | **Operational.** Theme cannot change cPanel tasks. Documented; no stale-file fatal remains in Git. |
| TN-H04 | Tools AI disconnected | **Fixed.** `template-parts/tools-ai.php` renders `teznevise_ai_shortcode()`. `teznevise_list_ai_agents()` wraps the agents table. |
| TN-H05 | Duplicate WP/React domains | **Accepted.** WordPress owns production content, auth, wallet, AI. |
| TN-H06 | AI settings vs runtime stores | **Fixed.** Limits/cost read `get_option('teznevise_ai_*')` in the REST handler. |
| TN-H07 | Credits / limits inconsistent | **Partially fixed** in 1.9.6 (quota lock, Tezcoin guard). 1.9.7 keeps one reserved unit per prompt. |
| TN-H08 | Invalid AI enqueue hooks | **Fixed.** Chat class enqueues `wp_enqueue_scripts`. |
| TN-H11 | Failed checks do not block VPS cron | **Operational.** Cannot enforce branch protection from the theme. |
| TN-H12 | Version drift | **Fixed** for 1.9.7: `functions.php`, `style.css`, `README.md`, `readme.txt`, `CHANGELOG.md` share `1.9.7`. |
| TN-H13 | Modules present but not loaded | **Fixed** since 1.9.3. |
| TN-H15 | `replace()` overwrites agent config | **Fixed.** Agents and skills are insert-if-missing. |
| TN-H16 | Spoofable guest IP | **Fixed** in 1.9.6 (`REMOTE_ADDR` only). |
| TN-M04 / M05 | FA version, menu locations | **Fixed.** FA7 self-hosted; `mobile` and `bottom` registered. |
| TN-M06 | Missing admin assets | Residual 404s for unused `_s` editor files are not enqueued by the current bootstrap. |

## Product bugs closed in this release

1. Classic Editor still showing `[tz_thesis_hub]` — 1.9.7 re-import + layout-hub stripping.
2. Mega opening from page centre — scoped panel + item-only hover.
3. AI looking like a contact form — Perplexity chrome, backend agent/provider CRUD.
4. Audit leftovers above.
5. No hero inquiry, boxed short cards, tick-only icons, tiny featured images, single-list comments, WP-looking dashboard, WP fingerprints.

## Still operational (not theme-fixable)

- Protect `main` and stop deploying failed checks.
- Add `--delete` or an allowlisted artifact to cPanel rsync.
- React lockfile/tests if that prototype is ever shipped separately.
