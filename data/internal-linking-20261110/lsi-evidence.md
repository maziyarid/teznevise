# LSI source review

Search priority was repository and PR #456 evidence, followed by the captured on-site WordPress content. No query-performance export with query/impression rows was found. All impression cells in `lsi-map.csv` are deliberately blank, meaning unknown rather than zero. These are review candidates grounded in on-site wording, not claimed GSC queries or verified search volumes.

## Scope searched

- Current repository tracked filenames and text for GSC, Search Console, query and impression exports.
- PR #456 branch `seo/teznevise-onpage-20261110` at `f53b21ed3b9d035a176abf0a2cd7de1f7e48b050`: `seo/20261110/PR_DESCRIPTION.md`, `seo/20261110/README.md`, `seo/20261110/audit/crawl-report.md`, `audit/gsc-indexing-20261110.csv`, `seo/20261110/audit/gsc-indexing-20261110.csv`, `gsc-targets-20261110.csv` and `gsc-seeds-20261110.json`, plus filename/text search across its SEO/audit material.
- PR #456 live description and the three returned human-authored comments at https://github.com/maziyarid/teznevise/pull/456#issuecomment-6766507243, https://github.com/maziyarid/teznevise/pull/456#issuecomment-6766695893 and https://github.com/maziyarid/teznevise/pull/456#issuecomment-6767012603.
- Existing WPVibe read-only snapshot of all published page/post/download titles and post_content, captured for this PR on 2026-11-10. Source URLs and fields are recorded per CSV row. No new WordPress writes or non-connector content reads were used.

The GSC files are indexing/URL decisions, not query-performance exports. URL parameters such as `?s=` are internal site-search strings; their presence in an indexing export does not establish that Google searchers used them. The description records aggregate clicks/impressions and money-page positions, not phrase-level impressions.

## Candidate wording

Each owner has eight distinct phrases. Verbatim phrases are checked against the normalized on-site text (markup removed, whitespace collapsed and U+200C replaced by ordinary spaces). Where no suitably varied verbatim degree-specific phrase exists, the source column explicitly labels an editorial variant and names the component wording found on that page. Such combinations are not presented as observed user queries. All phrases stay within six words. Thesis variants exclude the word disallowed by the owner; all variants exclude generic click anchors and the full exact keyword.

## Editorial variants needing Maziyar review

- «نگارش پایان نامه ارشد»: https://teznevise.ir/thesis/; combines «نگارش» + «پایان نامه ارشد».
- «نگارش پایان نامه کارشناسی ارشد»: https://teznevise.ir/thesis/; combines «نگارش» + «پایان نامه کارشناسی ارشد».
- «روش تحقیق پایان نامه ارشد»: https://teznevise.ir/thesis/; combines «روش تحقیق» + «پایان نامه ارشد».
- «راهنمای پایان نامه ارشد»: https://teznevise.ir/thesis/; combines «راهنمای» + «پایان نامه ارشد».
- «دفاع پایان نامه ارشد»: https://teznevise.ir/thesis/; combines «دفاع» + «پایان نامه ارشد».
- «فصل های پایان نامه ارشد»: https://teznevise.ir/thesis/; combines «فصل های» + «پایان نامه ارشد».
- «نگارش رساله دکتری»: https://teznevise.ir/thesis/phd/; combines «نگارش» + «رساله دکتری».
- «نگارش پروپوزال دکتری»: https://teznevise.ir/proposal/phd/; combines «نگارش» + «پروپوزال دکتری».
- «تدوین پروپوزال دکتری»: https://teznevise.ir/proposal/quantitative/; combines «تدوین» + «پروپوزال دکتری».
- «طراحی پروپوزال دکتری»: https://teznevise.ir/proposal/phd/; combines «طراحی» + «پروپوزال دکتری».
- «راهنمای پروپوزال دکتری»: https://teznevise.ir/proposal/phd/; combines «راهنمای» + «پروپوزال دکتری».
- «ساختار پروپوزال دکتری»: https://teznevise.ir/proposal/phd/; combines «ساختار» + «پروپوزال دکتری».
- «پروپوزال پژوهش دکتری»: https://teznevise.ir/proposal/phd/; combines «پروپوزال» + «دکتری».
