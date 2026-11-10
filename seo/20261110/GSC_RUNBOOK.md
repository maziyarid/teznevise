# Indexing and project decisions

The complete current apply/rollback sequence is in [PROJECT_RUNBOOK.md](PROJECT_RUNBOOK.md). It includes the original on-page content package, the 84-URL project batch and the remaining indexing work. All mutations remain staged for owner approval; production has not been changed.

| Review scope | 301 decisions | New 410 | Robots Allow exceptions | Keep | Source-backed restorations | Unresolved |
| --- | ---: | ---: | ---: | ---: | ---: | ---: |
| Original 387 supplied indexing URLs | 161 | 4 | 2 | 220 | 0 | 6 non-project URLs |
| 84 dead project URLs | 84 | 0 | 0 | 0 | 0 | 0 |
| Deduplicated combined 415 URLs | 189 | 4 | 2 | 220 | 0 | 6 non-project URLs |

The scopes overlap by 56 dead project URLs, so their counts must not be added directly. Twenty-eight project URLs extend the original supplied indexing list. The immutable original inventory stays at repository-root `audit/gsc-indexing-20261110.csv`, with an identical package copy. The 135-row project inventory and 84-row decision map are also mirrored under root `audit/`.

The complete reviewed redirect map is `audit/reviewed-redirect-map-20261110.csv`: 189 deduplicated new/direct GSC decisions plus the two required base service aliases. Each row names old URL, new URL, reason, batch and evidence. No new target is the homepage, another redirect source or a loop. The existing HTTP-home to HTTPS-home redirect is retained as protocol normalization, with a specific reason rather than a content fallback.

## Controlled apply batches

- Base content: six money pages, the homepage and retained 200 technical hub, with metadata, one matching FAQ per money/hub page and contextual links.
- `project`: 84 direct 301s, with source evidence and real historical metrics for each. Use `project-apply.sh` and `project-rollback.sh`.
- `redirects`: 105 other exact redirect decisions from the original indexing review.
- `gone`: four exact obsolete author/plugin/theme requests; literal wildcard input is never expanded.
- `indexing`: two narrow Allow exceptions for `/order/` and `/order/?service`, plus sitemap policy. A physical robots file is backed up by bytes/mode first; a virtual file stays virtual.
- `links`: the current navigation cleanup map is empty because `/service-project/` remains 200. The eight new contextual hub links are in the base content option. Private account-recovery links stay protected.

The optional `restores` batch supports actual source-backed restoration but is empty for the reviewed zero-restoration outcome. No original article, software capability, author identity or price was invented to populate it. Normal scripts skip plugins/themes during CLI mutations; MU plugins still load. Every changed post, all its metadata, each changed raw option/autoload and any changed physical robots file are backed up before mutation. Transactions, checksums, journals, repeat-run no-ops and newer-edit/reverse-order guards are tested.

Thirteen exact sampled guest-login GET queries preserve their already-observed final `/inquiry/` destination directly. Bare/admin/logged-in/POST flows remain protected. Six obsolete resource-feed 404 samples point to maintained public HTML resources. Valid search/feed/API/binary delivery retains its appropriate noindex, 404, 410 or access behavior. No Disallow is added for `?s=`, `/search/` or feeds.

Core WordPress, Yoast, Rank Math and custom XML filters omit redirect/gone/private/query/noncanonical/noindex entries and protect original canonical page URLs. Protocol/host mismatches are not self-canonical. Offline production evidence may be translated to the configured staging origin; actual canonical metadata is never rewritten to make a test pass. No HTTP requests run inside WordPress sitemap filters.

## Six unresolved non-project URLs

These retain 404 and `batch=none`; none is part of the 84 project URLs:

| URL | Missing evidence |
| --- | --- |
| `/translation/` | Original translation content and current service scope |
| `/translation/scientific-translation/` | Actual scientific-translation original or equivalent service page |
| `/translation/thesis-article-translation/` | Actual thesis/article translation original or equivalent page |
| `/supplementary/thesis-to-book/` | Original book-conversion content; article extraction is a different service |
| `/supplementary/subtitle-dubbing/` | Original content and current service scope |
| `/?page_id=363` | Identity or archived database record for the deleted ID |

The exact reasons and suggested owner follow-up are in `audit/gsc-decisions-report.md`. They are visible failures in default post-apply verification. The offline `--allow-unresolved-count 6` gate permits exactly these six documented exceptions and hides no other error.

## Verification commands

```bash
python3 scripts/seo-20261110/verify.py
python3 scripts/seo-20261110/verify-gsc.py --check-inventory --allow-unresolved-count 6
python3 scripts/seo-20261110/verify-projects.py --check-map
python3 scripts/seo-20261110/verify-projects.py --snapshot-only
# Owner-run acceptance after an approved apply:
python3 scripts/seo-20261110/verify.py --live https://teznevise.ir
python3 scripts/seo-20261110/verify-gsc.py
python3 scripts/seo-20261110/verify-projects.py
python3 scripts/seo-20261110/verify-sitemap.py --require-money-pages
```

For staging, the URL verifiers accept an explicit `--base-url https://YOUR-STAGING-HOST`; the base content verifier uses `--live https://YOUR-STAGING-HOST`, and the sitemap verifier uses `--sitemap https://YOUR-STAGING-HOST/sitemap_index.xml`. Private staging privacy is preserved. Original evidence CSVs are never rewritten by staging checks. Reverse rollback order and exact shell arguments are in `PROJECT_RUNBOOK.md`; the generic GSC rollback infers batch identity from its own immutable backup and takes no `--batch` argument.
