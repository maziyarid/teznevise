# Staged internal linking for teznevise.ir

This package is a reviewable proposal. Nothing in the theme loads it automatically. This task made only authenticated WPVibe reads, local files, a feature-branch commit and one draft PR. It did not run any installation, apply, removal, publication, cache purge or rollback against WordPress.

## Scope and ownership

`inventory.csv` records 180 tool/download objects: 165 canonical sources (64 tool/calculator surfaces, 99 downloads and two download hubs) and 15 excluded legacy redirect aliases. Both Persian and English tool pages are included. English pages receive a short Persian continuation paragraph with `lang="fa" dir="rtl"` because the requested exact anchors are Persian. Translated downloads do not exist as separate published download objects in the captured inventory.

`links.csv` is the requested source URL, anchor, target, action export. `manifest.json` adds the source ID, authored contextual sentence, activation gate and reviewed anchor role. Maziyar's revised policy replaces the original all-exact rule: designated primary source pages use the exact money keyword; other sources and all retained secondary money links use approved LSI variants. Each enrolled source has at most one exact money anchor overall. Header/footer navigation is outside this contextual profile. The package ensures at most two selected primary links per tool/download page and preserves existing secondary occurrences with LSI wording rather than manufacturing extra links. It does not modify stored source content, builder JSON, existing titles or SEO metadata.

There are 258 selected source-target pairs on 186 enrolled sources: 187 immediate and 71 deferred until separate publication of the master page. The additions since the initial plan are one reciprocal PhD-page link to its proposal guide and one GAMS-page continuation to the student-project owner. Each supplies one relevant pathway. This is an ensured-link count, not a claim that every pair is newly added. Relevant existing links are reused. All eleven requested guide/service target relationships remain represented; the money-anchor wording follows the revised allocation. The three non-money verified relationships retain their exact anchors. `keyword-owners.csv` keeps `/service-project/` as the sole student-project owner.

## LSI evidence and allocation

`lsi-map.csv` contains eight review candidates for each of the six keywords, with the requested source and GSC impressions columns. Repository and PR #456 query evidence was searched first. No query-performance export was found: its GSC files contain indexing URL decisions and aggregate metrics. Internal site-search parameters are not treated as Google search queries. Every impressions cell is blank, meaning unknown. Of the 48 phrases, 35 occur verbatim after normalizing the captured on-site wording and 13 are explicitly labeled editorial combinations. See `lsi-evidence.md` for the search scope and review limits. The map does not claim verified search demand for the variants.

`primary-exact-sources.csv` makes every exact designation reviewable. `anchor-ratios.csv` counts selected money links:

| Target | Exact sources | LSI sources | LSI per exact |
| --- | ---: | ---: | ---: |
| `/thesis/` | 20 | 78 | 3.90 |
| `/thesis/master/`, after separate publication | 15 | 56 | 3.73 |
| `/thesis/phd/` | 1 | 4 | 4.00 |
| `/proposal/` | 13 | 52 | 4.00 |
| `/proposal/phd/` | 1 | 3 | 3.00 |
| `/service-project/` | 3 | 9 | 3.00 |

The selected money profile is 53 exact and 202 LSI links. Existing secondaries are additional LSI occurrences, so these selected-link ratios are not presented as measured full-site ratios. The future live verifier reports the entire main-content profile of enrolled sources and flags ratios outside the aim; it does not add links to force a number. Topic-specific candidates such as VHDL, chemistry and defence are excluded from generic secondary pools and used only when the source topic fits. The awkward degree-specific candidate identified in the evidence notes remains review-only.

The renderer reuses a short editorial anchor for the selected link and rewrites other money anchors to short approved variants, including existing money links whose targets are not selected on that source. For complex linked cards it preserves the card content and moves that existing link occurrence into a short contextual continuation. Repeated rendering is idempotent. The master gate also unwraps any pre-existing public link to the draft while preserving its visible text.

## Master page decision

The authenticated inventory examined 410 published page/post/download objects and returned no draft/private/pending objects in those types. No Persian master-specific service owner was found. The English service at `/en/services/masters-thesis-guidance/` is a language counterpart, while `/download/masters-research-methods-guide/` is an educational download; neither is a second Persian service owner. Keep those distinct intents. Hreflang alignment with the English page is an editorial follow-up.

