# Supplied GSC URLs: phased remediation and acceptance

This follow-up is part of PR #456 on `seo/teznevise-onpage-20261110`. No production batch has been applied. All URL changes below are staged code and reviewed data, with local adapter tests. The four PR checkpoint comments record the controlled phases.

## Decisions

| Decision | Supplied URLs |
| --- | ---: |
| New or flattened 301 | 165 |
| New exact-URL 410 | 4 |
| Narrow robots unblock | 2 |
| Keep current behavior | 216 |
| Restore content | 0 |
| Remove noindex | 0 |
| Total | 387 |

The 216 keep decisions include six unresolved legitimate old intents. They deliberately remain 404 pending evidence, not because 404 is the preferred permanent business outcome. Healthy existing redirects, useful HTML resources, binary downloads, assets, API routes and justified search/private/archive exclusions account for the other keeps. The historical bucket alone is not a reason to change a response.

The exact requested inventory is at repository-root `audit/gsc-indexing-20261110.csv`. Its identical package copy and all decision/target evidence are under `seo/20261110/audit/`. `gsc-redirect-map-20261110.csv` is the complete 165-row old URL/new URL/reason map; `gsc-decisions-20261110.csv` covers every supplied URL, including keep reasons, robots/indexability expectations and unresolved reasons. Twenty-eight destinations were freshly checked as direct 200, self-canonical and indexable. Two destinations are created by the base content package and must exist before GSC redirects activate.

No new content fallback points at the homepage. The existing `http://teznevise.ir/` to HTTPS homepage redirect is retained as host/protocol normalization. Direct redirects replace 13 exact sampled guest-login GET query chains while retaining their already-observed `/inquiry/` destination; bare login, logged-in requests, POST and admin flows remain protected. Six obsolete 404 feed samples point to their maintained HTML parent resources. Literal wildcard samples are exact request strings, never wildcard rewrite rules.

The 2 unblocks allow only `/order/` and `/order/?service` so crawlers can see the existing single-hop 301 to `/inquiry/`. The other current API/admin robots exclusions remain. No supplied public money page currently has a noindex that should be lifted; the noindex bucket contains justified private/search/sort/tag/pagination behavior, so no blanket removal is staged.

## Six unresolved URLs

| URL | Why it remains unresolved | Owner evidence needed |
| --- | --- | --- |
| https://teznevise.ir/translation/ | No genuine matching published translation service or original copy found | Confirm current translation scope and supply original content or a real replacement |
| https://teznevise.ir/translation/scientific-translation/ | Scientific translation cannot be inferred from proofreading or research coaching | Confirm service scope and original copy, or approve retirement |
| https://teznevise.ir/translation/thesis-article-translation/ | No verified thesis/article translation page exists | Supply the original service/content or a substantively equivalent destination |
| https://teznevise.ir/supplementary/thesis-to-book/ | Article extraction is a different service from book conversion | Confirm whether the service belongs and provide original content, or approve retirement |
| https://teznevise.ir/supplementary/subtitle-dubbing/ | No published equivalent or tracked original content found | Confirm current service scope and content, or approve retirement |
| https://teznevise.ir/?page_id=363 | Public REST rejects page/post ID 363; its identity is unknown | Look up the deleted record or archive before choosing restore, redirect or retirement |

Tracked exports and published REST pages were checked for these subjects. The code does not publish invented services, overwrite unknown content or redirect these six to an unrelated page. Their default verification result remains FAIL with the unresolved reason visible, even when the other implemented outcomes pass.

## Batch behavior

The original `apply.sh`/`rollback.sh` owns the base content package and option `teznevise_onpage_20261110`. GSC batches use a separate option, `teznevise_gsc_20261110`, and a fresh immutable private backup directory per batch. Apply merges only the selected reviewed batch. Repeat apply is a no-op; rollback restores the previous raw option/autoload and any touched post/meta or physical robots file. Rollback guards enforce reverse batch order and refuse newer owner edits. Base rollback refuses while GSC batches are active, protecting the new landing-page targets.

`redirects` activates 165 exact source rules. `gone` activates 4 exact retirements, including the obsolete author route and stale plugin/theme samples; no prefix or wildcard expansion occurs. Real files/directories and protected routes remain guarded. `indexing` applies the two reviewed robots exceptions and sitemap policy. If `ABSPATH/robots.txt` is physical, its bytes/mode are backed up before an atomic narrow edit; otherwise the runtime filters the virtual robots response. All other robots rules are retained, and staging privacy remains in force. `links` updates five source-scoped href rules covering seven captured navigation references to the old project-service URL. Two links on private account-recovery pages stay untouched. The earlier full crawl contained no linked 404, so the historical orphan errors do not justify inserting imaginary link edits.

Sitemap filters cover core WordPress, Yoast, Rank Math and custom XML URL blocks. They omit base/GSC redirect sources, gone URLs, protected/query URLs, nonpublished/password content, explicit noindex and non-self canonical URLs. The captured live sitemap contains 404 page entries, which already passed the status/canonical/indexability audit; the guard prevents the new `/service-project/` redirect and any reviewed errors from remaining as page entries. It retains image metadata within valid page entries. No HTTP requests run inside WordPress sitemap filters.

