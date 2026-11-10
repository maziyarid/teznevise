# Teznevise on-page SEO package — 2026-11-10

This is a staged change for owner review. It has not been applied, merged or deployed. `manifest.json` is the authoritative keyword, H1, title, description and content-file map. Six money-page HTML files hold the complete Persian copy and matching FAQ schemas; `content/home.html` holds the brand/resource introduction for the existing homepage.

## Scope and evidence

The code source is `maziyarid/teznevise`; see `repository-choice.md` for the live theme evidence and observed production version drift. The public crawl checked 1,018 URLs and found no currently linked or sitemap 404. The requested new `/student-projects/` page is currently absent. The historical Search Console totals are owner-supplied aggregates, not an exported URL list; this PR does not claim to resolve 182 unenumerated historical URLs.

- `audit/redirect-map.csv`: three reviewed 301 rules, including the two required aliases and project-intent consolidation.
- `audit/url-status.csv`, `redirect-chains.csv`, `internal-404s.csv`: every observed status, redirect destination and 404 decision.
- `audit/robots-rules.csv`, `robots-blocked-urls.csv`, `snapshots/`: exact robots/sitemap evidence and useful blocked alias `/order/`.
- `audit/internal-links.csv`: 216 topic-matched continuation links, covering all 54 published posts, 61 tool/calculator/hub pages and 101 download/hub pages.
- `audit/exact-anchor-rewrites.csv`: 329 rules cover 361 existing exact-match anchors. The captured existing-plus-added corpus projects to 0% exact matches after these changes; live verification checks the actual ratio against the 5% ceiling.
- `audit/bluethesis-links.csv`: all 13 statistical-analysis anchors on 12 source pages, with nofollow actions. The replaced PhD page removes its old reference.
- `audit/cannibalisation.md`: separate recommended title, H1, copy and canonical decisions for the other domains. This package never edits those sites.
- `audit/before-after.csv`: measured content counts and before/proposed titles. New master landing has zero independent service words; its current redirect ends at a 512-word download guide. Homepage longcopy changes from a 235-word student-project introduction to 332 words of brand/resource guidance; its title, description and hero also change.

The copy uses natural Persian, Persian prose digits and ordinary spaces. Author, reviewer, credentials, actual prices, supported services and business policies are explicitly marked `[نیازمند تکمیل مالک: ...]` where not supplied. No credentials, prices, reviews or statistics were invented. The copy describes educational guidance with student authorship.

## What apply changes

The migration creates or updates six money pages, including the hierarchical master page, and updates the existing static homepage, and backs up each original full post row and all original postmeta before any database mutation. It writes Yoast and Rank Math metadata and stores the page FAQ separately from sanitized HTML. The homepage keeps its slug, parent and template; its classic-editor introduction becomes general resource guidance. The runtime overrides stale builder rendering on the six money pages, supplies one H1 and one FAQPage, removes the theme's old FAQ emitter, and filters competing Yoast/Rank Math FAQ nodes.

One non-autoloaded option, `teznevise_onpage_20261110`, holds the activated runtime configuration, redirects, homepage metadata/hero, links and anchor rewrites. Its original raw value and autoload state, including absence, are backed up. The redirect overlay is restored through that same option; existing third-party redirect tables are never changed. Read-only snapshots of known plugin redirect options are diagnostic evidence, not a claim to back up their custom database tables. The original `/service-thesis/` and `/service-proposal/` theme aliases already return 301; the package enforces the same canonical destinations.

Source blog, calculator and download posts remain in their original database state. The runtime inserts one visible server-rendered continuation link before `</main>` (or `</body>` fallback), updates only reviewed exact anchor labels on scoped sources, and adds nofollow to matching external anchors. Thus their original bodies, download actions and builder metadata require no mutation or restore. The new student page retains engineering, digital-circuit and simulation guidance from the old service intent.

Apply and rollback require InnoDB posts/postmeta/options, an advisory site lock and a private mode-0700 backup directory outside the WordPress root. A transaction protects database changes; the immutable baseline and checksum precede writes, with a journal before commit. Repeating an unchanged apply is a no-op. Rollback restores original post/meta values and the option, removes only unchanged package-created pages, and refuses newer edits, comments, revisions, attachments or children. After a completed rollback, use a fresh private directory for a new apply. Keep backups private; they contain original content and metadata.

WP-CLI wrappers use `--skip-plugins --skip-themes` to avoid normal plugin/theme mutation hooks. MU plugins still load as WordPress requires. The scripts do not edit, copy or include credentials from `wp-config.php` or `.env` in backups, flush rewrites, purge caches, or deploy a theme.

## Owner-approved installation and apply

Complete all factual placeholders in the six HTML files before public publication. Edit visible FAQ answers and their JSON-LD together. Confirm real author/reviewer names, verified relevant qualifications, date of review, service scope, price basis and policies; do not replace placeholders with unsupported claims. Re-run verification after any edit.

The live theme reports 1.9.35 while this repository reports 1.9.31. Install only the standalone runtime through the supplied MU-plugin installer; do not replace the full production theme with an older checkout. The installer backs up the four exact runtime files before writing, preserves pre-existing bytes/modes on rollback, refuses newer file edits, and is idempotent. Its module is inert until apply activates the option. The theme require in this PR also works when the owner reconciles a future full theme update; a declaration guard prevents duplicate loading.