`master.html` supplies useful Persian draft copy covering inputs, research stages, methods, cost/time factors, responsibilities and FAQs. Its three outgoing money links use reviewed LSI phrases, since this new draft is not a designated exact source. They are outside the selected-source allocation table and only become public after separate publication. Apply creates `/thesis/master/` with `post_status=draft`, parent ID 947 (`/thesis/`), and never publishes it. It refuses an existing master page or another page with the exact Persian owner keyword, requiring consolidation review instead. Links from `/thesis/` and 70 relevant sources are dormant until the editor separately publishes the page and an explicit activation passes a direct-200/self-canonical check. Core currently guesses a download redirect for the missing path; no redirect store is rewritten. A continuing redirect blocks master-link activation.

## Evidence and before/after counts

All WordPress content was read through WPVibe. `evidence.json` records coverage and limits. The read-only database snapshot covered every published page/post/download. Its stored-content diagnostic in `stored-content-counts.csv` counts 62 existing links to the selected targets, of which nine have the corresponding exact keyword, across all 258 planned pairs. Those are **stored post_content counts**, not rendered totals: builder and recovered theme copy can differ.

Twenty-six main-content link captures were also read through WPVibe, covering every BlueThesis source, every requested guide relationship, and representative calculators/downloads. `rendered-link-fixtures.json` preserves their link-only DOM and main-content hashes, omitting forms, tokens, scripts and unrelated prose. `rendered-before-after.csv` is reproduced by the actual runtime on those link fixtures:

| Captured selected pairs | Before target links | Before exact anchors | After draft stage | After separate master publication |
| --- | ---: | ---: | ---: | ---: |
| 22 pairs in the rendered sample | 8 | 4 | 20 links: 10 exact, 10 LSI | 24 links: 12 exact, 12 LSI |

This selected-pair sample includes the three non-money exact relationships. `rendered-anchor-profiles.csv` separately counts **every** occurrence of the six money targets in all 26 captured main elements, including unselected legacy links. It reports out-of-range sampled ratios honestly. The sample is not representative of the full allocation: some designated exact sources are outside these captures. Those rows do not trigger extra link creation or a false claim that a sample meets the full-plan ratio.

BlueThesis is a separate 13 -> 13 default / 0 with removal flag count across 12 pages. Chapter four contains two distinct anchors. `bluethesis-links.csv` lists all 13 occurrences, rather than discarding the extra occurrence to fit the expected count of 12. This is not a full-site rendered crawl or a claim of live after counts. Full rendered verification is supplied for the future staging run.

## Overlap with draft PR #456

Base is `main` at `8b432f7dd4ea7206758e9944f4cfe34977afb354`, independently of #456. There are zero shared changed paths with #456 as reviewed at `f53b21ed3b9d035a176abf0a2cd7de1f7e48b050`. No shared template, `functions.php`, existing SEO module or version file is edited.

There is substantial semantic overlap: #456 publishes the master page, redirects `/service-project/` to `/student-projects/`, diversifies all exact anchors, changes title/meta fields and retains most BlueThesis links with nofollow. This request keeps the master draft, the existing project owner, designated exact primaries plus LSI variants, unchanged live titles/meta, and audit-only BlueThesis by default. Do not activate both plans. Apply refuses the known active #456 option names, and this runtime also stops rendering if that package becomes active later. The reverse guard is not added to #456. Reconcile its ownership, publication and anchor behavior before anyone activates both packages.

## Local verification

Requires PHP 8.3 with DOM, Python 3, Bash; WordPress operations also need real WP-CLI. The WPVibe command emulator does not support `eval-file`; these scripts are deliverables for an owner-run WordPress shell, not connector commands.

```bash
python3 tools/internal-linking-20261110/validate.py
python3 tools/internal-linking-20261110/build-anchor-plan.py
php tools/internal-linking-20261110/test-runtime.php
php tools/internal-linking-20261110/test-wordpress.php
python3 tools/internal-linking-20261110/test-install.py
php tools/internal-linking-20261110/count-rendered.php > /tmp/rendered-before-after.csv
diff -u data/internal-linking-20261110/rendered-before-after.csv /tmp/rendered-before-after.csv
php tools/internal-linking-20261110/count-rendered.php --profiles > /tmp/rendered-anchor-profiles.csv
diff -u data/internal-linking-20261110/rendered-anchor-profiles.csv /tmp/rendered-anchor-profiles.csv
```

