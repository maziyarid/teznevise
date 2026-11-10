# Reviewed internal-link plan

The 2026-11-10 live crawl and the public WordPress REST post endpoint were reconciled before preparing `internal-links.csv`. Every planned source returned HTTP 200 and is a final live URL in `crawl.json`. New destination pages are deliberately identified as planned content: `/thesis/master/` and `/student-projects/`; their existence after apply is a verification requirement, not a claim that both existed during the crawl.

The REST evidence URL is `https://teznevise.ir/wp-json/wp/v2/posts?per_page=100&_fields=id,link,title,slug,status`. It returned exactly 54 published posts, 29 in Persian and 25 in English. `rest-post-inventory.csv` preserves the exact IDs, public URLs, language and normalized titles. The Persian post sitemap alone contains 29 posts plus the blog archive, so using only that sitemap would miss the English posts. All 54 post URLs occur in the full crawl and all receive a reviewed contextual continuation link.

| Source group | Pages receiving a link | Scope |
| --- | ---: | --- |
| Published blog posts | 54 | All 29 Persian and 25 English posts from the public REST inventory |
| Persian tools and hubs | 31 | 29 calculator/tool pages plus `/online-calculation-tools/` and `/abzar/` |
| English tools and hubs | 30 | 28 calculator pages plus `/en/tools/` and `/en/student-tools/` |
| Downloads and hubs | 101 | All 99 live `/download/` resource pages plus `/downloads/` and `/en/downloads/` |
| Total | 216 | One added, topic-matched contextual link on each source |

The plan includes every live tool and downloadable-resource page observed under these routes; pagination and taxonomy archives are not counted as individual resources. Existing navigation links remain distinct from the new in-content continuation links. Links should render with their source article or useful resource content and remain available to crawlers without requiring a click or JavaScript. Source content and original download actions are retained. English sources use English descriptive anchors that explicitly label the Persian destination.

| Destination | Added source pages | Selection rationale |
| --- | ---: | --- |
| https://teznevise.ir/thesis/ | 29 | Whole-research process, authorship, structure, scheduling and defence |
| https://teznevise.ir/thesis/master/ | 101 | Master's research methods, instrument quality, analysis interpretation and field checklists |
| https://teznevise.ir/thesis/phd/ | 13 | Research contribution, critical evidence synthesis and advanced interpretation |
| https://teznevise.ir/proposal/ | 47 | Feasibility, objectives, sampling and current university-proposal forms |
| https://teznevise.ir/proposal/phd/ | 12 | Doctoral research gaps, model justification, study power and doctoral forms |
| https://teznevise.ir/student-projects/ | 14 | Coursework/project planning, validation, deliverables and research ethics |

Each row documents why the destination follows from the source's topic. A primary keyword has one owner in the manifest; a link to a related service does not turn the source tool or download into another money landing page. The proposal material for a specific doctoral form goes to the doctoral proposal owner rather than a generic thesis page.

## Anchor audit and correction

Exact match means the whole visible anchor equals one of the six primary queries after decoding entities, stripping markup, replacing U+200C with a space and collapsing whitespace. A partial phrase inside a longer useful description is not counted as exact match. The denominator below is links from the 216 affected sources to the six money-page URLs, including existing rendered navigation, footer and body links. It is not inflated with unrelated internal links.

The 216 proposed additions contain **zero exact-match anchors: 0/216 = 0%**. Existing rendered pages nevertheless contain 361 exact-match occurrences among 1,910 links to these six targets: **18.90%**. Adding descriptive links alone would leave **361/2,126 = 16.98%**, above the requested 5% ceiling.

`exact-anchor-rewrites.csv` therefore contains 329 deduplicated source/target/old-text rules covering all 361 observed occurrences. It supplies 31 distinct meaningful replacement anchors, rotating the description while preserving the destination. A rule replaces every exact matching occurrence on that source, including repeated footer links. Old text is normalized to spaces in the reviewed CSV. It does not rewrite unrelated links or phrases within article prose. The runtime must compare the whole anchor's visible text and match the same-origin canonical target before replacing its inner content with escaped text.

For the captured baseline plus this plan, the expected result after all matching rewrites and additions is **0/2,126 = 0%**, below 5%. This is a deterministic projection from the captured corpus; no production apply has occurred. Verify the actual rendered pages after an owner-approved apply, because plugins, menus or subsequent edits may add links. Count both additions and existing money links, report residual exact matches, and fail verification above 5% instead of claiming that the additions-only ratio proves sitewide compliance.

## Outbound review

`bluethesis-links.csv` lists the 12 affected source URLs and all 13 outbound statistical-analysis anchors, with the observed rel tokens and crawler anchor index. `cannibalisation.md` records the proposed nofollow treatment, the duplicate occurrence and the separate recommendations for the other two domains. The PR performs no writes to those external sites.
