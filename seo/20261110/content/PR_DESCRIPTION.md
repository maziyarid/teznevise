FACT: **INCOMPLETE DRAFT — do not merge, deploy or publish.** This PR completes the runtime removal and stages six money-page replacement bodies. It does not complete the requested content program: TOP10 new pages, all WordPress draft creation, Waves 2–3, field replacement bodies and thin proposal-child discovery remain unfinished. There are **zero WordPress drafts created** and no WordPress writes were attempted.

FACT: New branch `codex/seo-content-clusters-20261110` starts at `main` 8b432f7. PRs #456 and #457 are untouched; no force push. No version bump, homepage body/H1 edit, robot/config/environment edit, deployment or redirect change.

FACT: All fetched remote branch tips and fetched style.css history were checked. Version 1.9.35 was not found. #454 is 4be1a44c / 1.9.31; #452 is 2e452a6f / 1.9.32. UNKNOWN: provenance of the deployed 1.9.35. The removal is based on main, not an assumption that main is deployed.

FACT: #456 has advanced from the supplied f53b21ed to 313d180c. Both versions were inspected. #457 is 34be496e. **#456 needs these amendments:** drop `/student-projects/` and `/service-project/` → `/student-projects/`; apply the student-projects body onto `/service-project/`; drop its homepage description containing «توضیح فرایند مشاوره». Its current legacy-route inventory also needs reconciliation with this new-page plan before any publication. See `seo/20261110/content/pr-comparison.csv` for every planned URL.

FACT: The rewriting file, loader, hooks and every runtime call site are deleted. The release checker rejects the old hardcoded service phrase and function name in the seven requested public files. `removal-grep.txt` records zero runtime/release references, excluding history and this PR's own notes.

FACT: The existing footer Enamad badge and its link are unchanged. An identical badge is repeated on About and next to the service fallback request CTA. Organization schema retains existing identity fields, adds the observed trust-page URL to sameAs and leaves the registered publisher identifier commented out.

UNKNOWN: Exact Enamad business name/activity: direct fetch returned 403; WPVibe browser fetch timed out and its HTTP fallback returned 530. No registered name, activity or number was invented. Business/legal descriptions containing the consulting wording are retained and listed in `consult-removal-report.md`.

FACT: **Enamad risk before merge:** historical CHANGELOG 1.9.10/1.9.11 described the removed mechanism as “Enamad-safe public copy: consulting and tools, not ghostwriting” and warned against removal while Enamad is active. Removal follows Maziyar's explicit order. Footer, About and Terms must be reviewed against actual Enamad conditions before merge. The visible badge does not resolve that wording question.

FACT: Money-page drafts preserve live H1s for existing pages. Service-project and master drafts start from the supplied #456 bodies and are extended; doctoral drafts preserve current live body text and add relevant sections. Draft claims avoid invented fees, deadlines, credentials and outcomes. All unverified operational details require «نیاز به تایید مازیار». Official reference URLs are supplied in source sections. Titles remain proposals in a separate file.

FACT: Only final direct HTTP 200 internal targets are used by the prepared drafts. Links to the unpublished master page are omitted. Three inherited/planned links are held in `held-links.json`. Missing cluster pages are represented in the plan, not fabricated as completed pages or linked as if published.

FACT: Draft inventory (word counts include Markdown table text; these are editing counts, not a search-ranking claim):

| URL | Status | Words | Exact anchors | #456 overlap |
|---|---|---:|---:|---|
| `/thesis/` | revise: repo draft | 3689 | 0 | Y |
| `/thesis/master/` | hold: unpublished master draft | 3995 | 1 | Y |
| `/thesis/phd/` | revise: repo draft | 2649 | 1 | Y |
| `/proposal/` | revise: repo draft | 4102 | 0 | Y |
| `/proposal/phd/` | revise: repo draft | 2279 | 1 | Y |
| `/service-project/` | revise: repo draft | 4375 | 0 | Y |
| `/thesis/engineering/electrical/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/engineering/mechanical/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/engineering/civil/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/engineering/industrial/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/engineering/chemical/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/engineering/computer/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/accounting/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/economics/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/theology-islamic-studies/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/education/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/project/engineering/chemical-industrial/chemical-engineering-project/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/simulation/network/cisco-packet-tracer/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/simulation/chemical-process/aspen-hysys/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/engineering/electrical/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/engineering/electrical/ees/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/programming/hardware/hspice/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/programming/hardware/vhdl/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/programming/hardware/silvaco/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/simulation/hydrology/hec-ras/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/engineering/mechanical-civil/opensees/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/engineering/mechanical-civil/plaxis/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/engineering/chemical-industrial/econometrics-project/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/engineering/mechanical-civil/aerospace-project/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/assignments/computer-science/uml-diagrams/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/programming/ai-data/signal-processing-matlab/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/gams/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/project/programming/languages/r-programming/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/simulation/electrical/proteus/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/student-research/university-projects/bachelor-project/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/statistics/general-statistics/ibm-spss-statistics/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/project/statistics/sem/amos/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/project/statistics/sem/smart-pls/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/project/simulation/electrical/matlab-simulink/` | hold: NOT DRAFTED | 0 | 0 | Y |
| `/thesis/engineering/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/management/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/psychology/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/law/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/history/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/philosophy/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/humanities/social-sciences/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/medical-health/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/pure-science/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/international/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/animal-science-veterinary/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/art-architecture-media/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/agriculture-natural-resources/` | hold: NOT DRAFTED | 0 | 0 | N |
| `/thesis/interdisciplinary/` | hold: NOT DRAFTED | 0 | 0 | N |