Use a staging clone first. Set the following absolute paths to the reviewed checkout, the intended WordPress root and a new private backup directory. The backup parent must already exist outside all public web roots. Keep editorial changes paused while apply or rollback runs.

```bash
export SEO_REPO=/absolute/path/to/reviewed/teznevise
export WP_PATH=/absolute/path/to/wordpress
export SEO_BACKUP=/absolute/private-parent/teznevise-seo-20261110-staging
cd "$SEO_REPO"
python3 scripts/seo-20261110/verify.py
php scripts/seo-20261110/test-wordpress.php
python3 scripts/seo-20261110/test-runtime-files.py
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
# After the owner approves the dry-run plan:
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP"
python3 scripts/seo-20261110/verify.py --live https://YOUR-STAGING-HOST
```

Use PHP 8.3 or later, Python 3.10 or later, WP-CLI, and MySQL/MariaDB with InnoDB. The offline verifier needs only Python's standard library; reproducing the crawl additionally needs `lxml`.

The captured `/thesis/master/` redirect has `X-Redirect-By: WordPress` and is consistent with core's missing-page permalink guess. Creating the real hierarchical page should end that guess. The live verifier requires direct 200 at `/thesis/master/`; if a server/CDN or explicit plugin rule still redirects it, stop and inspect that rule on staging. This package does not silently edit unknown redirect stores or server rules. Back up any separately approved rule before removing it; never leave the new page redirecting to a download.

The robots audit identifies `Disallow: /order/` blocking a useful legacy redirect. The follow-up GSC indexing batch now implements the reviewed narrow exceptions for `/order/` and `/order/?service`, backing up a physical robots file or filtering the virtual response. Administrative/private/parameter protections remain. The canonical six money pages are already allowed. See `GSC_RUNBOOK.md` for the exact batch order and reverse rollback.

After staging verification and owner approval, repeat the same dry-run/install/apply commands with production `WP_PATH` and a fresh `SEO_BACKUP` such as `/absolute/private-parent/teznevise-seo-20261110-production`. Run `python3 scripts/seo-20261110/verify.py --live https://teznevise.ir`. If site/CDN caches exist, use the owner's configured cache purge, then rerun the live check; no cache credentials or purge assumptions are embedded here. Confirm the 301s are single-hop and the new master page is 200, each money page has one H1/description/self-canonical/FAQPage, FAQ answers match visible copy, all 216 source pages render the planned links, the combined exact-match ratio is at most 5%, and all retained BlueThesis references are nofollow. A private staging site may deliberately be noindex; its noindex check will fail until tested in an indexable preview, and the runtime preserves the global staging privacy setting.

Only after these owner-run steps should updated canonical sitemaps be submitted in Search Console. Obtain the historical 404/blocked/noindex URL exports for a separate evidence-based review; never infer 182 redirect targets from aggregate totals.

## Exact rollback

Use the same WordPress root and private backup directory from that environment's apply. Keep the reviewed checkout available. Roll back data before removing the runtime:

```bash
cd "$SEO_REPO"
bash scripts/seo-20261110/rollback.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
python3 scripts/seo-20261110/runtime-files.py rollback "$WP_PATH" "$SEO_BACKUP" --dry-run
# After the owner reviews the restore plan:
bash scripts/seo-20261110/rollback.sh "$WP_PATH" "$SEO_BACKUP"
python3 scripts/seo-20261110/runtime-files.py rollback "$WP_PATH" "$SEO_BACKUP"
```

Restore/purge the owner's configured caches after rollback and inspect the original URLs and titles against `audit/before-money-pages.csv`. The pre-existing legacy alias redirects should remain. The newly created pages should return to their original absent/guessed state. The runtime-file installer removes files that did not exist before installation and restores pre-existing files exactly; an empty package directory may remain. No merge revert, configuration edit or full-theme deployment is required for this scoped rollback. Retain the immutable backup and journal. If a newer-edit guard fires, inspect the specific saved and current values; do not bypass it by deleting backups.

## Verification performed for this PR

Offline content/schema/keyword/redirect/anchor checks pass. The six pages contain 3,247, 3,257, 3,414, 3,663, 3,373 and 3,787 visible words, excluding JSON-LD. Every page has six matching visible FAQs and exactly one authored FAQPage block. All 54 actual post IDs are covered. The combined captured exact-match anchor projection is 0%, including existing labels, rather than an additions-only calculation.

PHP tests use isolated WordPress/SQL adapters to verify backups, exact raw metadata restoration, dry runs, repeat apply/rollback, transactions, checksum rejection and newer-edit/data guards. They also verify rendered link/nofollow/rewrite idempotence, homepage hooks and FAQ graph preservation. Seven isolated filesystem tests verify runtime installation/restoration, modes, no-write dry runs and overwrite refusal. PHP and shell syntax checks pass. Existing repository release/classic-content checks are also run before PR creation. No production WordPress database, real SEO-plugin installation or deployment was used for testing; the supplied `--live` verification is the owner-run rendered acceptance check.

## Supervised GSC follow-up

See [GSC_RUNBOOK.md](GSC_RUNBOOK.md) for all 387 URL decisions, six explicit unresolved intents, staged batch apply/rollback, internal href cleanup, sitemap guards and full live verification. Its reverse GSC rollback must precede the base rollback above.
