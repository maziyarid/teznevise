# Changelog

## 1.6.3 — 2026-08-19 — PR #400 follow-up: remaining mappings + batching

### Changes

- Finish remaining shortcode → builder mappings: `[tz_price_box]`, `[tz_price_cta]`, `[tz_calculation_hub]`, `[tz_careers_terms]`, plus calculator tool pages for Cohen's kappa, ICC, KR-20, and goodness of fit.
- Populate `software_catalog` items from the download CPT at migrate time; load `inc/builder-download-catalog.php` so empty catalogs still hydrate at render.
- Candidate query skips pages that already have builder meta or a `_teznevise_migration_skip` marker, so empty parses cannot loop forever.
- Auto-run stays bounded (25 pages × 3 batches per admin load). Never calls `teznevise_migration_run( …, 0 )` from auto-run. Completion is recorded only after a live scan finds zero remaining candidates (`TEZNEVISE_MIGRATION_VERSION` 1.2.0).
- Fallback shortcodes for `tz_price_cta`, `tz_calculation_hub`, and `tz_careers_terms` so pages still work before WPCode / builder meta exists.
- Guard `TEZNEVISE_VERSION` / `TEZNEVISE_DIR` / `TEZNEVISE_URI` with `defined()` so child themes or extra bootstraps cannot fatal.
- Stop tracking `docs/sep_posts.sql`, `docs/sep_posts.csv`, and `docs/wpcode-snippets-export-2026-08-18.json`; ignore them going forward.

### Testing

- PHP lint on changed files.
- Mapping table in `docs/SHORTCODE-TO-BUILDER-MIGRATION.md` matches parser cases.

## 1.6.2 — 2026-08-19 — migration auto-run + version alignment

### Changes

- Loaded `inc/migration/auto-run.php` from theme bootstrap so shortcode-to-builder migration can run once on admin load.
- Footer now prints the version from the `TEZNEVISE_VERSION` constant instead of a hardcoded `1.5.0` string.
- Completed the incomplete version bump: aligned `style.css` Version header, `readme.txt` Stable tag, and `README.md` to `1.6.2` (matching `functions.php`).

### Testing

- PHP lint on changed files.
- Visual check that the site footer shows the current theme version.

## 1.6.1 — 2026-08-19 — feature/html-to-builder

### Changes

- Converted 14 singular HTML sources from `teznevise_work/` into Flexible Page Builder defaults (`inc/builder-defaults.json`).
- Documented the full 16-file inventory, including `post-sample.html` and the non-singular blog/404 architecture.
- Added an idempotent seeder (Appearance → Teznevise Setup) that writes builder JSON and excerpts without clobbering editor work.
- Templates prefer builder sections when present and keep native forms, NAP cards, the calculator, and the posts loop.
- Hero renderer now outputs `h1` on pages (`h2` on posts) so converted titles stay a single document heading.
- Added `scripts/html-to-builder.mjs` inventory/schema validator and tests.
- Slugs are unchanged; no 301 map is required.

### Testing

- `node scripts/html-to-builder.test.mjs`
- `node scripts/html-to-builder.mjs --check`
- `node --check` on new scripts; PHP lint when `php` is available.

## 1.6.0 — 2026-08-18 — feature/flexible-page-builder


### Changes

- Added the flexible page builder module (`inc/class-teznevise-builder.php`): section type registry, JSON post meta storage, field-level sanitization, and section renderers.
- Added the builder admin meta box and repeater UI with add, duplicate, remove, drag-and-drop reorder, per-section enable, collapse, Font Awesome icon picker, and media-library icon/image pickers.
- Added builder frontend styles, enqueued only on singular views that render enabled sections.
- Registered `_teznevise_builder_sections` with `show_in_rest` and a REST sanitizer shared with the meta-box save path.
- Wired `teznevise_builder_render_sections()` into the page templates and `single.php`.
- Documented the builder in `docs/PAGE-BUILDER.md`, `docs/EDITABLE-BACKENDS.md`, and the README.
- Bumped theme version metadata to 1.6.0.

### Testing

- `php -l` on every changed PHP file and `node --check` on `assets/js/builder-admin.js`.
- Standalone sanitize/render smoke test: unknown section types and fields dropped, invalid icon colors coerced, attachment IDs cast to integers, disabled sections skipped, markup escaped, JSON meta round-tripped.

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
