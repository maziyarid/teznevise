# Phase 1: supplied Search Console URL inventory

All 387 supplied URLs were fetched live on 2026-11-10. Group counts exactly match the supplied lists: 47 blocked, 32 crawled-not-indexed, 33 discovered-not-indexed, 22 noindex, 182 not-found and 71 redirect. No rows were omitted or merged.

The requested repository path is `audit/gsc-indexing-20261110.csv`; the byte-identical package copy is `seo/20261110/audit/gsc-indexing-20261110.csv`. SHA-256: `c284751f1bac7e23db670550d18c104a8c21ce2f99d5a70f52c9136cfb941dbf`.

| Current initial response | Count |
| --- | ---: |
| 200 | 90 |
| 301 | 116 |
| 302 | 15 |
| 404 | 150 |
| 410 | 16 |

After redirects, 221 responses are 200, 150 are 404, and 16 are 410. There are no fetch or parse errors. Current robots rules allow 379 sampled URLs and block 8; historical Search Console buckets are retained separately from current observations. Two sampled login requests initially returned 429, then returned 302 after one ordinary retry without authentication or bypass; the initial attempts remain in the ignored raw cache. No rate-limit response was classified as permanently gone.

Every row records its exact request URL, original input in ASCII-escaped JSON, initial and terminal status, final URL, redirect chain, HTML and HTTP canonical evidence, robots meta/X-Robots-Tag, effective robots rule, content type/title, captured time, and links from the earlier full crawl. Literal wildcard paths were fetched as individual samples, never expanded. Query spaces and literal pipes were preserved through URL encoding; `//statistics/` remains a double-slash path on teznevise.ir. Markdown escapes were normalized and documented per row. The raw evidence remains under ignored `work/seo-20261110/gsc/`.

The current robots file no longer blocks the 47 URLs in the historical blocked bucket. Eight other supplied samples are still blocked: API/admin samples plus `/order/` and `/order/?service`. Decisions are made in Phase 2, rather than inferred from the historical bucket.

Reproduce with `python3 scripts/seo-20261110/gsc-inventory.py`; see its `--help` for cache/retry controls. The script performs only public HTTP GET requests and local evidence writes.
