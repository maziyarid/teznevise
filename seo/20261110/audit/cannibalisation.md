# Cross-site keyword ownership and recommendations

Observed by read-only HTTPS requests on 2026-11-10. This PR changes only teznevise.ir repository files. It makes no request that edits bluethesis.ir or teznevisan-official.ir. Common ownership is supplied by the task context; these observations do not establish which domain outranks another or prove that competing pages caused the Search Console positions.

The six primary query intents belong to the following teznevise.ir URLs. Other pages can mention these subjects naturally and link to the owner; they should not repeat the same title, H1, offer and primary optimization intent.

| Primary query | Sole target |
| --- | --- |
| انجام پایان نامه | https://teznevise.ir/thesis/ |
| انجام پایان نامه کارشناسی ارشد | https://teznevise.ir/thesis/master/ |
| انجام پایان نامه دکتری | https://teznevise.ir/thesis/phd/ |
| انجام پروپوزال | https://teznevise.ir/proposal/ |
| انجام پروپوزال دکتری | https://teznevise.ir/proposal/phd/ |
| انجام پروژه های دانشجویی | https://teznevise.ir/student-projects/ |

## bluethesis.ir proposal page

https://bluethesis.ir/proposal/ returned HTTP 200. Its observed title is `انجام پروپوزال کارشناسی ارشد و دکتری - بلوتز`; its H1 is `انجام پروپوزال کارشناسی ارشد و دکتری | مشاوره پژوهشی بلوتز`; its canonical points to itself. A simple visible-text count inside its main/article region is 2,938 whitespace-delimited words. This is an overlapping offer rather than evidence of verbatim duplication.

Recommended separate owner task: keep the BlueThesis page useful but move its purpose to statistical study design and analysis-plan review. Suggested title: `مشاوره طراحی مطالعه و برنامه تحلیل آماری | بلوتز`. Suggested H1: `طراحی مطالعه و بازبینی برنامه تحلیل`. Replace generic proposal-writing opening copy and exact generic proposal calls to action with concrete decisions about variables, measurement, sampling and analysis feasibility. Keep references to teznevise.ir/proposal/ as a contextual route for the complete research-proposal process, rather than optimizing a second page for the same generic offer. Remove broad promises that cannot be substantiated. This is de-optimization through a distinct useful purpose, not a request to hide text or change the URL.

If the owner instead decides that this page should reproduce the same service and substantially equivalent content as teznevise.ir/proposal/, consolidate the content first and then set a cross-domain canonical to `https://teznevise.ir/proposal/`. Verify equivalence, accessibility, a self-canonical on the destination and consistent internal links before that separate change. A canonical is a search-engine hint, not a guaranteed consolidation; do not combine this canonical strategy with `noindex`. A topical overlap alone is insufficient reason to canonicalize.

## Thin teznevisan-official.ir money pages

The five pages below all returned HTTP 200 and currently emit self-referencing canonicals. Counts use visible whitespace-delimited text inside the first article region, or main when no article is present; they include local UI within that region. They document the observed thinness and are not estimates of the amount of original research guidance. Extracted evidence is normalized from half spaces to spaces in this report.

| Existing URL | Observed title | Visible region words | Recommended separate change and differentiated title |
| --- | --- | ---: | --- |
| https://teznevisan-official.ir/writing-thesis/ | خدمات و راهنمای انجام پایان نامه \| تزنویسان | 379 | De-optimize into a clear description of the institute's actual research-support boundaries and intake documents. Suggested title: `ضوابط همکاری پژوهشی و مدارک شروع کار \| تزنویسان`. Send visitors needing the full thesis process to teznevise.ir/thesis/. |
| https://teznevisan-official.ir/writing-thesis/master/ | پایان نامه کارشناسی ارشد: راهنمای پژوهش \| تزنویسان | 278 | Rebuild as a focused preparation checklist rather than another general master's service landing page. Suggested title: `چک لیست جلسه پژوهشی و مدارک دوره ارشد \| تزنویسان`. Link to teznevise.ir/thesis/master/ for the complete degree-specific process. |
| https://teznevisan-official.ir/writing-thesis/phd/ | رساله دکتری: برنامه ریزی و پژوهش \| تزنویسان | 303 | Focus on research records, supervisor-meeting preparation and documenting contribution. Suggested title: `چک لیست مستندات و جلسات پژوهش دکتری \| تزنویسان`. Link to teznevise.ir/thesis/phd/. |
| https://teznevisan-official.ir/proposal/ | انجام پروپوزال ارشد قیمت مناسب \| تضمین تایید \| تزنویسان 🍀 | 461 | Remove unverified price and approval promises from metadata and copy. Focus on collecting current university forms and approval requirements. Suggested title: `فرم ها و مدارک مورد نیاز تصویب طرح پژوهش \| تزنویسان`. Link to teznevise.ir/proposal/. |
| https://teznevisan-official.ir/proposal/phd/ | پروپوزال دکتری \| طراحی پژوهش و بازبینی علمی (PhD) تضمینی به صورت فوری موسسه تزنویسان | 290 | Remove unverified guarantee and urgency claims. Narrow to documents and meeting preparation for doctoral-topic approval. Suggested title: `آمادگی جلسه بررسی موضوع و طرح دکتری \| تزنویسان`. Link to teznevise.ir/proposal/phd/. |

