- Stage all 84 dead project URLs as individually reviewed direct 301s: 0 source-backed restorations, 84 redirects, 0 unresolved project decisions.
- Preserve the 200 technical project hub, add eight contextual hub links, and keep the broad student-project keyword on its own landing page.
- Deliver six Persian money pages of 3,247–3,787 words, refreshed homepage metadata, and the complete reviewed 191-rule redirect map.
- Include real GSC metrics for all 135 legacy project URLs, the 387 supplied indexing checks, and a deduplicated 415-URL decision and verification scope.
- Provide backup-first, idempotent WP-CLI apply/rollback, no-write dry runs and verification; keep this PR draft with no merge, deploy or production change.

## Source and evidence

Use **maziyarid/teznevise**, branch `seo/teznevise-onpage-20261110`, in this existing PR only. Public theme assets and authenticated read-only WordPress information identify the active Teznevise preview theme. `maziyarid/TeznevisanTheme` has a different structure and is not used. Live theme version 1.9.35 differs from this repository's 1.9.31; the supplied installer installs only four reviewed standalone MU runtime files, without replacing the active theme. See [repository choice](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/repository-choice.md).

GSC Wizard successfully returned all **135 distinct `/project/` performance rows**, with `hasMore: false`, for web search on `sc-domain:teznevise.ir`, August 9–November 8, 2026. Fresh read-only checks confirm **84 initial 404s and 51 single-hop native 301s ending at 200**. The latter remain intact. Per-URL impressions, clicks and positions are preserved; the top 15 dead routes are ranked by measured impressions. The API did not establish a finalized-data boundary or a filtered cohort total, so neither is invented. [Raw sanitized GSC response](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/project-gsc-performance-20261110.json), [135-URL inventory](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/audit/project-legacy-inventory-20261110.csv), [ranked 84](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/audit/project-restoration-priority-20261110.csv).

Authenticated **read-only** WordPress checks enumerated all 257 non-trash pages and four trashed pages, plus public-content metadata across posts, revisions, WPCode and resource records using the 135 slug candidates and legacy project GUIDs. Repository files, available history and controlled redacted historical metadata were checked too. No original matched any of the 84 missing pages. Existing class-proposal, engineering-thesis and GAMS matches are different/live pages. Under the owner's source-backed restoration rule, all 84 therefore receive relevant direct 301s; no software article or original content is fabricated. [Sanitized WordPress evidence](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/project-wordpress-source-evidence-20261110.json), [method and priority report](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/audit/project-performance-evidence.md). No credentials, raw database dumps or unpublished bodies are included.

The owner-supplied site totals are 549 clicks and 16,661 impressions, with traffic concentrated in calculators and downloads. Money-page positions: thesis 21 with zero clicks; thesis/phd 77; proposal 7.7 on 20 impressions; proposal/phd 25; service-project 39.7. The supplied indexing totals were 617 indexed, 182 not found, 47 robots blocked, 71 redirects and 22 noindex. These aggregates are not substituted for the per-URL inventories.

## Decisions and redirect map

| Scope | New/direct 301 decisions | New 410 | Robots Allow exceptions | Keep | Restorations | Unresolved |
| --- | ---: | ---: | ---: | ---: | ---: | ---: |
| Original 387 supplied indexing URLs | 161 | 4 | 2 | 220 | 0 | 6 non-project |
| 84 dead project URLs | 84 | 0 | 0 | 0 | 0 | 0 |
| Deduplicated combined 415 URLs | 189 | 4 | 2 | 220 | 0 | 6 non-project |

Fifty-six project rows overlap the original indexing list; 28 extend it. These scopes must not be added directly. The immutable requested live snapshot remains [audit/gsc-indexing-20261110.csv](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/audit/gsc-indexing-20261110.csv), recording status, final URL, canonical, robots meta/header and effective robots rule for every exact supplied URL. Literal wildcard samples are not expanded and query identities are retained.

The [complete reviewed redirect map CSV](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/reviewed-redirect-map-20261110.csv) contains **191 exact rules**: the 189 deduplicated GSC decisions plus these two required aliases. Every CSV row gives old URL, new URL, reason, batch and evidence source.

| Required old URL | Direct target |
| --- | --- |
| `/service-thesis/` | `/thesis/` |
| `/service-proposal/` | `/proposal/` |

