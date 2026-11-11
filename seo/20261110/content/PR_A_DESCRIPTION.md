FACT: **PR-A — Wave 0 ONLY. Draft; do not merge, deploy or publish.** Addendum 3 supersedes #458. No page bodies, title rewrites in WordPress, new WordPress drafts, or content waves are included. Later waves require Maziyar's yes and then one reviewed sample before the rest is written.

FACT: Base is main 8b432f7. All 83 fetched remote refs and full non-shallow style.css history were searched again; 1.9.35 was not found. #454 is 4be1a44c / 1.9.31; #452 is 2e452a6f / 1.9.32. **UNKNOWN: deployed 1.9.35 provenance.** The candidate stays 1.9.31, with no version bump.

FACT: Commits are separated by step: remove the rewrite file/loader/hooks/calls; correct public service/form/request labels; remove obsolete release assertions; add a downgrade guard; implement the strict repo/local and report-only production gates; retain trust evidence and block unreviewed migration writes; prepare held inputs and review evidence. No stub or rewrite feature flag remains.

FACT: The cPanel copy entry point now refuses candidates below 1.9.35, below the installed version or with unreadable headers, before rsync. A fixture confirms 1.9.31 is refused without changing the target canary. The existing destination is unchanged. No SSH/FTPS access or deployment was performed. UNKNOWN: any server process bypassing this repository entry point. Branch-specific Vercel suppression is configured at root and react-app root to prevent the preview automation encountered on #458.

FACT: Expanded checks cover anchors, buttons, labels, options, placeholder/alt/value/title/aria-label, titles/H1, description/social metadata, decoded JSON-LD, breadcrumbs and menu names. JSON defaults are decoded; PO/POT and compiled MO paths are checked. History/docs and these PR notes/inputs are the small allowlist; future pages/** are never exempt. Static matches include U+200C, Arabic yeh/kaf/digits, the prohibited question spelling, banned claims and the expanded lexical family. Anchor budgets count main content only. Optional FAQPage must match visible text; missing FAQPage is not a defect. Mixed schema owners and review/rating schema fail.

FACT: Production is a separate **report-only** job with continue-on-error; live failures never block CI. Repo and local/staging checks are blocking.

| Check | Coverage | Result |
|---|---:|---|
| repo-static | 464 | 13573 findings; blocking FAIL |
| local-rendered | 22 | 349 findings; blocking FAIL |
| production-rendered | 404 | 1942 findings; report-only |
| legal-gate | 0 | 65 findings; blocking FAIL |

FACT: The red results are explicit, not waived: legacy runtime/import/prototype/React strings remain, and the synthetic local WordPress lacks several required routes. No content was created to manufacture coverage. Local browser checks also ran after JavaScript with POST requests blocked. Production browser checks are **not run** because certificate trust failed; no TLS bypass was used. The production server-rendered sitemap crawl did run.

FACT: Twenty-one checker tests, six version-guard tests, three PHP contracts and changed-PHP syntax checks pass. The full acceptance/release gates do not pass. The two original README/readme version mismatches remain, plus the intentional strict source-gate failure. No theme version was bumped to hide them.

FACT: Enamad business name/activity is **UNKNOWN**. The current listing request was refused at the proxy CONNECT stage (403; destination HTTP not obtained). Legal/business descriptions that may need to match the listing are retained and listed for Maziyar. They are not silently waived by the static checker. The existing source footer badge/link is unchanged and repeated on About and request CTAs. Organization trust references use the observed URL; the publisher ID remains commented out.

FACT: **Enamad risk before merge:** CHANGELOG 1.9.10/1.9.11 described the removed mechanism as Enamad-safe and warned not to remove it while Enamad is active. Removal follows Maziyar's explicit order; footer, About and Terms still require review against the actual listing. Every manifest page says legal review: not done and is unpublishable. The migration write path refuses to proceed before pending/missing reviews are resolved; archived import inputs could otherwise reintroduce the wording. No production migration was executed.

FACT: Twelve monitored production pages have unchanged REST modified_gmt and SHA-256(content.raw), including home/About, service parents, contact/inquiry, management/psychology and GAMS. See rest-before-after.csv. This is evidence for those observed fields, not proof of every possible external database change. No production WordPress write was made, and rendered hashes are not substituted for raw-field proof.

FACT: The held inputs cover 65 page/control/resource records and 222 proposed main-content links. They correct primary/secondary parents, assign GAMS to one wave, deduplicate the signal query, exclude master demand from bachelor, quarantine Aspen attribution, use the directed current tool names, spell out field paths, and provide target_state, publish order, held link patches and per-slug redirect decisions. /thesis/master/ remains deferred; distinct self-canonicals/cross-links with the downloadable methods resource are proposals only. No redirect was changed. Unknown WordPress parent IDs and all-status duplicate checks remain creation blockers.

FACT: Future quality inputs require the type-specific sections, 5–8 real visible FAQs, at least three page-specific elements, claim-to-source mapping or «نیاز به تایید مازیار», and a proposed pairwise unique 5-gram overlap below 0.12 (HYPOTHESIS, not measured because no pages are written). Word counts are guides, not gates. New drafts require [DRAFT] {slug}, explicit slug/parent/template, status=draft and blank Rank Math title/meta. Existing live bodies stay repo proposals only.

FACT: Exact current homepage/About strings, literal word deletion and one natural alternative pair marked proposal are in homepage-and-about-final-values.md. Nothing is applied to WordPress. No other owner title/meta is changed.

FACT: #456 remains 313d180c and #457 remains 34be496e; neither was edited. #456 must drop /student-projects/ and the /service-project/ redirect to it, apply that body onto /service-project/, and drop the homepage metadata containing the prohibited wording. Source/version reconciliation and future redirect removal are Maziyar's decisions.

FACT: Review files, under seo/20261110/content/:

- consult-removal-report.md — protected legal/business lines, required-route offenders, migration risk and decisions.
- rendered-check-output.md — readable tier outputs; complete findings/text in linked gzip JSON/CSV.
- homepage-and-about-final-values.md — exact current values, literal removal and proposal.
- verification-summary.json, rest-before-after.csv, removal-grep.txt and deploy-guard-proof.json — evidence.
- manifest.json, build-plan.csv, data-pack-corrected.jsonl, query-evidence-corrections.csv, quality-policy.json, sources.csv and sample-review-gates.md — held inputs only.
- link-graph.csv, publish-order.csv, links-to-patch-after-publish.csv, redirect-to-remove-at-publish.csv and master-and-guide-proposal.md — proposals; no live changes.

UNKNOWN / Maziyar review: identify the 1.9.35 source; verify the Enamad listing/legal wording; decide the scope of broad legacy source cleanup needed to clear the strict gate; provide representative staging routes for the missing local pages; confirm secondary-parent choices and the overlap threshold; later decide master publication/canonicals/redirects and sister differentiation. Reviewer/publisher identities, price/time policy, Iran search volume, PAA and backlink evidence remain unverified.

FACT: STOP after this PR-A handoff. No PR-B/C/D or further page authoring until Maziyar's yes.
