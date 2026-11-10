# Project performance and original-content evidence — 2026-11-10

## Authoritative performance cohort

The connected GSC Wizard API returned exactly **135 distinct `/project/` page rows** for `sc-domain:teznevise.ir`, web search, **2026-08-09 through 2026-11-08**. The source response is `project-gsc-performance-20261110.json`: `dataSource=api`, dimensions `page`, `rowsReturned=135`, `rowsTotal=135`, and `pagination.hasMore=false`. All page identities, impressions, clicks and average positions below come from that response; no performance values were inferred from topic popularity or indexing labels.

A fresh read-only check of **all 135 exact URLs** confirms the owner-stated split: **84 initial 404 and 51 native 301**. After redirects, 84 remain 404 and the 51 redirect destinations return 200. There are zero fetch/parse errors and no transient results. The checks ran from 2026-11-10T17:47:43.726976+00:00 through 2026-11-10T17:47:55.564840+00:00. Canonical, robots meta/header, effective robots rule and final URL are recorded per row.

`audit/project-legacy-inventory-20261110.csv` is the root review artifact and its package copy under `seo/20261110/audit/` is byte-identical. The earlier 112-URL discovery set is retained separately as `project-discovery-inventory-112-20261110.csv`; it is a different cohort. There are 68 URLs in both sets, 67 performance URLs absent from the earlier discovery set, and 44 discovery URLs not returned in this performance cohort, for a union of 179 project URLs. This reconciles the earlier 90/22 discovery count with the authoritative 84/51 performance count. The frozen 387-row indexing inventory remains unchanged.

The API did not provide `coverage.filteredTotals` or covered-share values, so no project-cohort aggregate or coverage percentage is quoted. Per-URL values remain authoritative for ranking. The supplied API site-wide totals are 549 clicks and 16,661 impressions.

## Data maturity

Search Console did not establish a settled-through boundary: `dataMaturity.settledThrough=null`, `firstIncompleteDate=null`, and source `unavailable`; the metadata probe exceeded its deadline. The API uses `America/Los_Angeles` date boundaries. The requested date window is preserved, but the report does not claim that its final days are finalized or infer a boundary from the last returned activity row.

## Exact top-15 dead-route priority

Only URLs whose fresh **initial status is 404** enter this ranking. Sort order is impressions descending, then clicks descending, then the exact URL ascending. The CSV contains all 84 ranked dead routes and flags the first 15. No native-301 URL is promoted into the dead-route ranking merely because it has more impressions.

| Rank | Original URL | Impressions | Clicks | Average position |
| ---: | --- | ---: | ---: | ---: |
| 1 | https://teznevise.ir/project/engineering/chemical-industrial/chemical-engineering-project/ | 87 | 0 | 39.2069 |
| 2 | https://teznevise.ir/project/engineering/electrical/ees/ | 52 | 0 | 18.7115 |
| 3 | https://teznevise.ir/project/programming/hardware/hspice/ | 50 | 0 | 18.5600 |
| 4 | https://teznevise.ir/project/engineering/chemical-industrial/econometrics-project/ | 48 | 0 | 14.3333 |
| 5 | https://teznevise.ir/project/engineering/mechanical-civil/opensees/ | 48 | 0 | 16.4375 |
| 6 | https://teznevise.ir/project/engineering/mechanical-civil/plaxis/ | 48 | 0 | 72.1875 |
| 7 | https://teznevise.ir/project/programming/hardware/vhdl/ | 46 | 0 | 23.1739 |
| 8 | https://teznevise.ir/project/programming/hardware/silvaco/ | 45 | 0 | 28.9111 |
| 9 | https://teznevise.ir/project/engineering/mechanical-civil/aerospace-project/ | 44 | 0 | 14.7955 |
| 10 | https://teznevise.ir/project/assignments/computer-science/uml-diagrams/ | 41 | 0 | 78.0244 |
| 11 | https://teznevise.ir/project/assignments/web-software/cms-frameworks/ | 41 | 0 | 32.0244 |
| 12 | https://teznevise.ir/project/assignments/web-software/ux-ui-design/ | 39 | 0 | 30.8462 |
| 13 | https://teznevise.ir/project/programming/ai-data/signal-processing-matlab/ | 39 | 0 | 27.9231 |
| 14 | https://teznevise.ir/project/assignments/electrical-electronics/signal-processing/ | 37 | 0 | 78.4054 |
| 15 | https://teznevise.ir/project/programming/languages/r-programming/ | 36 | 0 | 46.1389 |

