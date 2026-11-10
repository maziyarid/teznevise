# Reviewed internal-link plan

The 2026-11-10 live crawl and the public WordPress REST post endpoint were reconciled before preparing `internal-links.csv`. Every planned source returned HTTP 200 with a matching self-canonical and indexable HTML in `crawl.json`. New money-page destinations are deliberately identified as planned content: `/thesis/master/` and `/student-projects/`; their existence after apply is a verification requirement, not a claim that both existed during the crawl.

The REST evidence URL is `https://teznevise.ir/wp-json/wp/v2/posts?per_page=100&_fields=id,link,title,slug,status`. It returned exactly 54 published posts, 29 in Persian and 25 in English. `rest-post-inventory.csv` preserves the exact IDs, public URLs, language and normalized titles. The Persian post sitemap alone contains 29 posts plus the blog archive, so using only that sitemap would miss the English posts. All 54 post URLs occur in the full crawl and all receive a reviewed contextual continuation link.

| Source group | Unique source pages | Planned link rows | Scope |
| --- | ---: | ---: | --- |
| Published blog posts | 54 | 54 | All 29 Persian and 25 English posts from the public REST inventory |
| Persian tools and hubs | 31 | 32 | 29 calculator/tool pages plus `/online-calculation-tools/` and `/abzar/`; the matrix-inverse calculator also links to the technical hub |
| English tools and hubs | 30 | 30 | 28 calculator pages plus `/en/tools/` and `/en/student-tools/` |
| Downloads and hubs | 101 | 102 | All 99 live `/download/` resource pages plus `/downloads/` and `/en/downloads/`; the engineering checklist also links to the technical hub |
| Additional research and service pages | 6 | 6 | `/thesis/`, `/thesis/engineering/`, `/thesis/interdisciplinary/`, `/service-simulation/`, `/service-statistics/` and `/proposal/project/` |
| Total | 222 | 224 | The original 216 money-page links plus eight contextual technical-hub links |

The plan includes every live tool and downloadable-resource page observed under these routes; pagination and taxonomy archives are not counted as individual resources. Existing navigation links remain distinct from the new in-content continuation links. Links should render with their source article or useful resource content and remain available to crawlers without requiring a click or JavaScript. Source content and original download actions are retained. English sources use English descriptive anchors that explicitly label the Persian destination.

| Destination | Added source pages | Selection rationale |
| --- | ---: | --- |
| https://teznevise.ir/thesis/ | 29 | Whole-research process, authorship, structure, scheduling and defence |
| https://teznevise.ir/thesis/master/ | 101 | Master's research methods, instrument quality, analysis interpretation and field checklists |
| https://teznevise.ir/thesis/phd/ | 13 | Research contribution, critical evidence synthesis and advanced interpretation |
| https://teznevise.ir/proposal/ | 47 | Feasibility, objectives, sampling and current university-proposal forms |
| https://teznevise.ir/proposal/phd/ | 12 | Doctoral research gaps, model justification, study power and doctoral forms |
| https://teznevise.ir/student-projects/ | 14 | General coursework/project planning, deliverables and research ethics |
| https://teznevise.ir/service-project/ | 8 | The distinct technical implementation, engineering, code validation and documented-handover hub |

Each row documents why the destination follows from the source's topic. A primary keyword has one owner in the manifest; a link to a related service does not turn the source tool or download into another money landing page. The proposal material for a specific doctoral form goes to the doctoral proposal owner rather than a generic thesis page.

## Technical-hub inbound review

`/service-project/` remains a direct HTTP 200 page with its own self-canonical. It is not redirected to `/student-projects/`. The latter owns the generic student-project query; the former explains the narrower technical workflow and links to useful specialist services. These eight reviewed sources support that distinction without inventing software pages or requiring restored legacy micro-pages.

