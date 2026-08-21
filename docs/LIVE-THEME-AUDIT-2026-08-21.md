# Live theme audit — 2026-08-21

## Executive summary

The production WordPress theme at `https://teznevise.ir` was compared with the `main` branch at desktop, tablet, and mobile widths. The supplied screenshots were reproducible as cascade and RTL layout defects: the single-post named grid physically placed the main article in the narrow left region, the native logged-in WordPress comment form had almost no component styling, and shared header/footer rules competed across multiple source sheets. Version 1.8.8 fixes these repository-owned issues in the canonical source sheets and generated public bundles.

The production review also surfaced content, plugin, authentication, and hosting concerns that cannot be safely changed from the theme repository. Those are listed separately so deployment of this patch does not imply that every operational issue is theme-owned.

## Method

- Inspected the production homepage and a long article (`/necessity-research/`) with browser accessibility snapshots and full-page screenshots at 1592 × 872.
- Compared the production DOM with `header.php`, `footer.php`, `single.php`, `comments.php`, shared partials, the enqueue graph in `functions.php`, and the generated bundle script.
- Reviewed representative route families from the production link inventory and REST snapshot: home, blog/article, thesis/proposal/statistics services, tools, inquiry/contact/about, legal pages, account/login, search, and footer destinations.
- Inspected console output, asset loading, and lab Web Vitals. The initial homepage request showed approximately 750 ms TTFB; this is recorded as a hosting/application-stack concern because the theme alone cannot establish the source.
- Rebuilt public CSS/JS bundles and ran syntax/diff validation after source changes.

## Tested route/state matrix

| Surface | Representative route/state | Width/state | Result before patch | Disposition |
|---|---|---:|---|---|
| Shared header | `/` and long article | 1592 desktop | Dense controls and inconsistent spacing; supplied captures show clipping at narrower desktop widths | Fixed: medium-desktop CTA reduction and stable nav width |
| Mobile header/drawer | Shared templates | tablet/mobile code path | Multiple historical overrides compete around 1050 px | Verified canonical breakpoint remains; focus treatment added |
| Footer | `/` and article | desktop/mobile | Columns and utility/legal rows can collide or leave uneven dead space; long email/address values are fragile | Fixed: stable 3/2/1-column behavior and wrapping |
| Long article | `/necessity-research/` | 1592 desktop | Main text rendered in the narrow left column; TOC occupied the broad right column | Fixed: physical RTL grid is now `aside content` |
| Article TOC | Long article | desktop/mobile | English label visible in supplied production capture; mobile/desktop variants differ | Fixed hard-coded label to Persian and retained mobile disclosure |
| Comments | Logged-in WordPress state in supplied capture | desktop | Native textarea/button, weak hierarchy, poor spacing and ambiguous required-field treatment | Fixed form markup and full native WP state styling |
| Comments | Logged-out | article | Functional but visually weak membership notice | Fixed shared notice/card layout |
| Archives/search | Blog/category/tag/search families | responsive | Shared cards generally coherent; inherited focus and overflow treatment incomplete | Improved shared focus and content wrapping |
| Services/pages | thesis/proposal/statistics/about/contact/inquiry/legal | responsive | Shared chrome/cascade concerns; no isolated fatal template defect found | Improved through shared chrome rules |
| Tools/calculators | tools index and calculator family | responsive | Theme styles present; data/result correctness depends on page/plugin implementation | No unsafe business-logic change |
| Account/login | WordPress login/account destinations | anonymous/authenticated | Authentication UI partly WordPress/plugin-owned | Documented as external where outside theme DOM |
| 404 | invalid route | anonymous | WordPress routing/content state | No fatal theme-level defect identified in static review |

## Prioritized issue register

### P0 — resolved

1. **RTL single-post column inversion**
   - Evidence: supplied article screenshot and production DOM.
   - Cause: CSS Grid track order is physical even in an RTL document; `"content aside"` put content in the first (left) track.
   - Fix: use `minmax(240px, 300px) minmax(0, 1fr)` with `"aside content"`; keep the article as the wide primary region and collapse to one column below 900 px.
   - Source: `assets/css/blog.css`, bundle tail in `scripts/build-frontend-bundles.py`, generated `pages.css`/`chrome.css`.