`/service-project/` remains **200 and self-canonical**. It is the narrower technical/software hub; `/student-projects/` owns the generic money query. No dead URL points to the homepage, another redirect source or a loop. `/project/` itself redirects already, so the map uses relevant live final hubs directly. [Five destination proofs](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/audit/project-dead-targets-20261110.csv) show direct 200, self-canonical, crawlable, indexable pages.

| Project destination | Dead URLs |
| --- | ---: |
| `/service-project/` | 47 |
| `/service-simulation/` | 21 |
| `/service-statistics/` | 7 |
| `/service-qualitative/` | 8 |
| `/service-article/` | 1 |

<details>
<summary>All 84 exact project mappings (CSV includes individual reasons, metrics and source evidence)</summary>

| Original path | Action | Direct target |
| --- | --- | --- |
| `/project/assignments/business-management/business-administration/human-resources/` | 301 | `/service-project/` |
| `/project/assignments/business-management/business-administration/strategic-management/` | 301 | `/service-project/` |
| `/project/assignments/business-management/business-administration/supply-chain-optimization-models/` | 301 | `/service-project/` |
| `/project/assignments/business-management/business-administration/swot-analysis/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/algorithm-complexity-analysis-reports/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/artificial-intelligence/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/data-structures/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/database-management/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/operating-systems/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/sql-database-schema-designs/` | 301 | `/service-project/` |
| `/project/assignments/computer-science/uml-diagrams/` | 301 | `/service-project/` |
| `/project/assignments/electrical-electronics/circuit-simulation-files-spice/` | 301 | `/service-project/` |
| `/project/assignments/electrical-electronics/pcb-layout-designs/` | 301 | `/service-project/` |
| `/project/assignments/electrical-electronics/signal-processing/` | 301 | `/service-project/` |
| `/project/assignments/mechanical-civil/cad-modelling-autocad/` | 301 | `/service-project/` |
| `/project/assignments/mechanical-civil/finite-element-analysis-fea-reports/` | 301 | `/service-simulation/` |
| `/project/assignments/mechanical-civil/fluid-mechanics/` | 301 | `/service-simulation/` |
| `/project/assignments/mechanical-civil/geotechnical-engineering/` | 301 | `/service-simulation/` |
| `/project/assignments/mechanical-civil/structural-load-calculations/` | 301 | `/service-simulation/` |
| `/project/assignments/web-software/api-integration-scripts/` | 301 | `/service-project/` |
| `/project/assignments/web-software/cms-frameworks/` | 301 | `/service-project/` |
| `/project/assignments/web-software/ux-ui-design/` | 301 | `/service-project/` |
| `/project/engineering/chemical-industrial/chemical-engineering-project/` | 301 | `/service-project/` |
| `/project/engineering/chemical-industrial/chemistry-project/` | 301 | `/service-project/` |
| `/project/engineering/chemical-industrial/econometrics-project/` | 301 | `/service-statistics/` |
| `/project/engineering/chemical-industrial/industrial-engineering-project/` | 301 | `/service-project/` |
| `/project/engineering/chemical-industrial/storyline/` | 301 | `/service-project/` |
| `/project/engineering/electrical/ees/` | 301 | `/service-project/` |
| `/project/engineering/electrical/vensim/` | 301 | `/service-simulation/` |
| `/project/engineering/mechanical-civil/aerospace-project/` | 301 | `/service-project/` |
| `/project/engineering/mechanical-civil/designbuilder/` | 301 | `/service-simulation/` |
| `/project/engineering/mechanical-civil/fluent/` | 301 | `/service-simulation/` |
| `/project/engineering/mechanical-civil/ls-dyna/` | 301 | `/service-simulation/` |
| `/project/engineering/mechanical-civil/opensees/` | 301 | `/service-simulation/` |
| `/project/engineering/mechanical-civil/plaxis/` | 301 | `/service-simulation/` |
| `/project/programming/` | 301 | `/service-project/` |
| `/project/programming/ai-data/` | 301 | `/service-project/` |
| `/project/programming/ai-data/deep-learning/` | 301 | `/service-project/` |
| `/project/programming/ai-data/image-processing/` | 301 | `/service-project/` |
| `/project/programming/ai-data/neural-network/` | 301 | `/service-project/` |
| `/project/programming/ai-data/signal-processing-matlab/` | 301 | `/service-simulation/` |
| `/project/programming/hardware/` | 301 | `/service-project/` |
| `/project/programming/hardware/fpga/` | 301 | `/service-project/` |
| `/project/programming/hardware/hspice/` | 301 | `/service-project/` |
| `/project/programming/hardware/proteus-student/` | 301 | `/service-project/` |
| `/project/programming/hardware/silvaco/` | 301 | `/service-simulation/` |
| `/project/programming/hardware/vhdl/` | 301 | `/service-project/` |
| `/project/programming/languages/c-sharp/` | 301 | `/service-project/` |
| `/project/programming/languages/excel/` | 301 | `/service-project/` |
| `/project/programming/languages/fortran/` | 301 | `/service-project/` |
| `/project/programming/languages/latex/` | 301 | `/service-project/` |
| `/project/programming/languages/python-programming/` | 301 | `/service-project/` |
| `/project/programming/languages/r-programming/` | 301 | `/service-statistics/` |
| `/project/qualitative/mixed-methods/atlas-ti/` | 301 | `/service-qualitative/` |
| `/project/qualitative/mixed-methods/dedoose/` | 301 | `/service-qualitative/` |
| `/project/qualitative/open-caqdas/qda-miner/` | 301 | `/service-qualitative/` |
| `/project/qualitative/open-caqdas/qualcoder/` | 301 | `/service-qualitative/` |
| `/project/qualitative/open-caqdas/quirkos/` | 301 | `/service-qualitative/` |
| `/project/qualitative/open-caqdas/taguette/` | 301 | `/service-qualitative/` |
| `/project/qualitative/text-linguistics/antconc/` | 301 | `/service-qualitative/` |
| `/project/qualitative/text-linguistics/praat/` | 301 | `/service-qualitative/` |
| `/project/research/` | 301 | `/service-project/` |
| `/project/research/publishing/` | 301 | `/service-article/` |
| `/project/research/research-support/` | 301 | `/service-project/` |
| `/project/research/research-support/market-research/` | 301 | `/service-statistics/` |
| `/project/research/research-support/research-tutorial/` | 301 | `/service-project/` |
| `/project/research/research-support/scientific-consultant/` | 301 | `/service-project/` |
| `/project/simulation/chemical-process/aveva-pro-ii/` | 301 | `/service-simulation/` |
| `/project/simulation/chemical-process/coco-simulator/` | 301 | `/service-simulation/` |
| `/project/simulation/civil-mechanical/msc-adams/` | 301 | `/service-simulation/` |
| `/project/simulation/civil-mechanical/staad-pro/` | 301 | `/service-simulation/` |
| `/project/simulation/multiphysics/labview/` | 301 | `/service-simulation/` |
| `/project/simulation/network/boson-netsim/` | 301 | `/service-simulation/` |
| `/project/simulation/network/eve-ng/` | 301 | `/service-simulation/` |
| `/project/simulation/network/ns2/` | 301 | `/service-simulation/` |
| `/project/simulation/network/opnet/` | 301 | `/service-simulation/` |
| `/project/statistics/general-statistics/jamovi/` | 301 | `/service-statistics/` |
| `/project/statistics/general-statistics/jasp/` | 301 | `/service-statistics/` |
| `/project/statistics/general-statistics/statsmodels/` | 301 | `/service-statistics/` |
| `/project/statistics/sem/lavaan/` | 301 | `/service-statistics/` |
| `/project/student-research/academic-research/research-project/` | 301 | `/service-project/` |
| `/project/student-research/university-projects/bachelor-project/` | 301 | `/service-project/` |
| `/project/student-research/university-projects/master-project/` | 301 | `/service-project/` |
| `/project/student-research/university-projects/urgent-student-project/` | 301 | `/service-project/` |

