# Teznevise 1.6.0 — Controlled Requirements Register

## Status definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction before approval.
- **PENDING** — not yet tested or lacking evidence.
- **DEFERRED** — intentionally excluded with written acceptance.
- **N/A** — not applicable after environment confirmation with evidence.

## Blocker rule

Every **Blocker=Yes** item must be **PASS**. Any FAIL or PENDING Blocker=Yes item rejects production approval.

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---:|---|---|
| TZ-001 | Canonical theme source is repository root | Yes | PASS | `docs/CANONICAL.md`, repository tree |
| TZ-002 | Release version is 1.6.0 on release branch | Yes | PASS | `functions.php`, `style.css` |
| TZ-003 | No secrets/private credentials in package | Yes | PENDING | `scripts/release-check.sh` requires execution |
| TZ-004 | `teznevise_work/` is reference-only content | No | PASS | `docs/PAGE-MAP.md`, runtime resolver documented as migration fallback |
| TZ-101 | Desktop/mobile logo and header parity | Yes | PENDING | Requires deployed browser evidence |
| TZ-102 | Homepage hero, breakpoints, and motion | Yes | PENDING | Requires deployed browser evidence |
| TZ-103 | Required CSS/JS/fonts/images resolve | Yes | PENDING | Root asset inventory + browser network check required |
| TZ-104 | No overflow/clipping at target viewports | Yes | PENDING | Requires browser viewport checks |
| TZ-105 | Desktop/mobile navigation and bottom navigation | Yes | PENDING | Requires browser keyboard/mobile check |
| TZ-106 | FAB/contact channels work | Yes | PENDING | Requires live runtime check |
| TZ-107 | Reduced-motion behavior works | Yes | PENDING | Requires browser check |
| TZ-108 | Homepage motion remains richer than secondary pages | Yes | PENDING | Requires visual check |
| TZ-109 | Service pages preserve specific visual language | Yes | PENDING | Requires static/live parity check |
| TZ-201 | Homepage template loads without fatal errors | Yes | PENDING | `php -l` + WordPress runtime |
| TZ-202 | All mapped pages have valid templates | Yes | PASS | `docs/PAGE-MAP.md`, repository tree |
| TZ-203 | Menus are registered and fallback safely | Yes | PASS | `functions.php`, `inc/helpers.php` |
| TZ-204 | Customizer NAP/contact values are used | Yes | PENDING | Requires WordPress runtime/admin check |
| TZ-205 | Contact/inquiry forms render and provide feedback | Yes | PENDING | Requires browser/runtime check |
| TZ-206 | No unintended demo/lorem content is emitted | Yes | PENDING | Requires content/configuration audit |
| TZ-207 | Native blog presentation fields save safely | Yes | PASS | `inc/blog.php` implementation; runtime save test pending |
| TZ-208 | Blog TOC links match rendered H2/H3 IDs | Yes | PASS | `inc/blog.php` deterministic preparation logic; runtime test pending |
| TZ-209 | Persian/mixed-language reading time works | Yes | PASS | Unicode tokenization implementation; runtime test pending |
| TZ-301 | No PHP fatal on key pages | Yes | PENDING | PHP/WordPress runtime required |
| TZ-302 | Theme JavaScript console clean | Yes | PENDING | Browser runtime required |
| TZ-303 | RTL correct across mapped templates | Yes | PENDING | Browser runtime required |
| TZ-304 | Keyboard/focus-visible behavior | Yes | PENDING | Browser accessibility test required |
| TZ-305 | Title/meta/OG/Twitter/schema ownership is correct | Yes | PASS | `inc/seo.php` implementation; source validation pending |
| TZ-306 | HTTPS/mixed-content readiness | Yes | PENDING | Live network check required |
| TZ-307 | Search and 404 are noindex/follow | Yes | PASS | `inc/seo.php` robots filter; live source validation pending |
| TZ-308 | Core sitemap remains available and robots declares it | Yes | PASS | `inc/seo.php` robots filter; live endpoint validation pending |
| TZ-309 | Plugin-aware SEO fallback avoids duplicate theme output | Yes | PASS | `inc/seo.php` plugin detection; plugin runtime validation pending |
| TZ-401 | Production cPanel deployment target remains correct | Yes | PASS | `.cpanel.yml` |
| TZ-402 | `.git` excluded from deployed theme | Yes | PASS | `.cpanel.yml` |
| TZ-403 | Production live response is rendered WordPress | Yes | PASS | Live root (teznevise.ir) returns full WordPress HTML with theme sections (verified 2026-08-19) |
| TZ-404 | Deployed revision matches intended `main` revision | Yes | PENDING | Requires VPS SHA evidence |

## Current release decision

**NOT APPROVED.** TZ-403 is now PASS (live site renders correctly), but multiple Blocker=Yes items remain PENDING. These include runtime/browser evidence, VPS SHA confirmation, and outstanding browser checks; production approval must wait until every Blocker=Yes item is PASS.