EES, HSPICE, econometrics and OpenSees are all present in the measured top 15. The priority file is `project-restoration-priority-20261110.csv`, mirrored at the requested root `audit/` path. Full metric precision is retained in CSV/source JSON; this table rounds positions only for reading.

## Current source-backed restoration rule

The latest instruction is applied independently of the priority ranking: **restore a dead URL only when its original content exists in WordPress or the repository; otherwise 301 it to the closest relevant live page or project category.** Priority does not authorize inventing fifteen new software pages. Therefore `top15_priority=1` is a performance flag, while `restore_flag` remains zero unless real original-content evidence supports a restoration. The completed authenticated source review found none for the 84 dead URLs, so the final decision is 0 restorations and 84 direct 301s, with zero unresolved project decisions.

The repo/history review found no original page content matched to any of the 84 dead URLs. It checked the tracked REST export, extracted fields and WXR classic content; 347 available code/docs revisions; current public WordPress page GUIDs; and controlled redacted projections of historical public hierarchy/slug/GUID/content-topic metadata. Historical page-parent reconstruction also produced no `/project/` page hierarchy for the dead originals. The only exact legacy project GUID was GAMS (post 1678), whose original URL already redirects to the substantive `/gams/` page and is not one of the 84 dead routes. Software-logo attachments and passing econometrics mentions in an about/recruitment page are not treated as an original software article. Raw credential-bearing exports or snippets are not included or imported.

Authenticated read-only WordPress source checks are now complete. The owner-authorized query enumerated 257 non-trash pages and 4 trash pages and checked all 135 GSC slug candidates and legacy project GUIDs across page, post, revision, WPCode and resource records. Only the class-proposal page, an engineering-thesis page and the existing GAMS page matched; none is an original for the 84 dead URLs. Targeted HSPICE/OpenSees checks likewise found no missing original. Sanitized metadata evidence is in `project-wordpress-source-evidence-20261110.json`; no credentials, options, raw dump or unpublished body was exported. Therefore all 84 decisions are direct 301 consolidations to existing relevant 200 pages; no original article, software capability or empty category page was fabricated.

The 84-route decision map and the broader 387-route remediation map are separate implementation artifacts. They should consume this authoritative cohort, preserve the original immutable indexing observations and retain a 200 `/service-project/` software/topic hub while `/student-projects/` owns the broad money query. No live WordPress mutation, merge or deploy was performed for this evidence refresh.


## Final dead-route decisions

`project-dead-decisions-20261110.csv` contains all 84 current 404 project URLs, the exact performance ranks/metrics, authenticated source evidence and the reviewed action. The dedicated application batch is `project`. Results: **0 restored, 84 direct 301, 0 project URLs unresolved**. `project-dead-targets-20261110.csv` records fresh direct-200, self-canonical, crawlable and indexable checks for all five destinations. The retained `/service-project/` hub receives technical/coursework/circuit topics; simulation, statistics, qualitative methods and manuscript support use their closest existing specialist guides. No target is the homepage or a legacy project category that itself redirects.

The original 387-row decision map keeps its scope and row IDs. Its 56 rows overlapping the 84 dead cohort now match the project decisions and batch; 28 additional dead performance URLs remain in the separate project decision map for the CLI merge helper. Four existing native project aliases are kept on their already-correct one-hop route to the retained 200 hub. The immutable 387-row indexing inventory is unchanged. All 51 native aliases in the performance cohort were observed as a single hop to 200 and remain outside the dead-route mutation scope.
