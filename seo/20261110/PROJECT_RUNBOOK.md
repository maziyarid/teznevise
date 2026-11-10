# 84 dead project URLs: reviewed recovery plan

The latest owner instruction is to restore an original page when source content exists in WordPress or the repository, and otherwise redirect to the closest relevant live page/category. This supersedes the earlier proposal to write new replacement software pages. No replacement originals were invented.

## Evidence and counts

GSC Wizard returned all 135 `/project/` page rows for web search from 2026-08-09 through 2026-11-08, with `hasMore: false`. Fresh public GETs confirm 84 initial 404s and 51 single-hop native 301s. Per-URL impressions, clicks and positions are preserved in `audit/project-legacy-inventory-20261110.csv` and its identical package copy. `project-restoration-priority-20261110.csv` ranks the 84 dead URLs by impressions descending, clicks descending, then exact URL; it flags the top 15 without claiming content exists. The API did not establish a settled-through boundary, which is recorded in the source evidence.

Authenticated, read-only WordPress checks covered all 257 non-trash pages, four trashed pages, and public-content metadata across pages, posts, revisions, WPCode and resource records by all GSC slug candidates or legacy project GUID. The only matches were the class-proposal page, the generic engineering-thesis page and GAMS at `/gams/`. None is a missing original for these 84 URLs. A targeted content search also found no original HSPICE/OpenSees page. Repository files and safely redacted historical metadata likewise contain no matched original. See `project-wordpress-source-evidence-20261110.json` and `project-performance-evidence.md`; no raw dump or credentials are included.

| Action for the 84 dead URLs | Count |
| --- | ---: |
| Restore existing original content | 0 |
| Direct 301 to a relevant live destination | 84 |
| Unresolved within this project subset | 0 |

The five destinations were freshly checked as direct 200, self-canonical, crawlable and indexable:

| Destination | Dead URLs mapped |
| --- | ---: |
| `/service-project/` | 47 |
| `/service-simulation/` | 21 |
| `/service-statistics/` | 7 |
| `/service-qualitative/` | 8 |
| `/service-article/` | 1 |

Each URL has its own reason, source evidence and actual performance metrics in `audit/project-dead-decisions-20261110.csv`. There are no homepage fallbacks, wildcard patterns, loops or targets that are another redirect. The 51 healthy native project aliases are preserved. In particular, `/project/` already redirects, so pointing another dead URL there would add a chain; the plan names its final relevant live hub directly.

The original 387-URL indexing review remains intact. Fifty-six project decisions overlap it and are kept consistent; 28 additional dead project URLs produce a deduplicated total of 415 reviewed URLs. Across that combined plan there are 189 redirects, four exact 410s, two robots exceptions and 220 keeps. Six previously documented non-project content/identity gaps remain outside the 84-URL subset.

## Hub, links and homepage

`/service-project/` remains a direct 200 software/topic hub and is no longer included in the base redirect map. Its 1,266-word body distinguishes circuit work, engineering models, EES/OpenSees, econometrics and reproducible code; capability, licensing, pricing and author facts remain explicit owner placeholders. `/student-projects/` retains the broad student-project keyword. Eight relevant pages receive distinct contextual links to the technical hub. The base link plan totals 224 links across 222 sources, including all 54 published posts. Reviewed label changes cover 377 existing exact money anchors and one generic exact homepage anchor; the six-money-target ratio projects to 0%.

The homepage title, H1/hero and 332-word resource introduction are brand/general guidance. No new robots Disallow is added for `?s=`, `/search/` or feeds. Existing noindex/404/410 behavior is retained where justified. The only robots changes remain narrow Allow exceptions for `/order/` and `/order/?service` so their existing redirects can be crawled.

## Project-only apply and rollback

The scripts are delivered for owner review. None of these mutating commands was run against production. PHP 8.3+, WP-CLI and InnoDB are required. Use an absolute private backup directory outside every public web root. It must be new for this reviewed revision; its parent must already exist. Run against staging first and keep editorial changes paused during apply/rollback.