</details>

[84-URL action/target CSV](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/audit/project-dead-decisions-20261110.csv) is mirrored in the package and consumed by the scripts. Six **non-project** gaps retain their existing 404 with a reason: `/translation/`, `/translation/scientific-translation/`, `/translation/thesis-article-translation/`, `/supplementary/thesis-to-book/`, `/supplementary/subtitle-dubbing/`, and `/?page_id=363`. Original content, service equivalence or deleted-ID identity is unavailable; no misleading destination is assigned. See [decision report](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/gsc-decisions-report.md).

The initial exhaustive public sitemap/internal-link/resource crawl checked 1,018 URLs without a linked/sitemap 404. Historical/orphan indexing and performance URLs were then checked separately. Core WordPress, Yoast, Rank Math and custom XML sitemap filters exclude redirects, gone/private/query/noncanonical/noindex entries. The only robots changes are narrow Allow exceptions for `/order/` and `/order/?service` to expose their useful existing redirects. **No robots Disallow is added for `?s=`, `/search/` or feeds.** Appropriate existing noindex/404/410 behavior remains. Login exceptions cover only 13 exact observed guest GET queries; bare/admin/authenticated/POST flows stay protected. Obsolete feed and literal asset samples use exact reviewed rules, with real resources protected.

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
| `/service-project/` | 605 | 1,266 | پروژه های تخصصی دانشجویی \| مهندسی، VHDL و شبیه سازی - تزنویسه | راهنمای پروژه نرم افزاری و محاسبات تخصصی \| تزنویس |