2. **Unstyled native WordPress comment UI**
   - Evidence: supplied logged-in screenshot.
   - Cause: `comments.php` delegated to `comment_form()` without theme classes or complete styles for WordPress-generated markup.
   - Fix: accessible Persian textarea label, explicit required text, theme button classes, responsive form grid, focus states, nested comments, reply links, logged-in/out notes, and validation-compatible native fields.
   - Source: `comments.php`, `assets/css/blog.css`.

### P1 — resolved

3. **Header crowding at medium desktop widths**
   - Evidence: supplied header captures at different widths.
   - Fix: below 1340 px the duplicate About/Inquiry CTA cluster is hidden while primary links and essential search/account/credit controls remain.

4. **Footer wrapping and alignment instability**
   - Evidence: supplied wide footer capture.
   - Fix: explicit 3/2/1-column progression, bounded container, breakable contact strings, aligned utility rows, and consistent trust-seal sizing.

5. **Keyboard navigation discoverability**
   - Cause: a prior release removed the skip link because its visually-hidden state was broken.
   - Fix: restore a Persian skip link with a visible focused state and add consistent `:focus-visible` treatment to interactive controls.

6. **Persian article chrome**
   - Evidence: “On this page” in supplied production screenshot.
   - Fix: desktop TOC label is now «در این مقاله»; mobile remains «فهرست مطالب».

### P2 — monitored or external

7. **Homepage TTFB (~750 ms in one lab run)**
   - Likely owners: hosting, WordPress object/page caching, database, plugins, and origin configuration.
   - Follow-up: measure multiple uncached/cached production samples after deployment; inspect server timing and Query Monitor before changing theme rendering.

8. **CMS-managed menu duplication or stale destinations**
   - The theme supplies safe fallbacks, but assigned WordPress menus and page slugs remain production data.
   - Follow-up: review Appearance → Menus after deployment and remove duplicate legacy entries rather than hard-coding around them.

9. **Plugin-owned account/login, calculator, and form behavior**
   - Theme styling can improve the shell, but authentication policy, submissions, validation, emails, and calculation correctness belong to WordPress/plugins.
   - Follow-up: run authenticated staging tests with representative roles and non-production submissions.

10. **Production content typography and malformed spacing**
    - Hard-coded theme labels were corrected where identified. Article body copy and builder content remain CMS-managed.
    - Follow-up: editorial review for Persian half-spaces, punctuation, heading hierarchy, image alt text, and outdated copy.

## Files changed

- `header.php`: restored the accessible Persian skip link.
- `single.php`: localized the desktop TOC label.
- `comments.php`: added accessible, styleable native WordPress comment-form markup.
- `assets/css/blog.css`: corrected RTL article tracks and added complete comments styling.
- `assets/css/react-loader.css`: hardened focus, medium-desktop header, and responsive footer behavior.
- `scripts/build-frontend-bundles.py`: corrected the authoritative unlayered article-grid rule.
- Generated public bundles: `assets/css/pages.css` and `assets/css/chrome.css`.
- Version metadata and changelogs updated to 1.8.8.

## Verification checklist

- [x] Canonical source CSS changed; no new catch-all stylesheet was introduced.
- [x] Public bundles rebuilt with `python3 scripts/build-frontend-bundles.py`.
- [x] Theme version aligned in `functions.php`, `style.css`, `readme.txt`, and `README.md`.
- [x] Article grid remains single-column below 900 px.
- [x] Comment form preserves WordPress `comment_form()` hooks, nonce/security fields, reply behavior, and native validation.
- [x] Focus indicators and skip link are keyboard-visible.
- [x] Footer contact strings can wrap without horizontal overflow.
- [ ] Authenticated staging submission test after deployment (requires a staging WordPress session and must not mutate production).
- [ ] Repeat production Core Web Vitals after cache warm-up and deployment.

## Deployment notes

The repository deploys `main` through the documented VPS/cPanel process. Merge the pull request only after review; do not bypass the branch workflow. After deployment, purge page/CDN caches, confirm the HTML references theme version 1.8.8 assets, then re-check the article and authenticated comment state before closing the operational follow-up.
