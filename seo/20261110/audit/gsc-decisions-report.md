# GSC decision review — 2026-11-10

Phase 2 follows the completed live Phase 1 inventory of all 387 supplied URLs. This map is a staged proposal for the same PR; no production apply, merge or deployment has occurred. Each row keeps the Phase 1 row ID, exact encoded request URL, observed initial/terminal status and the reviewed intended outcome.

## Decisions

| Decision | URLs | Application batch |
| --- | ---: | --- |
| Direct 301 consolidation or chain correction | 165 | redirects |
| Exact retired/junk route removal | 4 | gone |
| Exact useful alias robots Allow | 2 | indexing |
| Restore verified content | 0 | indexing |
| Remove unjustified noindex | 0 | indexing |
| Keep current outcome with reason | 216 | none |

Total: **387** input URLs, including **6 unresolved keep/404 decisions**. A keep decision is a reviewed outcome, not an instruction to force indexation. Sixteen already-gone requests keep their native 410. Healthy existing one-hop permanent aliases remain native; only missing, misleading or multi-hop routes receive new rules.

| Supplied GSC bucket | 301 | 410 | Unblock | Keep |
| --- | ---: | ---: | ---: | ---: |
| blocked | 17 | 1 | 0 | 29 |
| crawled_not_indexed | 0 | 2 | 0 | 30 |
| discovered_not_indexed | 0 | 0 | 0 | 33 |
| noindex | 0 | 0 | 0 | 22 |
| not_found | 144 | 0 | 2 | 36 |
| redirect | 4 | 1 | 0 | 66 |

## Target review and chain prevention

`gsc-redirect-map-20261110.csv` is the conventional old URL / new URL / reason map for the 165 new direct rules. `gsc-targets-20261110.csv` records fresh read-only target checks: 28 existing destinations return direct 200, self-canonical HTML with no noindex. The two additional destinations are explicitly planned base-package pages, not claimed live: `/student-projects/` and `/thesis/master/`. Their reviewed content and creation scripts already exist in this PR. Apply and verify base content before the redirect batch, and stop if either destination is not a direct 200, self-canonical, crawlable and indexable page.

Every 301 source was checked against the combined new-GSC plus base redirect graph. There are **zero chains or loops** in the intended graph, and **zero new homepage destinations**. Existing `http://teznevise.ir/` to HTTPS homepage is retained because it is same-page protocol normalization, not a content fallback. The econometrics project route goes to the live statistical-analysis guide, whose existing content explicitly covers econometrics and time series, rather than following its misleading old chemical-industrial hierarchy. The malformed `//statistics/` guess to an English tutoring page is corrected to the Persian `/service-statistics/` guide. The plural `/thesis/masters/` alias goes directly to the new degree-specific owner after base apply. Old project aliases that currently end at `/service-project/` point directly to `/student-projects/`, preventing a new chain after the base consolidation.

The 13 sampled guest login/recovery GET URLs currently reach `/inquiry/` through `/account/`; one initial rate-limited response per two URLs was retried politely and both final observations confirm that same existing final destination. The staged map preserves it in one direct hop only for those exact sampled request keys. Bare/admin login, logout, other query combinations and every POST are outside this change. These entries carry `implementation=exact_guest_login_redirect` for the runtime guard.

Three exact missing/obsolete asset-directory or literal-wildcard requests carry `implementation=exact_legacy_sample`. No wildcard is expanded. The runtime must refuse to replace an actual file or directory. Other real DOC/DOCX, JavaScript, RSS and JSON endpoints remain working assets/APIs; the historical crawled-not-indexed label is not a reason to turn them into HTML pages or redirect them.

## Indexing and robots decisions

The 33 discovered-not-indexed pages now all return 200 with self-canonicals, index robots and allowed crawling. They are retained, with internal-link and clean-sitemap work in Phase 3; this PR does not promise Google will index every eligible URL. The sampled noindex URLs are thin pagination/tag/sort views, internal search or private account/recovery flows. Their exclusions are intentional and stay in place; no unsupported blanket noindex removal is proposed.

