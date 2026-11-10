# Teznevise staged on-page SEO — 2026-11-10

This package is delivered in draft PR #456 on `seo/teznevise-onpage-20261110`. It has not been merged or deployed. No production page, option, redirect, robots file, `wp-config.php` or `.env` was changed.

## Content and keyword ownership

`manifest.json` is authoritative for titles, descriptions, H1s, roles and content files. Six distinct money pages own the six requested broad keywords; a brand/resource homepage reduces competition for student projects. The existing `/service-project/` stays a separate 200 technical hub rather than redirecting to `/student-projects/`.

| Path | Role | New visible words |
| --- | --- | ---: |
| `/thesis/` | انجام پایان نامه | 3,247 |
| `/thesis/master/` | انجام پایان نامه کارشناسی ارشد | 3,257 |
| `/thesis/phd/` | انجام پایان نامه دکتری | 3,414 |
| `/proposal/` | انجام پروپوزال | 3,663 |
| `/proposal/phd/` | انجام پروپوزال دکتری | 3,373 |
| `/student-projects/` | انجام پروژه های دانشجویی | 3,787 |
| `/service-project/` | Technical software/topic hub | 1,266 |
| `/` | Brand/resource introduction | 332 |

Money and hub pages include process, pricing factors, author/reviewer details and six visible FAQs with exactly one matching FAQPage block. Unknown author qualifications, actual prices, supported capabilities/licenses and policies are marked `[نیازمند تکمیل مالک: ...]`; complete those facts before publication. No statistics, credentials, testimonials or prices were invented. Authored Persian uses ordinary spaces, Persian prose digits and no U+200C. The guide preserves student authorship and educational support.

The content loader replaces stale builder output on managed pages, avoids duplicate footer content, owns the H1 and suppresses competing theme/Yoast/Rank Math FAQ nodes. Homepage hero/theme-mod filters and a body fallback support both the repository theme and observed live preview drift. Source tool/download/blog bodies are not rewritten: the runtime adds visible server-rendered contextual links.

## Evidence and decisions

- `repository-choice.md`: public and authenticated read-only evidence that `maziyarid/teznevise` is the Teznevise code source. The live preview is 1.9.35 while this checkout is 1.9.31, so install only the standalone runtime until the owner reconciles a full theme release.
- `audit/crawl-report.md`: original 1,018-URL sitemap/internal-link/resource crawl with statuses, redirect targets, robots review and exact snapshots. It found no linked/sitemap 404; later supplied historical/orphan URLs are handled separately.
- Root `audit/gsc-indexing-20261110.csv` and identical package copy: all 387 supplied indexing samples, preserving exact queries and literal wildcard requests.
- Root `audit/project-legacy-inventory-20261110.csv`: all 135 real GSC performance URLs, freshly checked as 84 current 404s and 51 native single-hop 301s. Source JSON, metric ranking and WordPress/repo source checks are included.
- Root `audit/project-dead-decisions-20261110.csv`: 84 exact source-backed decisions: zero restorations, 84 direct topical 301s, zero unresolved project URLs. No matched original content was recoverable in authenticated WordPress or repo/history; no replacement original was fabricated.
- `audit/reviewed-redirect-map-20261110.csv`: all 191 reviewed rules, comprising 189 deduplicated GSC redirects plus the two required base aliases `/service-thesis/` and `/service-proposal/`.
- `audit/internal-links.csv`: 224 contextual links across 222 sources, retaining all 54 blog posts plus tools/downloads and adding eight unique-source links to the live technical hub. Reviewed label changes cover 377 existing money exact matches and one generic exact homepage anchor; the projected money ratio is 0%.
- `audit/bluethesis-links.csv`: all 13 statistical-analysis anchors across 12 source pages. Retained links gain nofollow; replaced content may remove an old reference. `cannibalisation.md` recommends separate owner changes to the other domains; no external site is edited.
- `audit/before-after.csv`: before/proposed counts, titles, H1s and measurement notes for all eight pages.

The combined review covers 415 unique URLs: 189 redirect decisions, four exact 410s, two narrow robots Allows and 220 keeps. The original 387 and project 84 scopes overlap by 56; 28 new dead project URLs extend the original set. Six unresolved non-project content/identity gaps remain explicitly documented in `GSC_RUNBOOK.md` and are not part of the completed 84-URL subset.

## Apply, rollback and verification

Use [PROJECT_RUNBOOK.md](PROJECT_RUNBOOK.md) for exact owner-run installation, project-only apply/rollback and the complete batch sequence. [GSC_RUNBOOK.md](GSC_RUNBOOK.md) describes scope, protected routes, exceptions and verification. Four runtime files are backed up/installed through `runtime-files.py`; normal behavior stays dormant until the reviewed options are applied. The owner should review completed factual placeholders and staging verification before any public installation.

All changed post fields/meta, raw options/autoload or absence, and any changed physical robots bytes/mode are backed up first in immutable mode-0700 directories outside web roots. InnoDB transactions, site locks, checksummed journals, exact metadata restoration and newer-edit/reverse-order guards are tested. A project-only batch does not modify robots or posts for this zero-restoration plan. Scripts never edit or copy `wp-config.php` or `.env`; no full old-theme deployment is needed.

No robots block is added for search queries, `/search/` or feeds. Only `/order/` and `/order/?service` gain narrow Allows so their existing 301 can be crawled. Sitemap filters and the standalone verifier enforce direct 200, self-canonical, crawlable/indexable page entries while preserving valid image extensions. Site-wide staging privacy is retained.

Offline checks include actual eight-page word/schema/keyword/anchor contracts, all 54 real post IDs, 135/84 performance coverage, exact source evidence, redirect graph safety and full reviewed-package dry-run/apply/repeat/reverse rollback through isolated WordPress/SQL adapters. PHP lint, shell syntax, release guards and frontend bundle drift checks are run. These are not a production WordPress/plugin-stack apply; the read-only acceptance reports explicitly distinguish pending approved changes from actual live behavior.

The pre-existing Vercel integration attempted and failed an automatic preview for the first PR commit. `vercel.json` disables automatic deployment for this SEO branch; no preview or production deployment was performed.
