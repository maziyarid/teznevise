- Stage six distinct Persian money pages (3,247–3,787 words) and brand-focused homepage metadata, hero and body copy.
- Review a 1,018-URL live crawl, every observed redirect/404/robots rule, and a three-row 301 map.
- Add 216 source-page links covering all 54 posts and vary 361 existing exact-match anchors to project 0%.
- List all 13 BlueThesis anchors on 12 pages and document external-domain de-optimization recommendations only.
- Provide dormant runtime, backup-first idempotent WP-CLI apply/rollback, dry runs, tests and owner approval steps; no merge or deploy.

## Supervised follow-up

The supplied URL-level Search Console lists are being processed in four controlled phases in this same PR: live inventory, per-URL decisions, staged backup-first apply batches plus link/sitemap corrections, then exhaustive verification. A checkpoint comment will record each completed phase. Production remains unchanged under the no-deploy instruction.

## Source and search context

Use **maziyarid/teznevise**, branch `seo/teznevise-onpage-20261110`. The live site loads the Teznevise preview theme; its public theme header, assets and builder architecture match this repository. `maziyarid/TeznevisanTheme` has a different theme structure and receives no PR. Live version 1.9.35 differs from repository 1.9.31, so the exact installation below installs only the reviewed standalone module rather than overwriting the full active theme.

Owner-supplied Search Console facts, Aug 9–Nov 8 2026: 549 clicks, 16,661 impressions; traffic mainly calculators/downloads. Money pages: thesis position 21 / 0 clicks, thesis/phd 77, proposal 7.7 / 20 impressions, proposal/phd 25, service-project 39.7; plain «انجام پایان نامه» absent. Coverage: 617 indexed, 182 not found, 47 robots blocked, 71 redirects, 22 noindex. These are supplied aggregates, not downloaded URL-level exports.

## Reviewed redirect map

| Old URL | New URL | Reviewed reason |
| --- | --- | --- |
| https://teznevise.ir/service-thesis/ | https://teznevise.ir/thesis/ | Reviewed 2026-11-10: legacy service alias; live crawl already confirms a single 301 to the canonical thesis page. Keep and enforce idempotently. |
| https://teznevise.ir/service-proposal/ | https://teznevise.ir/proposal/ | Reviewed 2026-11-10: legacy service alias; live crawl already confirms a single 301 to the canonical proposal page. Keep and enforce idempotently. |
| https://teznevise.ir/service-project/ | https://teznevise.ir/student-projects/ | Reviewed 2026-11-10: existing 200 page covers student engineering and university projects, the same intent as the new broader student-projects landing. Consolidate only after target is created and verified. |

The two required aliases already return single-hop 301 on the live site. The third rule consolidates the old project offer after creating the new page. The current `/thesis/master/` 301 ends at `/download/masters-research-methods-guide/`; creating the real hierarchical page should resolve WordPress's missing-page guess, and rendered verification must confirm 200. All six observed redirect sources and final targets are in `seo/20261110/audit/redirect-chains.csv`.

The exhaustive public sitemap/internal-link/resource crawl checked 1,018 URLs with zero fetch errors. No existing linked/sitemap URL currently returned 404. `/student-projects/` is the sole 404, an explicit requested-new-page probe resolved by creation. `internal-404s.csv` records that decision. This evidence cannot enumerate the 182 historical/orphan Search Console 404s without the owner's URL export; no invented redirect targets or blanket homepage 301s were added.

`robots-rules.csv` reviews every fetched Allow/Disallow rule. `Disallow: /order/` blocks a useful 301 alias to `/inquiry/`; all six canonical money paths are allowed. Exact robots/sitemap snapshots and hashes are included. The runbook specifies a narrow owner review of that rule; the migration does not rewrite unknown physical robots files or plugin settings.

## Before and proposed content

| URL | Before words | After words | Before title | Proposed SEO title |
| --- | ---: | ---: | --- | --- |
| `/` | 235 | 332 | انجام پروژه دانشجویی \| انجام پروژه های دانشگاهی - تزنویسه | تزنویسه \| راهنمای پژوهش دانشگاهی، ابزار و منابع آموزشی |
| `/thesis/` | 2,207 | 3,247 | انجام پایان نامه \| مسیر مرحله به مرحله پژوهش \| تزنویسه | انجام پایان نامه \| راهنمای پژوهش و مشاوره تزنویسه |
| `/thesis/master/` | 0 | 3,257 | کتابچه روش تحقیق برای پایان نامه کارشناسی ارشد \| تزنویسه | انجام پایان نامه کارشناسی ارشد \| مسیر پژوهش تزنویسه |
| `/thesis/phd/` | 1,828 | 3,414 | انجام رساله دکتری \| طراحی پژوهش، سهم علمی و دفاع | انجام پایان نامه دکتری \| راهنمای روش، هزینه و دفاع \| تزنویسه |
| `/proposal/` | 1,607 | 3,663 | انجام پروپوزال ارشد و دکتری \| روش تحقیق - تزنویسه | انجام پروپوزال \| مراحل، هزینه راهنمایی و بازبینی \| تزنویسه |
| `/proposal/phd/` | 820 | 3,373 | انجام پروپوزال دکتری \| سؤال پژوهش، سهم علمی و روش - تزنویسه | انجام پروپوزال دکتری \| مراحل، روش و برآورد هزینه \| تزنویسه |
| `/student-projects/` | 0 | 3,787 | صفحه پیدا نشد - تزنویسه | انجام پروژه های دانشجویی \| آموزش و رفع اشکال \| تزنویسه |

