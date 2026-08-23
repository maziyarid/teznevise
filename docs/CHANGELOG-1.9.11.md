# Teznevise 1.9.11

**Date:** 2026-08-23  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`).

After deploy, open wp-admin once so the waitlist table exists. Existing members receive 30 welcome coins on next login.

## What changed

- Consulting-first copy at render time (builder, Classic, Customizer, page fields). Ghostwriting FAQs and «می‌نویسیم» claims are rewritten.
- Footer: exact Enamad HTML + TrustedSite `https://cdn.ywxi.net/js/1.js`. Fake ساماندهی/اتحادیه/ثبت ملی tiles removed.
- University crests: `tz-uni-card` (88×88, white tile). The 52px green letter-badge no longer clips logos.
- Tools notice is a dismissible bottom dock with the original sentence. It no longer sits under the header.
- `/account/` is a customer portal: login/register, dashboard vs profile, password change, logout. wp-login.php is reserved for staff (`redirect_to=wp-admin` or `?staff=1`).
- New users get 30 تزکوین (`teznevise_welcome_coins` meta).

## Rollback

Remove `hotfix-201.css` from `teznevise_runtime_css_files()` to drop the visual layer. Do not remove `inc/legal-copy.php` while Enamad is active.