Counts measure visible whitespace-delimited content excluding scripts, JSON-LD and site chrome; existing bodies use the largest article/longcopy surface. The new master landing has zero existing service words: its observed 301 resolves to a separate 512-word download guide. [Before/after CSV](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/before-after.csv) includes H1s and measurement notes.

The six money queries have exactly one canonical owner in the manifest. `/thesis/phd/` now targets «انجام پایان نامه دکتری». Each money page has tailored pricing factors, process, six visible FAQs with exactly one matching FAQPage, and author/reviewer/E-E-A-T fields. The 1,266-word technical hub has its own specialist workflow and six matching FAQs. Homepage title, description, hero and 332-word body emphasize the brand, resources and research guidance. All authored Persian is neutral Tehrani Persian, uses Persian prose digits and contains no U+200C. Prices, qualifications, author identities, capability, licensing and policies requiring owner facts use clearly marked placeholders; no statistics, reviews or credentials are invented.

[Internal-link plan](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/internal-links.csv): **224 contextual links across 222 source URLs**, including all 54 published posts (29 Persian, 25 English), calculator/tool pages, downloads, and **eight relevant links to `/service-project/`**. The 344 reviewed label rules cover 377 existing exact money anchors plus one generic exact homepage anchor. Across the captured affected corpus, the six-money-target ratio projects to **0/2,202 = 0%**, including existing links and additions. Rendered acceptance measures the actual result and fails above 5%; the projection is not a production claim. See [link evidence](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/links-report.md).

[BlueThesis link list](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/bluethesis-links.csv) contains **13 statistical-analysis anchors on 12 source pages**; one page has two. Retained links receive nofollow with existing rel tokens preserved; the replaced PhD body removes its old reference. [Cannibalisation note](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/cannibalisation.md) recommends de-optimizing bluethesis.ir/proposal and the thin official-site money pages, with cross-domain canonicalization only where content equivalence is established. No other domain is edited.

## Verification and current production state

All offline content/keyword/FAQ/anchor/map checks pass. PHP contracts cover actual merged package dry runs, backup-first transactions, repeat-apply no-ops, exact raw metadata/options/autoload restoration, reverse rollback, newer-edit guards, routing, MU boot and isolated restoration/failure scenarios. Python contracts cover runtime installation/restoration, per-URL acceptance and malformed project maps. All 119 PHP files pass syntax checks; shell syntax and existing release/classic-content checks pass. [Offline test evidence](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/verification-offline-20261110.json) records the executed contracts, including 12 project-verifier, 16 GSC-verifier and seven runtime-file regression tests. Database tests use isolated adapters; no mutating production WP-CLI command has run.

The [read-only verification summary](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/audit/verification-summary-20261110.json) preserves actual results:

- **84/84 project baseline checks PASS**: all still return 404 before apply. Their post-apply acceptance is currently **0 PASS / 84 FAIL**, because the redirects are staged only.
- **415 URL acceptance checks: 214 PASS / 201 FAIL**. Exactly 195 failures await approved apply (189 redirects, four 410s, two robots exceptions); six are the documented non-project content/identity gaps. Zero unexpected fetch failures. Five baseline changes are recorded, not silently discarded.
- Current sitemap audit: **404/404 page URLs PASS across eight XML files**. Two transient 503s passed one ordinary retry each; the original failures and retry records remain. Newly requested pages are not claimed to be in the live sitemap; owner acceptance uses `--require-money-pages` after apply.

Per-URL CSVs and compressed full reports are committed beside the summary. These are pre-apply results, not a claim that production has been fixed. The pre-existing Vercel integration attempted and failed a preview for the initial PR commit; branch-specific `git.deploymentEnabled: false` suppresses subsequent previews. No successful deploy, merge, production content change or external-site write was performed.

## Exact owner-reviewed apply steps