The pre-existing repository release check fails because `README.md` and `readme.txt` do not match version 1.9.31 on main. This package leaves those files untouched; #456 already changes them. The dedicated offline workflow exercises only this package and cannot deploy.

## Future staging procedure

Use a database clone preserving source IDs, the same theme/plugin versions and a correct staging `home_url`. Manifest URLs remain teznevise.ir for review. Apply remaps source/target URLs to the WordPress installation's home origin, so staging verification reads staging. Never point staging `home_url` at production. Existing source content and its original external references remain byte-for-byte unchanged in the database.

Set paths to a reviewed checkout, the intended staging WordPress root, and a new private backup directory whose parent already exists outside all public web roots. Every command defaults to dry-run. `verify.sh` defaults to showing its verification plan; `--check` performs read-only verification. Installation and database activation are separate.

```bash
export LINK_REPO=/absolute/path/to/reviewed/teznevise
export LINK_WP=/absolute/path/to/staging-wordpress
export LINK_BACKUP=/absolute/private-parent/internal-linking-staging
cd "$LINK_REPO"
bash tools/internal-linking-20261110/install.sh install "$LINK_WP" "$LINK_BACKUP"
bash tools/internal-linking-20261110/apply.sh "$LINK_WP" "$LINK_BACKUP"
bash tools/internal-linking-20261110/verify.sh "$LINK_WP" "$LINK_BACKUP"
# Explicit owner-run mutations, after reviewing the dry-run:
bash tools/internal-linking-20261110/install.sh install "$LINK_WP" "$LINK_BACKUP" --apply
bash tools/internal-linking-20261110/apply.sh "$LINK_WP" "$LINK_BACKUP" --apply
bash tools/internal-linking-20261110/verify.sh "$LINK_WP" "$LINK_BACKUP" --check
```

The standalone MU file is inert until activation. Database apply creates only the draft and one package option, after writing a private journal. It checks immediate targets for direct 200 and exact self canonicals. Verification checks every source and selected active anchor, every retained money anchor's approved LSI wording, the single-exact source limit, target status/canonical and the BlueThesis count. Its per-target profile reports exact/LSI counts and ratio deviations. It also verifies that the master remains a draft in the default stage. Run the owner's configured staging cache purge if an existing cache serves older HTML, then repeat verification; this package does not purge caches automatically.

To preview the separate BlueThesis removal plan, add `--remove-bluethesis` without `--apply`. Actual removal needs both flags. The renderer unwraps only inventoried same-anchor/same-target matches and preserves their text. It does not replace the links with another domain or change rel attributes. Repeated flagged application must retain the flag or use rollback first.

```bash
bash tools/internal-linking-20261110/apply.sh "$LINK_WP" "$LINK_BACKUP" --remove-bluethesis
# Only when the removal itself is separately chosen:
bash tools/internal-linking-20261110/apply.sh "$LINK_WP" "$LINK_BACKUP" --apply --remove-bluethesis
```

After separate editorial publication of the master, `--activate-master-links` previews or, with `--apply`, enables its 71 pending links. This flag never publishes or modifies the page. A direct redirect or wrong canonical blocks activation. Existing title/meta values remain untouched.

## Rollback

Use the same reviewed checkout, WordPress root and private backup. Restore database activation first, then runtime bytes. Both default to dry-run. Rollback refuses newer page/meta/option edits, child content or comments; preserve the backup and review those edits instead of bypassing the guard. Once an editor publishes or changes the draft, automatic page deletion deliberately stops.

```bash
bash tools/internal-linking-20261110/rollback.sh "$LINK_WP" "$LINK_BACKUP"
bash tools/internal-linking-20261110/install.sh rollback "$LINK_WP" "$LINK_BACKUP"
# Explicit restore:
bash tools/internal-linking-20261110/rollback.sh "$LINK_WP" "$LINK_BACKUP" --apply
bash tools/internal-linking-20261110/install.sh rollback "$LINK_WP" "$LINK_BACKUP" --apply
```

The journal records the previously absent activation state and the exact package-created draft fingerprint, including metadata. Runtime-file backup preserves prior bytes and permissions. Partial-apply recovery and repeated apply/rollback are covered by isolated tests. No existing source-page body, builder data, title or SEO metadata needs restoration because none is written.