`/order/` and `/order/?service` are useful existing single-hop 301 aliases to `/inquiry/`, currently hidden by `Disallow: /order/`. Exact Allow rules for those two sampled request forms make the existing redirects crawlable without opening unrelated query variants or changing admin/API protections. No other sampled useful canonical page is currently blocked. Six other blocked samples are intentionally protected infrastructure/obsolete routes and remain blocked.

The missing resource-feed variants are consolidated to their maintained corresponding HTML resource where one exists; a functioning RSS feed is retained. The old Sciences and Research University proposal feed is directed to its exact maintained university-specific HTML parent; no form contents are invented. The retired class-proposal route goes to the exact live class/research-methods proposal guide rather than a generic proposal page. The missing English APA citation-feed slug is corrected to the maintained same-topic English citation tutorial.

## Unresolved URLs and owner action

These are deliberately retained as 404 rather than inventing content, business capabilities or unrelated redirects. No unresolved row is applied by a mutation batch.

| Row | URL | Why unresolved | Owner action |
| --- | --- | --- | --- |
| gsc-0149 | https://teznevise.ir/translation/ | No original translation copy, currently published equivalent or verified translation-service scope is available. Owner must supply real content/scope for restoration or confirm a relevant replacement or permanent retirement. | Review the original database/archive and confirm the real service or retired content intent, then approve restoration, a genuinely equivalent 301, or permanent retirement. |
| gsc-0235 | https://teznevise.ir/supplementary/thesis-to-book/ | Owner must confirm whether this service still belongs on the site and supply the original content/real service scope; otherwise approve permanent retirement. | Review the original database/archive and confirm the real service or retired content intent, then approve restoration, a genuinely equivalent 301, or permanent retirement. |
| gsc-0240 | https://teznevise.ir/translation/scientific-translation/ | No original translation copy, currently published equivalent or verified translation-service scope is available. Owner must supply real content/scope for restoration or confirm a relevant replacement or permanent retirement. | Review the original database/archive and confirm the real service or retired content intent, then approve restoration, a genuinely equivalent 301, or permanent retirement. |
| gsc-0253 | https://teznevise.ir/supplementary/subtitle-dubbing/ | Owner must confirm whether this service still belongs on the site and supply the original content/real service scope; otherwise approve permanent retirement. | Review the original database/archive and confirm the real service or retired content intent, then approve restoration, a genuinely equivalent 301, or permanent retirement. |
| gsc-0262 | https://teznevise.ir/translation/thesis-article-translation/ | No original translation copy, currently published equivalent or verified translation-service scope is available. Owner must supply real content/scope for restoration or confirm a relevant replacement or permanent retirement. | Review the original database/archive and confirm the real service or retired content intent, then approve restoration, a genuinely equivalent 301, or permanent retirement. |
| gsc-0278 | https://teznevise.ir/?page_id=363 | Owner database/archive lookup is needed to identify deleted post 363 before restoring, redirecting or permanently retiring it. | Review the original database/archive and confirm the real service or retired content intent, then approve restoration, a genuinely equivalent 301, or permanent retirement. |

Read-only public REST/archive checks found no currently published translation, thesis-to-book or subtitle/dubbing equivalent, and public page/post ID 363 lookups return invalid ID. The verified `/thesis/international/` page does explicitly cover language editing, copyediting and proofreading, so it is a defensible nearest destination for the old native-editing/proofreading routes. It is not used as a fabricated translation service. A manuscript-to-article page is not treated as equivalent to thesis-to-book.

## Verification contract

`expected_status` is the intended status of the initial supplied request. `expected_canonical` is the final HTML page canonical where applicable; binary/API and intentionally noncanonical private/search/archive responses leave it empty. `expected_robots` and explicit `expected_robots_allowed` / `expected_indexable` fields distinguish crawl permission from search eligibility. `unknown` is used for non-HTML delivery assets where forcing HTML indexability would be meaningless. No content was restored because no verified missing original was available or necessary for the reviewed consolidations.

Phase 3 batches and Phase 4 checks must consume exact `request_url` keys, preserve query values and leading double slashes, keep GET/HEAD restrictions, and refuse unresolved rows. Primary sitemap URL entries must be 200, self-canonical, crawlable and indexable; image resources inside image extensions are not mistaken for HTML sitemap entries. The verifier must report the six unresolved URLs separately from changes merely pending owner-approved apply.
