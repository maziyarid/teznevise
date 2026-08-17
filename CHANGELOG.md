# Changelog

## 1.5.0 — 2026-08-17 — release-readiness branch

### Changes

- Added the native blog presentation field module to the production theme bootstrap.
- Added Unicode-aware automatic reading-time calculation.
- Added deterministic H2/H3 heading IDs, including duplicate explicit-ID handling.
- Made the TOC derive links from the prepared rendered post content so every emitted fragment targets an actual heading.
- Added responsive blog archive, taxonomy, single-post, TOC, content, tags, and pagination styles.
- Added featured image, modified date, comments, tags, previous/next navigation, and related posts.
- Improved archive post cards with semantic headings, category links, dates, lazy images, and accessible labels.
- Removed fabricated/demo blog cards from the homepage when there are no posts.
- Added contextual technical SEO descriptions.
- Added plugin-aware Open Graph and Twitter metadata.
- Added plugin-aware WebSite, Article, and BreadcrumbList JSON-LD.
- Added search/404 `noindex,follow` handling and robots.txt sitemap declaration.
- Kept WordPress core responsible for title tags, sitemap infrastructure, and canonical ownership.
- Made the header search control resolve through WordPress search URLs.
- Hardened mobile navigation with `aria-controls`, dialog semantics, focus restoration, Escape handling, and focus trapping.
- Added a responsive mobile TOC disclosure while retaining the sticky desktop TOC.
- Removed nested `main` landmarks from blog templates so the shared header/footer landmark remains singular.
- Updated theme and footer version metadata to 1.5.0.
- Added the release validation script, complete page-parity matrix, controlled requirements register, VPS deployment verification, and rollback documentation.

### Testing

- Current `main` inspected before branch creation: `643455415c1143efd862e01a3b644636c5a65a18`.
- All five repository branches inspected.
- PRs #1, #2, and #3 inspected, including review findings and reproduced bug descriptions.
- Live production root checked and returns `PLACEHOLDER3`; production is therefore FAIL.
- GitHub reports no workflow runs for the release commit.
- PHP lint, WordPress runtime, browser, VPS/cPanel, network, schema, and production-asset HTTP checks remain pending because the available environment cannot execute the production WordPress runtime or access the cPanel server.

### Deployment notes

- No GitHub Actions deployment was enabled.
- Production branch remains `main`.
- VPS cron/cPanel deployment remains authoritative.
- PR #4 is draft and has not been merged into `main`.
