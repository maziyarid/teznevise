# Staged internal linking for teznevise.ir

This package is a reviewable proposal. Nothing in the theme loads it automatically. This task made only authenticated WPVibe reads, local files, a feature-branch commit and one draft PR. It did not run any installation, apply, removal, publication, cache purge or rollback against WordPress.

## Scope and ownership

`inventory.csv` records 180 tool/download objects: 165 canonical sources (64 tool/calculator surfaces, 99 downloads and two download hubs) and 15 excluded legacy redirect aliases. Both Persian and English tool pages are included. English pages receive a short Persian continuation paragraph with `lang="fa" dir="rtl"` because the requested exact anchors are Persian. Translated downloads do not exist as separate published download objects in the captured inventory.

`links.csv` is the requested source URL, anchor, target, action export. `manifest.json` adds the source ID, authored contextual sentence and activation gate. The renderer ensures one editorial link to each selected target, preserves a matching exact anchor when present, and unwraps duplicate/other-anchor links to that target within main content while preserving their text. Header/footer navigation is outside this contextual count. It adds at most two selected links per tool/download page. It does not modify stored source content, builder JSON, existing titles or SEO metadata.

There are 256 selected source-target pairs: 185 immediate and 71 deferred until separate publication of the master page. This is an ensured-link count, not a claim that every pair is newly added. Relevant pre-existing links are reused. All eleven requested guide/service relationships are represented in the manifest. `keyword-owners.csv` keeps `/service-project/` as the sole student-project owner.

## Master page decision

The authenticated inventory examined 410 published page/post/download objects and returned no draft/private/pending objects in those types. No Persian master-specific service owner was found. The English service at `/en/services/masters-thesis-guidance/` is a language counterpart, while `/download/masters-research-methods-guide/` is an educational download; neither is a second Persian service owner. Keep those distinct intents. Hreflang alignment with the English page is an editorial follow-up.

`master.html` supplies useful Persian draft copy covering inputs, research stages, methods, cost/time factors, responsibilities and FAQs. Apply creates `/thesis/master/` with `post_status=draft`, parent ID 947 (`/thesis/`), and never publishes it. It refuses an existing master page or another page with the exact Persian owner keyword, requiring consolidation review instead. Links from `/thesis/` and 70 relevant sources are dormant until the editor separately publishes the page and an explicit activation passes a direct-200/self-canonical check. Core currently guesses a download redirect for the missing path; no redirect store is rewritten. A continuing redirect blocks master-link activation.

## Evidence and before/after counts

All WordPress content was read through WPVibe. `evidence.json` records coverage and limits. The read-only database snapshot covered every published page/post/download. Its stored-content diagnostic in `stored-content-counts.csv` counts 62 existing links to the selected targets, of which nine have the selected exact anchor, across all 256 planned pairs. Those are **stored post_content counts**, not rendered totals: builder and recovered theme copy can differ.

Twenty-six main-content link captures were also read through WPVibe, covering every BlueThesis source, every requested guide relationship, and representative calculators/downloads. `rendered-link-fixtures.json` preserves their link-only DOM and main-content hashes, omitting forms, tokens, scripts and unrelated prose. `rendered-before-after.csv` is reproduced by the actual runtime on those link fixtures:

| Captured planned pairs | Before target links | Before exact anchors | After draft stage | After separate master publication |
| --- | ---: | ---: | ---: | ---: |
| 21 pairs in the rendered sample | 8 | 4 | 17 | 21 |

BlueThesis is a separate 13 -> 13 default / 0 with removal flag count across 12 pages. Chapter four contains two distinct anchors. `bluethesis-links.csv` lists all 13 occurrences, rather than discarding the extra occurrence to fit the expected count of 12. This is not a full-site rendered crawl or a claim of live after counts. Full rendered verification is supplied for the future staging run.

## Overlap with draft PR #456

Base is `main` at `8b432f7dd4ea7206758e9944f4cfe34977afb354`, independently of #456. There are zero shared changed paths with #456 as reviewed at `f53b21ed3b9d035a176abf0a2cd7de1f7e48b050`. No shared template, `functions.php`, existing SEO module or version file is edited.

There is substantial semantic overlap: #456 publishes the master page, redirects `/service-project/` to `/student-projects/`, diversifies exact anchors, changes title/meta fields and retains most BlueThesis links with nofollow. This request keeps the master draft, the existing project owner, exact anchors, unchanged live titles/meta, and audit-only BlueThesis by default. Do not activate both plans. Apply refuses the known active #456 option names, and this runtime also stops rendering if that package becomes active later. The reverse guard is not added to #456. Reconcile its ownership, publication and anchor behavior before anyone activates both packages.

## Local verification

Requires PHP 8.3 with DOM, Python 3, Bash; WordPress operations also need real WP-CLI. The WPVibe command emulator does not support `eval-file`; these scripts are deliverables for an owner-run WordPress shell, not connector commands.

```bash
python3 tools/internal-linking-20261110/validate.py
php tools/internal-linking-20261110/test-runtime.php
php tools/internal-linking-20261110/test-wordpress.php
python3 tools/internal-linking-20261110/test-install.py
php tools/internal-linking-20261110/count-rendered.php > /tmp/rendered-before-after.csv
diff -u data/internal-linking-20261110/rendered-before-after.csv /tmp/rendered-before-after.csv
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

The standalone MU file is inert until activation. Database apply creates only the draft and one package option, after writing a private journal. It checks immediate targets for direct 200 and exact self canonicals. Verification checks every source and selected active anchor, target status/canonical and the BlueThesis count. It also verifies that the master remains a draft in the default stage. Run the owner's configured staging cache purge if an existing cache serves older HTML, then repeat verification; this package does not purge caches automatically.

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