Use PHP 8.3+, Python 3.10+, WP-CLI and InnoDB. Review the CSVs and complete money-page/hub factual placeholders before public content publication; update visible FAQs and their schema together. Use a staging clone first, with absolute paths and **new private backup directories outside every public web root**, whose parent already exists. Pause editorial changes during apply/rollback. The installer backs up its four exact runtime files and modes; base apply backs up every changed post, all raw metadata and changed options; GSC batches back up raw option/autoload or absence and any physical robots bytes/mode before mutation. Existing redirect stores are retained, with their overlay state backed up. No script edits or backs up `wp-config.php` or `.env`.

For the requested **project-only** recovery, no content-package apply is needed because all five targets already exist:

```bash
export SEO_REPO=/absolute/path/to/reviewed/teznevise
export WP_PATH=/absolute/path/to/wordpress
export SEO_BACKUP=/absolute/private-parent/teznevise-seo-20261110-base
export PROJECT_BACKUP=/absolute/private-parent/teznevise-seo-20261110-project
cd "$SEO_REPO"
python3 scripts/seo-20261110/verify-projects.py --check-map
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP" --dry-run
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP" --dry-run
# Owner approval of this concrete dry-run plan is required before applying:
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP"
python3 scripts/seo-20261110/verify-projects.py --base-url https://YOUR-STAGING-HOST
```

For the **complete SEO package**, starting from a fresh staging clone and fresh backups, run offline checks and runtime install dry-run above, then this sequence. Review each dry-run before its apply:

```bash
python3 scripts/seo-20261110/verify.py
python3 scripts/seo-20261110/verify-gsc.py --check-inventory --allow-unresolved-count 6
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP" --dry-run
# After owner approval:
python3 scripts/seo-20261110/runtime-files.py install "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/apply.sh "$WP_PATH" "$SEO_BACKUP"
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP" --dry-run
bash scripts/seo-20261110/project-apply.sh "$WP_PATH" "$PROJECT_BACKUP"
for SEO_BATCH in redirects gone indexing links; do
  bash scripts/seo-20261110/gsc-apply.sh "$WP_PATH" "/absolute/private-parent/teznevise-seo-20261110-$SEO_BATCH" "--batch=$SEO_BATCH" --dry-run
  bash scripts/seo-20261110/gsc-apply.sh "$WP_PATH" "/absolute/private-parent/teznevise-seo-20261110-$SEO_BATCH" "--batch=$SEO_BATCH"
done
python3 scripts/seo-20261110/verify.py --live https://YOUR-STAGING-HOST
python3 scripts/seo-20261110/verify-gsc.py --base-url https://YOUR-STAGING-HOST
python3 scripts/seo-20261110/verify-projects.py --base-url https://YOUR-STAGING-HOST
python3 scripts/seo-20261110/verify-sitemap.py --sitemap https://YOUR-STAGING-HOST/sitemap_index.xml --require-money-pages
```

The optional `restores` batch is empty. The full-scope URL verifier retains the six non-project failures until the owner supplies evidence; do not call that a passing acceptance. Private staging noindex is preserved and will intentionally fail indexability checks until testing an indexable preview. Verify `/thesis/master/` is directly 200: its current core WordPress missing-page guess to a download must disappear when the actual page exists. Inspect any remaining explicit server/CDN/plugin redirect on staging rather than silently overwriting unknown stores.

After reviewing staging results, the owner may separately approve repeating the same dry-run/install/apply sequence with production `WP_PATH`, **fresh** private backup paths, and production verifier origins (`https://teznevise.ir`). Use the owner's configured cache purge and repeat acceptance. Check 301s are one hop, all money/hub URLs are 200 self-canonical/indexable, each managed money/hub page has one matching FAQPage and all links/anchors/nofollow are correct. Only then submit the cleaned sitemap in Search Console. None of these mutating steps was executed in this task.

## Exact rollback

For the complete package, roll back in reverse order, reviewing every dry-run. The generic GSC rollback reads its batch identity from its immutable backup and takes **no `--batch` argument**:

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

For project-only recovery, skip the batch loops and base content rollback: run the project rollback pair, then restore runtime files only if no other package is using them. Backups and journals are immutable; checksum/newer-edit guards must not be bypassed. Purge configured caches and re-run read-only checks against the original snapshot after rollback. Full commands and limits are in [PROJECT_RUNBOOK.md](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/PROJECT_RUNBOOK.md) and [GSC_RUNBOOK.md](https://github.com/maziyarid/teznevise/blob/seo/teznevise-onpage-20261110/seo/20261110/GSC_RUNBOOK.md).