For each page, rewrite title, H1, description, opening copy and calls to action together so its new purpose is clear. Avoid deleting useful unique information merely to reduce word count. If the owner chooses to close a duplicated offer and publish substantially equivalent content on its mapped teznevise.ir target, cross-domain canonicalization is an alternative to the proposed differentiation. Review each page independently; the current short text and shared keywords do not establish equivalence. Do not point unrelated discipline or software pages at a broad service through canonical tags.

The official site's `/programing/` page was also checked: HTTP 200, self canonical, title `انجام پروژه برنامه نویسی | تزنویسان`, approximately 314 visible article/main words. Keep it focused on programming methods, code review and specific technologies if it remains an independent offer; avoid broad optimization for the generic student-project query. Its programming intent is narrower than teznevise.ir/student-projects/, so a blanket canonical would be inappropriate. `/military-services-project/` has a separate military-service research-project intent and should not be folded into the generic student page solely because both use the word project.

## teznevise.ir project ownership

The live https://teznevise.ir/service-project/ page returned HTTP 200 with title `پروژه های تخصصی دانشجویی | مهندسی، VHDL و شبیه سازی - تزنویسه`. The new `/student-projects/` page is the broad student-project owner. Consolidate the useful engineering and simulation explanations into that page before enabling the reviewed `/service-project/` to `/student-projects/` 301. Preserve distinct useful specialist routes such as `/service-simulation/` where their narrower purpose remains clear. The redirect map and apply plan own the teznevise.ir change; this note does not edit the other domains.

## Statistical-analysis outbound links

The crawl found **12 source pages and 13 anchor occurrences** pointing to `https://bluethesis.ir/statistical-analysis/`. The extra occurrence is a second link on `/thesis/chapter-four/`. Every observed occurrence has `rel="noopener"` and none has `nofollow`. All 13 occurrences are listed in `bluethesis-links.csv`, including the observed anchor and zero-based index in the crawler's extracted anchor list. The raw href, normalized target, source page and anchor are retained in the compressed external-edge evidence and raw crawl cache. `noopener` protects the browsing context; it does not supply `nofollow`.

The reviewed action is to retain a useful external reference while adding `nofollow`, preserving existing rel tokens. For `/thesis/phd/`, replacing the entire old landing-page content naturally removes the existing external reference; if another matching reference remains in rendered output, the runtime rule adds `nofollow`. A link should not be retained merely to meet a count. The explicit owner request is the reason for this link treatment; `nofollow` alone does not demonstrate or resolve keyword cannibalisation.

| Source page path | Occurrences | Observed anchor text |
| --- | ---: | --- |
| /download/thesis-chapter-three-four-sample-guide/ | 1 | مشاوره تحلیل آماری |
| /online-calculation-tools/cronbachs-alpha-calculator/ | 1 | مشاوره انجام تحلیل آماری |
| /proposal/english/ | 1 | بخش تخصصی تحلیل آماری در بلوتز |
| /proposal/medical/ | 1 | مشاوره تخصصی تحلیل آماری در بلوتز |
| /service-article/ | 1 | مشاوره تخصصی تحلیل آماری در بلوتز |
| /service-qualitative/ | 1 | مشاوره تخصصی تحلیل آماری در بلوتز |
| /thesis/agriculture-natural-resources/ | 1 | مشاوره انجام تحلیل آماری |
| /thesis/animal-science-veterinary/ | 1 | مشاوره تخصصی تحلیل آماری در بلوتز |
| /thesis/chapter-four/ | 2 | BlueThesis — تحلیل آماری; BlueThesis |
| /thesis/chapter-three/ | 1 | BlueThesis |
| /thesis/international/ | 1 | مشاوره تخصصی تحلیل آماری در بلوتز |
| /thesis/phd/ | 1 | مسیر تخصصی تحلیل داده و آمار در بلوتز |

External evidence sources: https://bluethesis.ir/proposal/, https://teznevisan-official.ir/page-sitemap.xml, https://teznevisan-official.ir/services-sitemap.xml and each listed live page. No Search Console access was used to invent cross-domain ranking or traffic figures.
