# Static → WordPress production audit matrix

**Reference source:** `teznevise_work/`

**Builder conversion (v1.6.1):** singular HTML sources seed `_teznevise_builder_sections`.
See [`HTML-TO-BUILDER-ROADMAP.md`](HTML-TO-BUILDER-ROADMAP.md). Blog (`home.php`)
and `404.php` stay native/coded. Slugs in this table are unchanged.

**Audit baseline:** `main` at `643455415c1143efd862e01a3b644636c5a65a18`

Status meanings: **Implemented** = repository template/code exists; **Pending runtime** = requires deployed WordPress/browser evidence; **Gap** = repository work remains.

| Original HTML | Intended URL | WordPress template | Frontend | Backend/editability | SEO | Accessibility | Responsive/RTL | Remaining |
|---|---|---|---|---|---|---|---|---|
| `index.html` | `/` | `front-page.php` | Implemented | Customizer | Theme fallback + core | Structural landmarks in place | Implemented in CSS; runtime pending | Live deployment verification |
| `blog.html` | `/blog/` | `home.php` | Implemented | Native Posts | Theme fallback + core | Semantic archive/card structure | Blog CSS added; runtime pending | Live/runtime verification |
| `post-sample.html` | individual post | `single.php` | Implemented on release branch | Native post fields + WP core fields | Article/schema + social fallback | TOC labels, article semantics, navigation | Responsive blog CSS; runtime pending | Browser/runtime verification |
| `about.html` | `/about/` | `page-about.php` | Implemented | Page meta: features/timeline/policy | Theme fallback + core | Template-level | Shared RTL/responsive CSS | Runtime parity verification |
| `team.html` | `/team/` | `page-team.php` | Implemented | Page meta: stats/members | Theme fallback + core | Template-level | Shared RTL/responsive CSS | Runtime parity verification |
| `tools.html` | `/tools/` | `page-tools.php` | Implemented | Page meta: tools list | Theme fallback + core | Template-level | Shared RTL/responsive CSS | Runtime parity verification |
| `downloads.html` | `/downloads/` | `page-downloads.php` | Implemented | Page meta: downloads list | Theme fallback + core | Template-level | Shared RTL/responsive CSS | Runtime parity verification |
| `contact.html` | `/contact/` | `page-contact.php` | Implemented | Customizer NAP + page content | Theme fallback + core | Form labels/errors require runtime check | Shared RTL/responsive CSS | Form runtime test |
| `inquiry.html` | `/inquiry/` | `page-contact.php` | Implemented | Same template with inquiry meta | Theme fallback + core | Form runtime check | Shared RTL/responsive CSS | Form runtime test |
| `privacy.html` | `/privacy/` | `page-privacy.php` | Implemented | Page meta/content | Theme fallback + core | Heading/landmark runtime check | Shared RTL/responsive CSS | Runtime parity verification |
| `service-proposal.html` | `/service-proposal/` | `page-service.php` | Implemented | Page meta | Theme fallback + core | Template-level | Service CSS + shared RTL | Runtime parity verification |
| `service-simulation.html` | `/service-simulation/` | `page-service.php` | Implemented | Page meta | Theme fallback + core | Template-level | Service CSS + shared RTL | Runtime parity verification |
| `service-statistics.html` | `/service-statistics/` | `page-service.php` | Implemented | Page meta | Theme fallback + core | Template-level | Service CSS + shared RTL | Runtime parity verification |
| `service-thesis.html` | `/service-thesis/` | `page-service.php` | Implemented | Page meta | Theme fallback + core | Template-level | Service CSS + shared RTL | Runtime parity verification |
| `tool-descriptive-statistics.html` | `/tool-descriptive-statistics/` | `page-tool.php` | Implemented | Page meta + calculator | Theme fallback + core | Keyboard/form runtime check | Shared RTL/responsive CSS | Calculator runtime test |
| `404.html` | 404 | `404.php` | Implemented | N/A | `noindex` via theme robots | Runtime check | Shared RTL/responsive CSS | Live 404 verification |

## Cross-cutting parity contract

For each mapping, the release audit must verify:

- DOM hierarchy and section presence.
- Original CSS class hooks where they remain part of the design contract.
- Asset existence and WordPress-safe URLs.
- Internal links and permalink-safe navigation.
- Desktop and mobile navigation.
- RTL direction and Persian typography.
- Forms, labels, validation, and feedback states.
- Heading hierarchy and H1 behavior.
- Skip link and main landmark.
- Page title, description, canonical, robots, Open Graph, Twitter, and schema output.
- Footer, CTA, floating actions, and mobile bottom navigation.
- Empty states and 404 behavior.
- Console/network errors and missing assets on deployed pages.

## Deployment state

The current public production hostname was checked during this release cycle and returned `PLACEHOLDER3` instead of rendered WordPress HTML. This is a deployment-state failure, not a repository implementation finding. Production must not be approved until the deployed revision is synchronized with the intended `main` release and the live response is verified.

— M•Z