```bash
export SEO_REPO=/absolute/path/to/reviewed/teznevise
export WP_PATH=/absolute/path/to/wordpress
export SEO_BACKUP=/absolute/private-parent/teznevise-seo-20261110-base
export PROJECT_BACKUP=/absolute/private-parent/teznevise-seo-20261110-project
cd "$SEO_REPO"
python3 scripts/seo-20261110/verify-projects.py --check-map
python3 scripts/seo-20261110/verify-projects.py --snapshot-only
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP" --dry-run
# After owner approval of the reviewed plan:
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP"
python3 scripts/seo-20261110/verify-projects.py --base-url https://YOUR-STAGING-HOST
```

The project-only batch changes one backed-up option holding the 84 exact redirects. It does not alter posts or robots files when no original content is restored. Every original option value/autoload or absence is captured before mutation, with immutable checksums and a journal. Repeated apply is a no-op; rollback refuses newer option edits or a backup for a different batch. The optional source-backed restore machinery is tested but no restoration catalog is instantiated for this plan.

To apply the complete SEO work, use the base content apply and the other reviewed GSC batches in the order below, with a separate private directory for every batch:

```bash
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP" --dry-run
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP"
for SEO_BATCH in redirects gone indexing links; do
  bash scripts/seo-20261110/gsc-apply.sh "$WP_PATH" "/absolute/private-parent/teznevise-seo-20261110-$SEO_BATCH" "--batch=$SEO_BATCH" --dry-run
  bash scripts/seo-20261110/gsc-apply.sh "$WP_PATH" "/absolute/private-parent/teznevise-seo-20261110-$SEO_BATCH" "--batch=$SEO_BATCH"
done
```

Review each dry-run before its apply. Complete the six money-page and technical-hub fact placeholders before public publication. The base apply is required for the new money pages, the hub copy, the homepage and contextual links. Its backup shares the runtime install directory safely through separate backup filenames. The optional empty `restores` batch is unnecessary for this zero-restoration decision set.

Rollback later GSC batches first, in reverse order, before the project batch and base content. The generic rollback script reads and validates the batch identity from its own backup:

```bash
for SEO_BATCH in links indexing gone redirects; do
  bash scripts/seo-20261110/gsc-rollback.sh "$WP_PATH" "/absolute/private-parent/teznevise-seo-20261110-$SEO_BATCH" --dry-run
  bash scripts/seo-20261110/gsc-rollback.sh "$WP_PATH" "/absolute/private-parent/teznevise-seo-20261110-$SEO_BATCH"
done
bash scripts/seo-20261110/project-rollback.sh "$WP_PATH" "$PROJECT_BACKUP" --dry-run
bash scripts/seo-20261110/project-rollback.sh "$WP_PATH" "$PROJECT_BACKUP"
bash scripts/seo-20261110/rollback.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/rollback.sh "$WP_PATH" "$SEO_BACKUP"
python3 scripts/seo-20261110/runtime-files.py rollback "$WP_PATH" "$SEO_BACKUP" --dry-run
python3 scripts/seo-20261110/runtime-files.py rollback "$WP_PATH" "$SEO_BACKUP"
```

For a project-only apply, skip the loops and base data rollback: review `project-rollback.sh --dry-run`, roll back that batch, and restore runtime files only if no other package is using them. Use the owner's configured cache purge after apply/rollback or recovery. Do not bypass a newer-edit guard. No script edits or copies `wp-config.php` or `.env`.

## Verification

`verify-projects.py --check-map` validates exact 135/84 cohort membership, all 84 decisions, source evidence, safe direct targets and absence of chains/homepage fallbacks. Default live mode re-fetches every dead project URL and reports each expected 301 or source-backed restoration as PASS/FAIL. `--snapshot-only` checks the current pre-apply 404 baseline; it is not a claim that redirects are active. `--base-url` tests the owner's staging origin without changing the production evidence.

The shared `verify-gsc.py` checks the original 387 supplied indexing URLs and can check the merged 415-row inventory; `verify-sitemap.py --require-money-pages` checks every sitemap page as direct 200, self-canonical, crawlable and indexable and requires all canonical home/money/hub URLs after apply. Before approved apply, the new 84 redirects are expected to fail their post-apply acceptance checks because production is intentionally unchanged.
