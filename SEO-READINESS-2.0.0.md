# Teznevise 2.0 — SEO Readiness Audit

Audit date: 2026-08-30

Branch: `feature/teznevise-2.0-rebuild`

Starting main SHA: `a315c4dd380cef2796219c050a755fe4b3032e24`

## Evidence hierarchy

1. Google Search Console / URL Inspection for Google indexing state.
2. Live crawl evidence for current HTTP/indexability output.
3. Current Git source for theme behaviour.
4. WPWriter for WordPress content/configuration evidence.
5. Ubersuggest as a secondary crawler/estimate source.
6. Ahrefs was connected but its API endpoints returned `Insufficient plan`; no Ahrefs metric is treated as evidence in this audit.

## Executive result

The site is indexable and already receives Google visibility. The dominant technical risks found before the 2.0 PR were not raw performance problems; they were URL/indexation regressions and mixed sitemap signals.

The branch now contains compatibility safeguards for the source-level issues that can be fixed safely before merge. Live-site revalidation remains required after a deployment because production is currently behind some protections already present in Git.

## Google Search Console baseline

90-day report:

- Clicks: 153
- Impressions: 2,964
- CTR: 5.16%
- Average position: 14.02

The strongest current organic assets include the regression, content-validity and power-analysis calculators plus the homepage. Google URL Inspection returned `PASS / Submitted and indexed` for sampled critical pages including the homepage, calculators, downloads, inquiry, about, research-gap and article paths.

### Sitemap reality

The Search Console sitemap-list counters reported zero indexed URLs for several submitted sitemaps, but a direct sitemap-performance join found 95 sitemap URLs and 88 with Search Console performance data in the latest settled 28-day period. URL Inspection independently confirms multiple sitemap URLs are indexed.

Conclusion: the zero sitemap-list counters are not a reliable representation of the site's overall indexing state and must not be interpreted as “nothing is indexed”.

A real inconsistency did exist in source: some pages deliberately marked `noindex` were still eligible for the Yoast page sitemap. The 2.0 branch now adds a compatibility filter so the full explicit noindex-slug set is excluded consistently from Yoast sitemap entries.

## Critical URL regressions

Live crawling found URLs that still have Google impressions or an indexed URL-Inspection history but currently return HTTP 404. These are search-equity regressions, not harmless unused routes.

The branch now preserves them with 301 redirects to the closest current canonical equivalent:

| Legacy URL path | Current canonical target | Evidence/rationale |
| --- | --- | --- |
| `/article/` | `/service-article/` | Current article-consulting service page |
| `/article/isi/` | `/service-article/` | Indexed legacy article-service route |
| `/article/academic/` | `/service-article/` | Legacy article route still receiving impressions |
| `/project/assignments/` | `/service-project/` | Current student-project service hub |
| `/project/assignments/mechanical-civil/` | `/service-project/` | Removed legacy project taxonomy |
| `/project/assignments/mechanical-civil/thermodynamics/` | `/service-project/` | Query `انجام پروژه های ترمودینامیک` still near page one before the live 404 |
| `/project/engineering/electrical/gams/` | `/gams/` | WP confirms current published GAMS page at `/gams/` |
| `/thesis/engineering/electrical/` | `/thesis/engineering/` | Current live engineering thesis hub |
| `/thesis/other-fields/` | `/thesis/` | Removed legacy umbrella route |
| `/thesis/other-fields/space-science/` | `/thesis/pure-science/` | GSC query is `علوم فضایی`; pure-science is the closest current taxonomy |
| `/how-to-write-research-proposal/` | `/proposal/` | Dead internal research-proposal destination; no current GSC performance on the old URL |

These rules live in `inc/seo-regressions.php` and are covered by `tests/seo-regression-contract.php`.

## Broken URL generation

A fresh Ubersuggest crawl found malformed discovered URLs such as:

- `https://teznevise.ir/tel:09302822091`
- `https://teznevise.ir/https:/teznevise.ir/inquiry/`
- `https://teznevise.ir/https:/teznevise.ir/statistics/`
- `https://teznevise.ir/https:/wa.me/989302822091`

The current Git source already contains a `teznevise_url()` safeguard that preserves `tel:`, `mailto:` and absolute HTTP(S) URLs. The live crawl still exposes the old broken `tel:` output, which indicates production is behind the repository state or stale stored values are still reaching older call paths.

The 2.0 branch adds a defensive `home_url` compatibility filter to recover malformed single-slash absolute URLs and non-HTTP action schemes even when legacy builder/database values reach `home_url()`.

## Intentional noindex state

The crawler reports 16 blocked/non-indexable resources. Most are expected rather than ranking failures:

- WordPress search-result URLs blocked from crawling.
- Account and policy/support pages intentionally marked noindex by theme policy.
- Direct uploaded document resources that are not intended as independent HTML landing pages.

Explicit noindex page slugs currently include:

- `account`
- `achievements`
- `careers`
- `corporate-social-responsibility`
- `fair-use-policy`
- `join-us`
- `originality-guarantee`
- `revision-policy`
- `service-commitments`