## Exact owner-run apply

First complete the factual content placeholders and review the base runbook in `README.md`. Use staging first. Set paths to the reviewed checkout, an existing WordPress installation, and a new mode-0700 backup root outside all public web roots. These are commands for owner approval; they were not run against production for this PR.

```bash
export SEO_REPO=/absolute/path/to/reviewed/teznevise
export WP_PATH=/absolute/path/to/wordpress
export SEO_BACKUP_ROOT=/absolute/private-parent/teznevise-seo-20261110-staging
export SEO_BACKUP="$SEO_BACKUP_ROOT/base"
mkdir -m 700 -p "$SEO_BACKUP_ROOT"
cd "$SEO_REPO"
python3 scripts/seo-20261110/verify.py
python3 scripts/seo-20261110/verify-gsc.py --check-inventory --allow-unresolved-count 6
php scripts/seo-20261110/test-wordpress.php
php scripts/seo-20261110/test-gsc.php
php scripts/seo-20261110/test-gsc-boot.php
php scripts/seo-20261110/test-gsc-package.php
python3 scripts/seo-20261110/test-runtime-files.py
python3 scripts/seo-20261110/test-verify-gsc.py
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
# After owner approval of the base plan:
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP"
# Each later batch requires its own reviewed dry-run and private backup:
for SEO_BATCH in redirects gone indexing links; do
  bash scripts/seo-20261110/gsc-apply.sh "$WP_PATH" "$SEO_BACKUP_ROOT/$SEO_BATCH" "--batch=$SEO_BATCH" --dry-run
  bash scripts/seo-20261110/gsc-apply.sh "$WP_PATH" "$SEO_BACKUP_ROOT/$SEO_BATCH" "--batch=$SEO_BATCH"
done
```

The planned student/master targets must already be published and owned by the active base package; the GSC redirect and href batches refuse to run if that dependency is missing. Keep editorial changes paused during mutations. A staging database's `home` URL must identify the staging host; the runtime accepts that host for its own internal URLs. The migration never edits `wp-config.php` or `.env`. It requires PHP 8.3+, WP-CLI and InnoDB. Normal plugins/themes are skipped during CLI mutations; MU plugins still load.

Use the owner's configured cache purge after applying or recovering a batch. Targeted WordPress caches are invalidated on graceful transaction failure; a hard process kill can leave external caches stale, so purge the site's configured caches after recovery. Backups and journals remain private and are never overwritten with a second baseline.

## Acceptance and production approval

After the owner-approved staging apply, run `python3 scripts/seo-20261110/verify.py --live https://YOUR-STAGING-HOST`, `python3 scripts/seo-20261110/verify-gsc.py --base-url https://YOUR-STAGING-HOST`, and `python3 scripts/seo-20261110/verify-sitemap.py --sitemap https://YOUR-STAGING-HOST/sitemap_index.xml --require-money-pages`. Private staging noindex is preserved, so an indexability test requires an appropriately controlled indexable preview. Production approval remains a separate owner action. For an approved production apply, repeat the exact sequence with production `WP_PATH` and a fresh backup root ending in `-production`.

```bash
python3 scripts/seo-20261110/verify.py --live https://teznevise.ir
python3 scripts/seo-20261110/verify-gsc.py --output-prefix "$SEO_REPO/work/seo-20261110/owner-gsc-postapply"
python3 scripts/seo-20261110/verify-sitemap.py --require-money-pages --output-prefix "$SEO_REPO/work/seo-20261110/owner-sitemap-postapply"
```

The default GSC verification must show the six unresolved URLs as FAIL until their owner decisions are implemented. Every other failure must be examined against the exact request, status/chain/canonical/robots evidence; do not suppress failures by treating all Search Console exclusions as errors. Snapshot-only mode checks drift against Phase 1 without claiming staged changes are live. The offline allowance accepts exactly six documented unresolved decisions and suppresses no unrelated structural error.

## Exact reverse rollback

Use the same environment's paths and backup directories. Review every dry-run first. Roll back GSC batches in reverse order, then base content, then runtime files:

```bash
cd "$SEO_REPO"
for SEO_BATCH in links indexing gone redirects; do
  bash scripts/seo-20261110/gsc-rollback.sh "$WP_PATH" "$SEO_BACKUP_ROOT/$SEO_BATCH" "--batch=$SEO_BATCH" --dry-run
  bash scripts/seo-20261110/gsc-rollback.sh "$WP_PATH" "$SEO_BACKUP_ROOT/$SEO_BATCH" "--batch=$SEO_BATCH"
done
bash scripts/seo-20261110/rollback.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/rollback.sh "$WP_PATH" "$SEO_BACKUP"
python3 scripts/seo-20261110/runtime-files.py rollback "$WP_PATH" "$SEO_BACKUP" --dry-run
python3 scripts/seo-20261110/runtime-files.py rollback "$WP_PATH" "$SEO_BACKUP"
```

Purge the owner's configured caches and compare the restored URLs and metadata with the captured pre-apply evidence. Keep all immutable baselines and state journals. Do not bypass a newer-edit guard or delete a backup to force a rollback.
