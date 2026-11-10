# Reviewed GSC remediation decisions — 2026-11-10

## Scope and exact counts

The original indexing inventory contains 387 exact owner-supplied URLs and remains immutable. Its reviewed decision file preserves all row IDs and request URLs. The authenticated project-performance cohort supplies 135 URLs, of which fresh checks confirm 84 dead routes and 51 healthy single-hop native aliases. The separate project decision file adds 28 previously unsampled dead URLs and duplicates 56 original rows consistently, producing **415 unique reviewed decision URLs**. The remaining native performance aliases require verification, not new mutation rules.

| Action | Original 387 | Combined 415 |
| --- | ---: | ---: |
| 301 | 161 | 189 |
| 410 | 4 | 4 |
| unblock | 2 | 2 |
| restore | 0 | 0 |
| remove_noindex | 0 | 0 |
| keep | 220 | 220 |

The combined application batches contain **105 original redirects, 84 project redirects, 4 exact removals and 2 robots exceptions**. Link rewrites use their own reviewed batch. There are **0 source-backed dead-project restorations and 0 unresolved project decisions**. Six unresolved non-project URLs remain explicitly marked keep/404; their identities and required owner evidence are listed below.

## All 84 dead project routes

The authoritative source is `project-gsc-performance-20261110.json`: GSC Wizard API, web search, 2026-08-09 through 2026-11-08, exactly 135 page rows and `hasMore=false`. Every URL was freshly checked: 84 initial/terminal 404 and 51 native single-hop 301 to 200, with no errors. `project-legacy-inventory-20261110.csv` records metrics and live evidence; `project-restoration-priority-20261110.csv` ranks only the 84 current 404s by impressions descending, clicks descending, then exact URL. Its measured top 15 includes EES, HSPICE, econometrics and OpenSees. Data maturity remains unknown because `settledThrough` is null; no finalized-through date or aggregate filtered metric is inferred.

The latest rule is applied literally: restore only if real original content exists in WordPress or the repository; otherwise use the closest relevant live page/category. The authenticated read-only WordPress check enumerated non-trash/trash pages and inspected all performance slug candidates and legacy project GUIDs across content, revisions and snippets. It found no missing original for any of the 84 dead routes; the only actual legacy GAMS original is already live at `/gams/` with a healthy native alias. Repo/history checks corroborate this absence. `project-wordpress-source-evidence-20261110.json` retains sanitized source metadata, with no credentials, options, unpublished bodies or unredacted dumps.

Therefore `project-dead-decisions-20261110.csv` contains **84 direct 301s in batch `project`**, not invented software articles or empty parent pages. Its root `audit/` review copy is identical. All five destinations have fresh direct-200, self-canonical, crawlable and indexable proof in `project-dead-targets-20261110.csv`:

| Live destination | Dead project URLs | Topic fit |
| --- | ---: | --- |
| https://teznevise.ir/service-project/ | 47 | Retained 200 software/topic hub; coursework, programming, circuit and engineering guidance, including HSPICE/EES/VHDL topic organization |
| https://teznevise.ir/service-simulation/ | 21 | Simulation, numerical/structural modelling, CFD and model validation |
| https://teznevise.ir/service-statistics/ | 7 | Statistical methods, econometrics, measurement, time series and R |
| https://teznevise.ir/service-qualitative/ | 8 | Qualitative coding and interpretation |
| https://teznevise.ir/service-article/ | 1 | Scientific manuscript preparation and publication review |

The `/service-project/` URL remains 200 and serves technical software/topic discovery; it is not redirected to `/student-projects/`. The latter owns the broad student-project money query. Four existing native project aliases stay on their already-correct single hop to the retained hub. The 56 overlapping original decisions now exactly match the separate project map; other original decisions retain their reviewed scope.

## Redirect integrity and existing technical outcomes

The combined new-redirect graph, including the two retained base service aliases, has **zero chains, zero loops and zero new homepage destinations**. The existing HTTP homepage-to-HTTPS redirect is retained as same-page protocol normalization, not a content fallback. Every new project destination is already live 200; a legacy `/project/` category that itself redirects is never used as an intermediate target.

The corrected `//statistics/` route avoids its wrong-language English tutoring guess and goes directly to the Persian statistical-analysis guide. The plural `/thesis/masters/` route goes directly to the new degree-specific master landing after the base content package creates and verifies it. The live `/thesis/master/` missing-page guess must disappear when the real canonical page exists; owner-run verification enforces that outcome. Other planned base money targets are likewise verified before activating dependent original rules.