`join-us` and corporate-social-responsibility were previously indexed and still have historical impressions, but current source deliberately noindexes them. This audit does not reverse that product/SEO policy without an explicit decision that those pages should compete in organic search.

## On-page crawl

GSC Wizard live on-page audit of 25 visibility-bearing URLs found 46 issues:

- Critical: 7
- Medium: 3
- Low: 36

The critical set was dominated by the live 404s above and the intentional noindex pages. Low-severity findings were mostly trailing-slash redirects and title/meta lengths.

One medium structured-data issue remains: a Yoast-generated `Organization` node was observed without a `logo` on a download page. Yoast documents `logo` as a required Organization property. This should be corrected in Yoast/site identity configuration or through a verified Yoast schema filter, but it is not classified as a release-blocking ranking failure by this audit.

## Internal links

Ubersuggest found 24 pages containing broken-link anchors, dominated by the old malformed telephone CTA. The current source contains the direct `tel:` preservation fix and the branch adds defensive compatibility recovery.

One content link pointed to `/how-to-write-research-proposal/`, which is now covered by a 301 to `/proposal/`.

## Performance

Fresh Ubersuggest PageSpeed lab results:

Desktop:

- LCP: ~0.77 s
- FCP: ~0.43 s
- TBT: 107 ms
- CLS: 0.106
- TTI: ~1.5 s

Mobile:

- LCP: ~1.3 s
- FCP: ~1.3 s
- TBT: 7 ms
- CLS: 0.048
- TTI: ~4.7 s

Observed optimisation opportunities include roughly 51–54 KB unused CSS, ~90 KB unused JavaScript and redirect overhead. These should be addressed during the 2.0 frontend consolidation, but current lab speed is not the primary organic-search blocker.

GSC Wizard field Core Web Vitals could not be queried because no Chrome UX Report API key is configured, so this document does not claim CrUX field-data PASS.

## Cannibalisation/content architecture

Search Console shows low-volume query splitting across multiple project/service pages, including:

- `انجام پروژه کارشناسی`
- `انجام پروژه شیمی`
- `انجام پروژه هوش مصنوعی`
- `انجام پروژه پروتئوس`
- `انجام تحقیق`
- `انجام پروژه مهندسی صنایع`
- `انجام مقاله دانشجویی`

These are content/IA consolidation opportunities rather than immediate theme release blockers. They should inform future content pruning, canonical targeting and internal-link architecture.

## Off-page constraint

Ubersuggest's traffic estimates substantially under-report the Persian site's real Search Console visibility and are therefore not used as traffic truth. Its backlink index nevertheless reports only one backlink/referring domain and no followed backlink in the sampled overview.

That is a material strategic constraint: fixing technical SEO alone will not create strong competitive rankings. Authority acquisition, citations and relevant followed referring domains remain necessary after the site is technically stable.

## WordPress configuration observations

WPWriter successfully connected to the live WordPress site and confirmed a static homepage and more than 100 pages. It reported the permalink structure as `Plain`, but this conflicts with the live site's working pretty permalinks, canonicals and REST connection. No permalink setting was changed because the connector result is internally inconsistent and changing permalink structure on an indexed production site without stronger evidence is unsafe.

WPWriter's plugin-list call failed, so the active SEO-plugin inventory could not be independently enumerated through that connector. Current source is Yoast-aware and the live structured-data output is consistent with Yoast being present.

## Ubersuggest crawl summary

Fresh crawl:

- Overall health: 79
- Crawled: 151
- Successful: 91
- Redirected: 33
- Broken: 11
- Blocked: 16
- Total reported issues: 156

The most actionable technical findings are addressed above. Title-length and generic URL-shape warnings are not treated as equivalent to crawl/indexing failures.

## Merge/deployment gates

Before calling the 2.0 release SEO-safe:

1. GitHub CI must pass PHP 8.3 lint, release contracts, the new SEO regression contract and the fresh WordPress activation job.
2. Deploy the resulting branch/release to an environment that represents production.
3. Re-crawl the formerly broken legacy routes and confirm one-hop HTTP 301 to the documented canonical targets.
4. Confirm `tel:`/WhatsApp/absolute CTA links no longer resolve under the Teznevise host.
5. Regenerate/recheck Yoast sitemaps and verify intentionally noindexed pages no longer appear.
6. Re-run GSC URL Inspection on priority canonical pages and recovered legacy routes after Google recrawls them.
7. Re-run the live on-page crawl and require zero unexplained critical technical issues.
8. Validate the Organization logo/schema configuration.
9. Continue the broader Teznevise 2.0 frontend, FA7 Pro, accessibility and exact-ZIP release gates separately; this SEO audit does not mark those unexecuted gates PASS.

## Current conclusion

There is no evidence that Google is globally blocked from crawling or indexing Teznevise. The site already ranks for useful queries and several calculator pages perform well. The highest-value technical corrections before the 2.0 merge are preserving legacy URL equity, eliminating malformed generated links, aligning noindex with sitemap membership, and preventing the redesign from reintroducing those regressions.