| Direct 200 source | Reviewed anchor | Context |
| --- | --- | --- |
| /thesis/ | پشتیبانی فنی برای بخش محاسباتی و نرم افزاری پژوهش | Distinguishes technical implementation from thesis authorship |
| /thesis/engineering/ | طراحی، آزمون و مستندسازی بخش فنی پروژه مهندسی | Continues engineering implementation and validation |
| /thesis/interdisciplinary/ | هماهنگی کدنویسی و مدل سازی در پژوهش بین رشته ای | Covers the computational workstream of an interdisciplinary study |
| /service-simulation/ | خدمات فنی پروژه از مدل سازی تا ارزیابی خروجی | Places simulation inside a documented technical project |
| /service-statistics/ | آماده سازی کد و گردش کار محاسباتی پژوهش | Connects analysis scripts and reproducibility to technical delivery |
| /proposal/project/ | تعریف خروجی و معیار آزمون برای پروژه فنی | Moves from a class-project plan to implementation criteria |
| /online-calculation-tools/matrix-inverse-calculator/ | کنترل محاسبات و اعتبارسنجی پروژه های نرم افزاری | Connects numerical checks to software validation |
| /download/engineering-thesis-checklist/ | برنامه ریزی پیاده سازی و تحویل مستند پروژه مهندسی | Connects the checklist to implementation evidence and handover |

All eight sources and the technical hub were independently observed as direct 200, self-canonical and indexable in the captured live crawl. Two sources already had a money-page continuation link; six are newly covered source pages. There are eight unique technical-hub sources, 224 total planned link rows and 222 total unique sources. No robots blocks or search/feed policy changes are part of this link plan.

## Anchor audit and correction

Exact match means the whole visible anchor equals one of the six primary queries after decoding entities, stripping markup, replacing U+200C with a space and collapsing whitespace. A partial phrase inside a longer useful description is not counted as exact match. The conservative primary denominator below is links from the 222 affected sources to the six money-page URLs, including existing rendered navigation, footer and body links. Homepage, technical-hub and unrelated links do not inflate that denominator.

All **224 proposed additions contain zero exact-match anchors: 0/224 = 0%**. Of those additions, 216 point to the six primary money pages and eight point to the distinct technical hub. The expanded source set contains 377 existing exact-match money-page anchors among 1,986 existing links to the six money pages: **18.98%**. Adding the 216 money-page links alone would leave **377/2,202 = 17.12%**, above the requested 5% ceiling.

`exact-anchor-rewrites.csv` therefore contains 344 deduplicated source/target/old-text rules covering all 377 observed money-page exact occurrences and one additional exact generic student-project anchor pointing to the homepage on `/thesis/engineering/`. The homepage anchor becomes a descriptive brand/navigation label while retaining its destination. Across all rules there are 42 distinct meaningful replacement labels. A rule replaces every exact matching occurrence on its source, including repeated footer links. Old text is normalized to spaces in the reviewed CSV. Rules do not rewrite unrelated links or phrases within article prose. The runtime must compare the whole anchor's visible text and match the same-origin canonical target before replacing its inner content with escaped text; the homepage target must normalize to `/`.

For the captured baseline plus this plan, the expected primary-money result after all matching rewrites and additions is **0/2,202 = 0%**, below 5%. The one additional exact homepage anchor is also covered, so it is not hidden by the money-page denominator. This is a deterministic projection from the captured corpus; no production apply has occurred. Verify the actual rendered pages after an owner-approved apply, because managed-page content, plugins, menus or subsequent edits may change the denominator or add links. Count both additions and existing money links, report residual exact matches, and fail verification above 5% instead of claiming that the additions-only ratio proves compliance.

## Outbound review

`bluethesis-links.csv` lists the 12 affected source URLs and all 13 outbound statistical-analysis anchors, with the observed rel tokens and crawler anchor index. `cannibalisation.md` records the proposed nofollow treatment, the duplicate occurrence and the separate recommendations for the other two domains. The PR performs no writes to those external sites.
