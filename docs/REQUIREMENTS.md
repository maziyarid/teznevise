# Teznevise Version 1.0.0 — Controlled Requirements Register

## Status Definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction before approval.
- **PENDING** — not yet tested or lacking evidence.
- **DEFERRED** — intentionally excluded from this release; must include owner, reason, and (if it was ever Blocker=Yes) a written client acceptance record.
- **N/A** — not applicable after environment confirmation (must include why).

## Blocker Rules (Single Authoritative Gate)

- **Yes** — Status must be **PASS**. FAIL or PENDING blocks approval.
- **No** — may remain PENDING/DEFERRED/N/A without blocking the code package.

### Approval Truth Table

| Any Blocker=Yes FAIL | Any Blocker=Yes PENDING | All Blocker=Yes PASS | Result |
|---|---|---|---|
| Yes | * | * | REJECT |
| No | Yes | * | REJECT |
| No | No | Yes | **APPROVE** |

## Release Identity

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| TZ-001 | Canonical theme source at repo root | Yes | PENDING | CANONICAL.md |
| TZ-002 | Version 1.0.0 recorded | Yes | PENDING | style.css + assets |
| TZ-003 | No secrets / private data in package | Yes | PENDING | scan |
| TZ-004 | Historical static folder non-production | No | PENDING | teznevise_work retained as reference |

## Visual and Assets / Motion

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| TZ-101 | Desktop + mobile logo | Yes | PENDING | |
| TZ-102 | Hero breakpoints + motion intact + enhanced | Yes | PENDING | |
| TZ-103 | CSS/JS/fonts/images 200 | Yes | PENDING | |
| TZ-104 | No overflow / clipping | Yes | PENDING | |
| TZ-105 | Mobile nav (right drawer) + bottom-nav | Yes | PENDING | |
| TZ-106 | FAB + contact channels | Yes | PENDING | |
| TZ-107 | prefers-reduced-motion respected | Yes | PENDING | |
| TZ-108 | Homepage richer motion than secondary pages | Yes | PENDING | |
| TZ-109 | Service pages keep specific visual language | Yes | PENDING | |

## WordPress / Templates

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| TZ-201 | Homepage template (front-page.php) | Yes | PENDING | |
| TZ-202 | All major pages have templates | Yes | PENDING | |
| TZ-203 | Menus registered & assignable | Yes | PENDING | |
| TZ-204 | Customizer contact/NAP values used | Yes | PENDING | |
| TZ-205 | Forms process without fatal + feedback | Yes | PENDING | |
| TZ-206 | No demo/lorem content in production | Yes | PENDING | |

## Quality / A11y / SEO / RTL

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| TZ-301 | No PHP fatal on key pages | Yes | PENDING | |
| TZ-302 | Console clean (theme JS) | Yes | PENDING | |
| TZ-303 | RTL correct on all templates | Yes | PENDING | |
| TZ-304 | Keyboard + focus-visible usable | Yes | PENDING | |
| TZ-305 | Basic meta / OG / title-tag | Yes | PENDING | |
| TZ-306 | HTTPS / mixed-content ready | Yes | PENDING | |

## Feedback Control

New AI findings must be classified (blocker / environment / preference / duplicate / out of scope) before any code change. An AI suggestion does not change the approved design or this gate result.