The 13 exact sampled guest login/recovery GET URLs preserve their observed final `/inquiry/` destination in one hop; admin/bare login, other query combinations and POST requests remain untouched. Two initially rate-limited login observations were retried politely and confirmed the same existing final route. The exact guest-login implementation tag is required.

Three reviewed missing asset-directory/literal-wildcard samples and a retired author archive receive new exact 410 outcomes. No wildcard is expanded, and the runtime refuses to replace a real file or directory. Six obsolete resource-feed samples receive exact reviewed topical redirects; functioning RSS feeds, search, JSON APIs, downloadable files and JavaScript delivery URLs retain their intended behaviour. No broad robots block is introduced.

## Indexing and robots decisions

All 33 discovered-not-indexed URLs in the original input now return public 200 self-canonical indexable pages and are retained. Historical exclusion labels alone do not justify forcing duplicate or private pages into search. Internal search, account/recovery, thin pagination/tag collections and alternate sorts retain justified noindex or access policies. Binary/API/feed delivery is not mislabeled as an HTML money landing. No unsubstantiated blanket noindex removal or indexation promise is proposed.

Only `/order/` and the exact `/order/?service` sample need new robots Allow exceptions, so crawlers can see their existing single-hop enquiry redirects. Administrative/API/transactional protections remain. The physical robots file is backed up before any approved narrow change, or stays virtual if no physical file exists; rollback restores its bytes and mode and refuses newer owner edits.

Primary sitemap URL entries are filtered to direct 200, self-canonical and indexable pages, including the retained software hub. Redirects, 410s, unresolved 404s, noncanonical/duplicate/private URLs and generated internal aliases are excluded. Protocol/host mismatches are not treated as self-canonical. Actual canonical metadata is never rewritten merely to make a staging test pass. Image-extension resources are not mistaken for primary HTML sitemap entries.

## Unresolved non-project URLs

These six URLs retain observed 404 with batch `none`; they are not mutated by the project batch or silently redirected to the homepage.

| Row | URL | Evidence still needed |
| --- | --- | --- |
| gsc-0149 | https://teznevise.ir/translation/ | No original translation copy, currently published equivalent or verified translation-service scope is available. Owner must supply real content/scope for restoration or confirm a relevant replacement or permanent retirement. |
| gsc-0235 | https://teznevise.ir/supplementary/thesis-to-book/ | Owner must confirm whether this service still belongs on the site and supply the original content/real service scope; otherwise approve permanent retirement. |
| gsc-0240 | https://teznevise.ir/translation/scientific-translation/ | No original translation copy, currently published equivalent or verified translation-service scope is available. Owner must supply real content/scope for restoration or confirm a relevant replacement or permanent retirement. |
| gsc-0253 | https://teznevise.ir/supplementary/subtitle-dubbing/ | Owner must confirm whether this service still belongs on the site and supply the original content/real service scope; otherwise approve permanent retirement. |
| gsc-0262 | https://teznevise.ir/translation/thesis-article-translation/ | No original translation copy, currently published equivalent or verified translation-service scope is available. Owner must supply real content/scope for restoration or confirm a relevant replacement or permanent retirement. |
| gsc-0278 | https://teznevise.ir/?page_id=363 | Owner database/archive lookup is needed to identify deleted post 363 before restoring, redirecting or permanently retiring it. |

A short title, a numeric page ID or an unrelated source mention cannot establish the identity, service availability or content equivalence needed for a truthful restoration/301. These explicit non-project limitations are separate from the completed 84-route project source check.

## Apply and verification contract

The shared decision columns preserve initial request status and final/canonical expectations, exact request/query keys, crawl permission and indexability separately. The project map uses the same schema plus metric/source-evidence fields and `old_url` for review. The merge helper must reject conflicting duplicates and apply the dedicated `project` batch exactly once with an immutable private backup. All original observations remain unchanged.

WP-CLI and runtime checks protect files, private endpoints and admin flows; base content and the retained hub are prepared before dependent rules. Dry runs write nothing. Database transactions, immutable checksummed backups, created-post ownership checks, physical-file recovery and strict reverse batch rollback protect the approved mutations. No production apply, merge or deployment was performed for this decision review. The runbook and verification scripts provide the owner-run acceptance steps.