Counts are visible whitespace-delimited content words, excluding scripts, JSON-LD and site chrome. Existing counts use the largest article/longcopy surface; proposed counts use the authored full body including headings/FAQs. The homepage's 235-word student-project longcopy becomes 332 words of brand/resource guidance; its title, description and hero also change. The new master landing has 0 existing service words; its observed redirected guide has 512 words and remains a separate download. The 605-word `/service-project/` intent is consolidated into the new 3,787-word student page, including engineering, digital-circuit and simulation guidance. `audit/before-after.csv` also records proposed H1s and method notes.

The manifest assigns each of the six requested queries one canonical target. `/thesis/phd/` now targets «انجام پایان نامه دکتری». Each money page includes tailored process and pricing factors, six visible FAQs and one exactly matching FAQPage, author/reviewer/E-E-A-T fields and explicit owner fact placeholders. No statistics, prices, testimonials or credentials were invented; prose uses Persian digits and no U+200C.

## Links and cross-site recommendations

`internal-links.csv` adds one server-rendered contextual continuation link on 216 sources: all 54 published posts (29 Persian / 25 English), 61 tool/calculator/hub pages and 101 download/hub pages. All 216 added anchors are descriptive and distinct, with 0 exact matches. Existing money-page anchors were 361/1,910 exact matches; additions alone would leave 16.98%. The 329 reviewed rewrite rules cover all 361 existing occurrences, projecting 0/2,126 = 0% in the captured combined corpus. Live verification measures both existing and added links and fails above 5%.

`bluethesis-links.csv` lists all 13 statistical-analysis anchors on 12 teznevise source pages; chapter-four has two. Retained links receive nofollow while preserving rel tokens; the replaced PhD body removes its old link. `cannibalisation.md` gives observed titles/canonicals and precise de-optimization recommendations for bluethesis.ir/proposal and the thin official-site money pages, with cross-domain canonicalization only as an alternative after content equivalence is established. No other domain is edited.

## Validation and operating limits

- Offline word counts, Persian digits/spaces, six sole keyword owners, FAQ-visible-text equality, all 54 post IDs, redirect validation and combined anchor calculations pass.
- PHP contract tests cover backup-first dry runs, idempotence, exact raw metadata/option restoration, transactions/failure recovery, checksum checks, newer edits/comments/child guards, rendered links/nofollow/anchor rewrites and FAQ graph preservation.
- Aligned two stale README version labels with the existing 1.9.31 constant so the repository release guard passes; no runtime version or release was bumped.
- Seven filesystem tests cover dormant-runtime install/restore, original modes, no-write dry runs, tampering and newer-edit refusal. PHP/shell syntax and existing release/classic-content checks pass.
- Database tests use isolated WordPress/SQL adapters. A live WordPress/plugin-stack apply has **not** been performed. Staging `--live` verification is an explicit owner acceptance step.

Every changed post and all its metadata are backed up first; the only changed option holds the runtime and redirect overlay and is backed up including prior absence/autoload. Existing plugin redirect stores are not changed. Source posts use reversible render-time links instead of body mutations. InnoDB transactions and locks guard writes; rollback refuses newer work. No wp-config.php or .env is modified or copied into backups.

## Exact owner-approved installation/apply and rollback

Complete all factual placeholders in the six HTML files before public publication. Edit visible FAQ answers and their JSON-LD together. Confirm real author/reviewer names, verified relevant qualifications, date of review, service scope, price basis and policies; do not replace placeholders with unsupported claims. Re-run verification after any edit.

The live theme reports 1.9.35 while this repository reports 1.9.31. Install only the standalone runtime through the supplied MU-plugin installer; do not replace the full production theme with an older checkout. The installer backs up the three exact runtime files before writing, preserves pre-existing bytes/modes on rollback, refuses newer file edits, and is idempotent. Its module is inert until apply activates the option. The theme require in this PR also works when the owner reconciles a future full theme update; a declaration guard prevents duplicate loading.

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

The robots audit identifies `Disallow: /order/` blocking a useful legacy redirect. This PR records the exact rule and recommends allowing only that alias. The apply script does not edit a physical robots file or unknown SEO-plugin robots setting. Retain administrative/private/parameter protections. The canonical six money pages are already allowed. Review and approve the narrow robots correction separately in the file or plugin that actually owns it.

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


No merge, deploy, production WP-CLI call, external-site edit or cache purge has been executed by this PR task.