FACT: Shared money-page comparison:

| URL | #456 | #457 | This PR |
|---|---|---|---|
| `/thesis/` | body and metadata proposal | link plan; see comparison CSV | body file only; existing H1 retained where live |
| `/thesis/master/` | body and metadata proposal | master draft | body file only; existing H1 retained where live |
| `/thesis/phd/` | body and metadata proposal | link plan; see comparison CSV | body file only; existing H1 retained where live |
| `/proposal/` | body and metadata proposal | link plan; see comparison CSV | body file only; existing H1 retained where live |
| `/proposal/phd/` | body and metadata proposal | link plan; see comparison CSV | body file only; existing H1 retained where live |
| `/service-project/` | student-projects body/redirect proposal | link plan; see comparison CSV | body file only; existing H1 retained where live |

FACT: Authorized About metadata proposal, PR only:

- Before title: درباره تزنویسه | مشاوره پژوهشی و تحلیل داده
- After title: درباره تزنویسه | تحلیل داده
- Before description: با رویکرد تزنویسه در مشاوره پژوهشی، روش شناسی، تحلیل داده، بازخورد علمی و استفاده مسئولانه از ابزارهای پژوهش و هوش مصنوعی آشنا شوید.
- After description: با رویکرد تزنویسه در روش شناسی، تحلیل داده، بازخورد علمی و استفاده مسئولانه از ابزارهای پژوهش و هوش مصنوعی آشنا شوید.

FACT: The removed noun phrase is «مشاوره پژوهشی» plus its connecting conjunction/comma; remaining words are retained. The homepage title/meta proposal exactly follows the latest addendum and is stored in `metadata-proposals.json`. No values were applied to WordPress.

FACT: Validation:

- PHP syntax checks pass for every changed PHP file.
- Four rendered-scanner tests pass; Organization reference tests pass, including existing sameAs preservation, nested graph handling, idempotence and absent registration ID.
- Six prepared files pass the isolated file/link/schema checks. This is explicitly **not full acceptance**.
- Rendered live baseline: 47 offending elements on the eight required pages. Every offender is printed in `rendered-before.json` and tabulated in the copy-removal report.
- Pandoc-rendered replacement bodies: zero offending anchors/H1/accessible names across the six bodies. This does **not** prove that an integrated WordPress page passes with sitewide menus or stored content.
- Full acceptance: FAIL — 202 errors, including missing planned drafts and existing live/stored wording. The existing theme-integrity workflow runs the verifier and retains its report. CI is intentionally blocking while acceptance is incomplete.
- Release contract: FAIL on two unchanged main-branch metadata mismatches (`README.md` and `readme.txt` versus 1.9.31). No version changes were made to mask them.
- Integrated local WordPress rendering was attempted through the existing local WP-CLI install; it failed with “Error establishing a database connection”. No production database/configuration was changed.
- HTTP/hash comparison: all eight baseline pages remained HTTP 200; 3/8 exact HTML hashes matched. The other response-byte differences are UNKNOWN, not proof of unchanged content. `live-before-after.csv` records both hashes. No WordPress mutation was called by this run; snapshot hashes alone cannot prove absence of all external changes.

UNKNOWN / open questions for Maziyar:

- Identify the deployed 1.9.35 source and verify Enamad's registered activity/business wording.
- Reconcile #456 with `/service-project/` ownership and the new project/software URLs before merge.
- Approve timing for `/thesis/master/` publication and redirect removal; this PR performs neither.
- Confirm the latest fixed homepage title/meta wording versus the earlier alternative proposals. Existing homepage body and H1 are unchanged.
- Decide sister-page differentiation later; no cross-site links or sister-site edits are made.
- Supply real reviewer names, registered publisher details, price/time policy, and topic-specific service availability.
- Iran search volume, Google People Also Ask and backlink evidence remain UNKNOWN. Supplied SERP evidence is DuckDuckGo/independent search, not Google.
- Finish the 48 planned cluster/field replacement bodies plus thin-proposal-child discovery, create only eligible new WordPress drafts after duplicate checks, and obtain a passing integrated rendered check before this can be considered the requested deliverable.
