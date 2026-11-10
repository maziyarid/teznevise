# Live crawl review: 2026-11-10

Read-only owner audit of https://teznevise.ir. Started 2026-11-10T11:18:03.300292+00:00; evidence frozen 2026-11-10T11:24:14.613197+00:00. No WordPress content or settings were changed.

## Coverage and results

- 1,018 distinct same-origin HTTP URLs checked, including 428 HTML responses, recursive XML sitemap URLs, all reachable anchor links, downloadable files, first-party image/srcset/script/style/preload references and CSS resource references.
- 83,116 observed internal anchor edges and 8,648 first-party resource references retained in compressed CSVs; repeated navigation is intentionally retained as evidence.
- Initial status counts: {200: 1011, 301: 5, 302: 1, 404: 1}. Terminal status counts after redirects: {200: 1017, 404: 1}. Initial and terminal status are distinct in url-status.csv.
- 1 terminal 404: `/student-projects/`, an explicit requested new-page probe with no existing inbound link or sitemap entry. Creating the canonical landing resolves it; redirecting it elsewhere would defeat the intended URL.
- No linked or sitemap HTML/file/resource URL currently returned 404. 0 fetch/parse errors. 0 title-based soft-404 candidates; this heuristic does not replace Search Console soft-404 diagnostics.
- All 54 published blog posts (including 25 English posts) from the public WordPress REST collection are covered. The English sitemap supplies English discovery too.

The user-supplied Search Console figures are facts for 2026-08-09 through 2026-11-08: 549 clicks, 16,661 impressions; indexing totals 617 indexed, 182 not found, 47 blocked, 71 redirect and 22 noindex. These are historical aggregate counts, not URL exports. A public live crawl cannot enumerate unlinked historical URLs or establish that those 182 old 404s have all been fixed. No claim of that completeness is made. Import the owner's Search Console URL exports and re-crawl any historical-only URL before assigning further 301s.

## Redirect decisions

`redirect-map.csv` is the manually reviewed, three-row application map: the two required legacy service aliases keep their already observed single-hop 301s; `/service-project/` consolidates the same project-service intent into the new `/student-projects/` page after that target exists. No unrelated missing URL is redirected to the homepage.

`redirect-chains.csv` lists every observed hop and terminal URL. In addition to the two required aliases: `/account/` currently returns 302 to `/inquiry/`; `/order/` returns 301 to `/inquiry/`; `/wp-sitemap.xml` returns 301 to `/sitemap_index.xml`; `/thesis/master/` returns 301 to `/download/masters-research-methods-guide/`.

The last redirect must be removed for the new master landing to return 200 at its own canonical URL. The downloaded guide remains a separate resource. The current project-service page returns 200 and contributes a 605-word baseline for consolidation; it is not misreported as a broken URL.

## Robots review

The exact fetched robots file and XML sitemap bodies are in `snapshots/`; SHA-256 hashes are in `snapshot-sha256.csv`. `robots-rules.csv` reviews every Allow/Disallow directive, and `robots-blocked-urls.csv` lists discovered URLs blocked by the effective wildcard-group rule.

`Disallow: /order/` is the only rule blocking a useful discovered or explicitly probed path: crawlers need to see its legacy 301 to `/inquiry/`. Allow that alias (or remove only its Disallow) and retain protection for administration, API, private transactions, staging and duplicate parameter noise. All six canonical money-page URLs are crawlable under the fetched robots rules. No broader removal of robots protections is warranted by this evidence.

The audit deliberately performs read-only status checks on blocked paths. It uses wildcard and terminal `$` rule matching and longest-match Allow precedence for this site's single `User-agent: *` group. It does not infer the identities of the 47 historical Search Console blocked URLs.

## Word-count method and evidence limits

`before-money-pages.csv` records each requested URL, its initial status, terminal URL, current title, and largest rendered `longcopy` or article surface after scripts, styles, navigation, header and footer removal. Counts split text on whitespace and include headings/FAQ prose in that surface. Nested wrappers are counted once; builder hero and site chrome are excluded. Homepage count is its 235-word longcopy block, not all homepage cards. New `/student-projects/` has zero existing landing words. `/thesis/master/` has no independent service-page baseline: its observed 512 words belong to the redirected download guide.

Fragments are removed; percent encoding is normalized; query strings and schemes are retained as distinct fetch URLs. Extracted text replaces U+200C with ordinary spaces for the requested content convention. Exact current robots/XML snapshots contain no U+200C. Raw HTML and full resumable state stay in ignored `work/seo-20261110/`, while the compact JSON and compressed edge CSVs preserve the review evidence.

This traversal covers public, linked, non-authenticated discovery; it cannot expose orphan URLs absent from sitemaps and links, server-only rewrite rules, authenticated pages, Search Console history, or JavaScript-created links not present in HTML. Binary responses are read up to eight MB for status verification; no binary response was misclassified as a sitemap. External-site destination health is outside the internal crawl.

## Reproduce

Use Python 3 with `lxml` available. From the repository root:

```sh
python3 scripts/seo-20261110/crawl.py --workers 8
python3 scripts/seo-20261110/report-crawl.py
```

The crawler writes only local audit/work files and makes HTTP GET requests. `--resume` uses local ignored crawl state; omit it for a fresh live crawl. The report script performs no network requests and does not overwrite the manually reviewed redirect map. Read compressed CSV evidence with Python `gzip.open(path, 'rt')` or `gzip -dc path`.
