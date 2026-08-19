# Flexible Page Builder — HTML to WordPress conversion

## Overview

Convert the static HTML in `teznevise_work/` into WordPress pages whose
repeatable sections live in the Flexible Page Builder (`_teznevise_builder_sections`).

Non-technical editors then manage those sections from the page/post editor.
No URL slugs change in this conversion, so no 301 map is required.

## Inventory (16 HTML files)

`teznevise_work/` has **16** top-level HTML pages. **14** are singular and
seed the builder. **2** stay on native/coded templates because the builder
stores sections as post meta and those requests have no editable post ID.

| # | Source | WordPress target | Builder? | Notes |
|---|--------|------------------|----------|-------|
| 1 | `about.html` | `page-about.php` `/about/` | Yes | hero, mission, timeline, policy, CTA |
| 2 | `contact.html` | `page-contact.php` `/contact/` | Yes | NAP + form stay in the template |
| 3 | `downloads.html` | `page-downloads.php` `/downloads/` | Yes | catalog + CTA |
| 4 | `index.html` | `front-page.php` `/` | Yes | Customizer hero visual kept |
| 5 | `inquiry.html` | `page-contact.php` `/inquiry/` | Yes | same template as contact, different slug |
| 6 | `privacy.html` | `page-privacy.php` `/privacy/` | Yes | legal body stays in `post_content` |
| 7 | `service-proposal.html` | `page-service.php` `/service-proposal/` | Yes | shared service template |
| 8 | `service-simulation.html` | `page-service.php` `/service-simulation/` | Yes | shared service template |
| 9 | `service-statistics.html` | `page-service.php` `/service-statistics/` | Yes | shared service template |
| 10 | `service-thesis.html` | `page-service.php` `/service-thesis/` | Yes | shared service template |
| 11 | `team.html` | `page-team.php` `/team/` | Yes | stats + specialties + CTA |
| 12 | `tools.html` | `page-tools.php` `/tools/` | Yes | software catalog |
| 13 | `tool-descriptive-statistics.html` | `page-tool.php` `/tool-descriptive-statistics/` | Yes | calculator stays in content |
| 14 | `post-sample.html` | `single.php` (sample post) | Yes | article body is native post content |
| 15 | `blog.html` | `home.php` `/blog/` | **No** | native posts index — see below |
| 16 | `404.html` | `404.php` | **No** | no queried post ID — see below |

Service pages do **not** get four new PHP templates. `page-service.php` already
covers them. Inquiry does **not** get `page-inquiry.php`; it reuses
`page-contact.php` with inquiry-specific builder JSON.

## Non-singular architecture (blog + 404)

The builder registers only for `page` and `post`, persists JSON as post meta,
and `teznevise_builder_has_sections()` runs only on singular queries
(`inc/class-teznevise-builder.php`).

| Layout | Why builder meta cannot own it | What this PR does | Follow-up if we ever need it |
|--------|--------------------------------|-------------------|------------------------------|
| Blog index (`blog.html` → `home.php`) | The posts index is a WP_Query loop. A Posts page *can* have an ID, but the cards are posts, not builder items. | Keep `home.php`. Optional intro remains the posts-page excerpt. | Optional singleton “Blog intro” page whose builder sections render *above* the loop. |
| 404 (`404.html` → `404.php`) | A 404 has no queried object. There is nothing to attach `_teznevise_builder_sections` to. | Keep the coded `404.php` (search + quick links + `noindex`). | Option-backed layout (`teznevise_404_sections`) plus a dedicated Appearance screen. |

Those two files stay in the inventory so they are not silently dropped, but
they are **not** builder conversion deliverables.

## SEO continuity

- Slugs match `docs/PAGE-MAP.md` (`/about/`, `/contact/`, `/service-thesis/`, …).
- Seed writes `post_excerpt` from the HTML lead so `inc/seo.php` can fill
  meta description / OG when no SEO plugin is active.
- Hero sections render an `h1` (see `teznevise_builder_render_hero()`).
- Schema, canonical, and robots stay in `inc/seo.php`. No permalinks change.

## Overlap with PR #384

`docs/REDESIGN-REMAINING-WORK.md` on the mobile-menu branch scopes several of
the same files as hard-coded System A templates. **This branch is the
builder-section source of truth** for the singular pages in the table above.
404 stays coded on both sides.

## How conversion is applied

1. Defaults live in [`inc/builder-defaults.json`](../inc/builder-defaults.json).
2. [`inc/builder-defaults.php`](../inc/builder-defaults.php) loads them.
3. [`inc/builder-seed.php`](../inc/builder-seed.php) writes post meta.
4. **Appearance → راه‌اندازی تزنویسه** seeds pages *and* builder sections
   (never overwrites existing builder JSON unless you check replace).
5. Templates skip hardcoded blocks when matching builder types exist, so
   seeded pages do not double-render.

```bash
node scripts/html-to-builder.mjs --check
node scripts/html-to-builder.test.mjs
```

## Status

| Phase | Status |
|-------|--------|
| 1 Analysis & planning | Complete |
| 2.1 HTML inventory (16 files) | Complete |
| 2.2 Conversion map + script | Complete |
| 2.3 Seed defaults for 14 singular sources | Complete |
| 2.4 Special cases (blog, 404, inquiry, sample post, calculator) | Complete |
| 3 Automated inventory/schema tests | Complete |
| 3 Visual / runtime QA on a live WP install | Pending deploy |
| 4 User documentation | Complete — `docs/HTML-TO-BUILDER-GUIDE.md` |
| 5 Frontend click-to-edit | Out of scope (still Phase 4 of the builder itself) |

## Deliverables

1. 14 singular HTML sources mapped to builder JSON (13 pages + 1 sample post)
2. Documented non-singular architecture for blog + 404
3. `scripts/html-to-builder.mjs` analyzer + validator
4. Setup seeder (idempotent)
5. Template builder-first rendering
6. User guide (English + Persian)

## Related

- PR #9 — Flexible Page Builder foundation (merged, v1.6.0)
- PR #384 — mobile menu / System A templates (overlap noted above)
- PR #400 — shortcode → builder migration for *existing* production content
  (complementary; this PR converts the static HTML prototype)
