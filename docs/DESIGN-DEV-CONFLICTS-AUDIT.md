# Design ↔ Dev Conflict Audit — Teznevise WordPress Conversion

**Scope:** Every file in `teznevise_work/*.html` diffed against its mapped WordPress template (per `docs/PAGE-MAP.md`), plus `docs/CONVERSION-PLAN-1.0.md` and `docs/REQUIREMENTS.md`.
**Method:** Extracted every CSS class, `data-*` attribute, and `id` used in each static HTML file and compared it against the combined output surface of its PHP template + `header.php` + `footer.php` + `template-parts/*`. Cross-checked notable gaps by reading the actual template source.
**Result:** All 21 Blocker=Yes rows in `REQUIREMENTS.md` are still `PENDING`. Per the project's own approval truth table (`PENDING` on any Blocker=Yes ⇒ `REJECT`), this build is not release-ready. The findings below explain concretely why.

---

## Root cause: two unreconciled design systems in `teznevise_work/`

The static reference folder itself contains two incompatible UI systems that were never merged:

- **System A** — `index.html`, `blog.html`, `post-sample.html`, `about.html`, `tools.html`, `downloads.html`, `inquiry.html`, all 4 `service-*.html`. Markup vocabulary: `site-header-new`, `nav-links`, `mobile-nav-panel`, `bottom-nav`, `tz-fab-*`, Font Awesome icons, `btn-*-tz`.
- **System B** — `team.html`, `contact.html`, `privacy.html`, `tool-descriptive-statistics.html`, `404.html`. Markup vocabulary: `site-header` / `top-bar`, `nav-wrapper` / `nav-menu`, `mobile-bottom-nav`, `whatsapp-float`, `search-overlay`, `cookie-banner`, an inline SVG icon-sprite (`#icon-mail`, `#icon-phone`, …), and a light/dark **theme toggle** (`data-theme-toggle`, `#icon-sun`/`#icon-moon`, `data-pref-theme`). These files also carry `data-pplx-inline-edit` attributes, suggesting a different generation pass than System A.

`header.php` / `footer.php` were built to match **System A only** (`class="site-header-new"`). Every page sourced from System B was therefore not "converted" — its bespoke chrome and features were discarded outright and the page was rendered inside System A's shell instead. This is the source of most findings below.

---

## Findings

### 1. Cookie banner, WhatsApp float button, search overlay, and dark-mode toggle are missing site-wide
**Severity: Critical** · Blocks TZ-105, TZ-106, TZ-306

`team.html`, `contact.html`, `privacy.html`, `tool-descriptive-statistics.html`, and `404.html` all design a cookie-consent banner, a floating WhatsApp button, an in-page search overlay, and a theme switch. A repo-wide search confirms:

```
grep -rn "theme-toggle|dark-mode|data-pref-theme|prefers-color-scheme" inc/ template-parts/ header.php footer.php functions.php
→ no matches
```

None of these features exist anywhere in the PHP theme. This isn't a styling nit — it's several designed, user-facing features with zero implementation.

### 2. `/contact/` and `/inquiry/` lose almost all unique content
**Severity: Critical** · Blocks TZ-202, TZ-205, TZ-206

`page-contact.php` (60 lines) is shared by both pages per `PAGE-MAP.md` ("Same template, different meta"). In practice:
- `contact.html`'s FAQ accordion (`faq-item`/`faq-question`/`faq-answer`) and structured contact form (`form-group`, `form-input`, `form-select`, `form-row`) have no equivalent — replaced by one static `reason-item` block + `the_content()`.
- `inquiry.html`'s entire lead-capture flow is absent: the "call me back" widget (`#callMeForm`, `#callMePhone`, `.call-me-card`), the order form (`#orderForm`, `#orderFormCard`), and the Telegram/WhatsApp/Bale/Rubika messenger deep-links. There is no `<form>` element and no matching JS hook anywhere in `page-contact.php`.

### 3. Service pages lose the "specific visual language" that TZ-109 requires as a blocker
**Severity: Critical** · Blocks TZ-109 (explicit Blocker=Yes in REQUIREMENTS.md)

All four static service pages (`service-thesis/proposal/statistics/simulation.html`) design a process-showcase animation (`tz-process-panel`, `tz-panel-window`, `tz-progress`, `tz-status-pill`), a "challenge" grid, a software/tool catalog (`soft-catalog`, `soft-icon-grid`), and numbered step sequences — over 120 distinct classes/attributes combined. `page-service.php` renders **one identical generic template** for all four: hero + bullet list + `the_content()` + CTA band. None of the process/challenge/catalog sections exist in code.

### 4. The descriptive-statistics calculator isn't wired into the theme
**Severity: Critical (functional)**

`tool-descriptive-statistics.html` contains a ~350-line inline `<script>` implementing the actual calculator (parsing, mean/median/std-dev, results table, formula box). `page-tool.php`'s own doc-comment says this is expected to live in "post content," and:

```php
// inc/setup-pages.php, 'tool-descriptive-statistics' entry
'post_content' => '',
```

The seeded page ships with **empty content** — no calculator at all out of the box. Even if an admin manually pastes the raw HTML/`<script>` into the block editor, WordPress's default content filtering (`wpautop`, `wp_kses` for non-`unfiltered_html` users) will likely mangle or strip it.

### 5. Homepage/service "read more" SEO-disclosure panel is unimplemented
**Severity: High**

`index.html` and the service pages design an expandable SEO panel (`.seo-panel`, `.seo-more-btn`, `#seoGuide`, `#seoMoreContent`, `data-seo-toggle`), and `assets/js/redesign.js` even contains the working handler:

```js
document.querySelectorAll('[data-seo-toggle]').forEach(function (seoToggle) { ... });
```

No PHP template outputs this markup, so the JS binds to nothing. Dead code on one side, missing content on the other.

### 6. Blog and single-post templates drop structural sections
**Severity: High** · Blocks TZ-202

- `home.php` is a bare grid + pagination. `blog.html`'s sidebar (`.blog-sidebar`), search widget (`.blog-search`), and category widgets (`.blog-widget`) are gone.
- `single.php` has no table of contents (`.post-toc-desktop/mobile`), no sidebar (`.post-sidebar`, `.post-widget-cta`), no related-posts grid (`.related-posts`, `.related-grid`), and no breadcrumb — all present in `post-sample.html`.

### 7. Desktop dropdown submenus have no matching markup
**Severity: Medium**

The design's CSS/JS expects multi-level dropdowns via `.nav-dropdown`, `.nav-dropdown-l3`, `.nav-chevron`. `header.php` calls `wp_nav_menu()` with no custom `walker` argument, so WordPress's default walker outputs plain `.sub-menu` markup — the dropdown styling/behavior has nothing to attach to unless a custom `Walker_Nav_Menu` is added.

### 8. Tools listing and Team page lose their interactive filtering
**Severity: Medium**

`tools.html` designs a category filter (`data-category`, `data-filter`, `.tools-filters`); `team.html` designs member filter tabs (`data-filter`, `data-cat`, `.filter-tabs`). Neither `page-tools.php` nor `page-team.php` outputs this markup — both render static grids only.

### 9. Production still runtime-depends on the "reference-only" folder
**Severity: Medium** · Contradicts `docs/CANONICAL.md` single-source-of-truth intent and TZ-004

`assets/css/` at theme root only ships `header-fix.css` and `site-polish.css`. Every other stylesheet the site depends on — `redesign.css`, `motion.css`, `layout-refinements.css`, `batch-fixes.css`, `ui-round2.css`, all four `service-*.css` files — is missing from the root and exists **only** in `teznevise_work/assets/css/`. `functions.php`'s `teznevise_resolve_asset()` silently falls back to loading them from `teznevise_work/` unless an admin manually runs "promote assets" (`inc/promote-assets.php`). Until that one-time step is run, core site styling is served from the folder the plan says must not ship in production.

### 10. Version numbers disagree across the package
**Severity: Low** · Blocks TZ-002

| File | Version stated |
|---|---|
| `style.css` | `1.4.1` |
| `readme.txt` | `1.3.0` |
| `footer.php` (visible front-end text) | `1.2.0` |
| `docs/CONVERSION-PLAN-1.0.md` target | `1.0.0` |

Four different numbers, none matching the plan's stated target.

---

## What's solid

`page-about.php`, `page-team.php` (aside from the missing filter tabs), `404.php`, and `front-page.php` are genuinely well-converted — they use System A's vocabulary correctly, pull real data through `teznevise_page_field()`, and diverge from their static source only in minor, addressable ways. The problems cluster almost entirely around pages sourced from System B, plus the service/tool/contact pages whose unique interactive content wasn't carried over.

---

## Suggested next steps

1. Decide on **one** header/footer/chrome system and retrofit the System-B-sourced pages (`contact`, `privacy`, `team`, `tool-descriptive-statistics`, `404`) to it — or explicitly redesign those features (cookie banner, WhatsApp float, search overlay, theme toggle) into the System A shell if they're still wanted.
2. Rebuild `page-contact.php` into two real templates (or one template with real conditional sections) so `/contact/` and `/inquiry/` regain their forms, FAQ, and messenger widgets.
3. Rebuild `page-service.php` sections (or add page-meta-driven partials) so each service page can reproduce its process-showcase/challenge/catalog sections — required for TZ-109.
4. Either build the descriptive-statistics calculator into a real template/shortcode, or seed working, filter-safe content for it.
5. Add the SEO-disclosure panel markup to `front-page.php` / `page-service.php`, or remove the dead JS handler.
6. Add a custom nav walker for the dropdown menu structure.
7. Run "promote assets" as part of the build/release process (or copy the CSS files into the repo directly) so production doesn't depend on `teznevise_work/` at runtime.
8. Reconcile the version number across `style.css`, `readme.txt`, and `footer.php`.
9. Update `docs/REQUIREMENTS.md` statuses to `FAIL` for the blocker rows this audit reproduces, with this document as evidence, per the project's own gate process.

---

## Release Engineering Analysis — GPT-5.6 Luna

**Author:** GPT-5.6 Luna  
**Assessment date:** 2026-08-17

### Independent assessment

This audit is consistent with the release-readiness evidence I reviewed, but I would separate confirmed source-level defects from deployment/runtime observations.

The strongest finding is not the existence of two visual systems by itself. The release risk comes from the fact that the WordPress theme currently treats the static reference set as if it were a single coherent design contract while the reference set actually contains materially different page contracts. A template can therefore be internally clean and still be an incorrect conversion.

The conversion should be judged by behavior and information architecture, not only by whether a page renders.

#### Priority model

1. **P0 — Functional parity failures**
   - `/contact/` and `/inquiry/` must not collapse distinct forms, FAQs, lead capture, and messaging flows into a generic content template.
   - The descriptive-statistics tool must be a real WordPress feature, not an empty seeded page or raw script pasted into post content.
   - Service pages must retain the page-specific process/challenge/software sections required by the approved requirements.

2. **P0 — Production dependency failures**
   - Runtime loading from `teznevise_work/` contradicts the intended canonical production source.
   - A production release must be self-contained under the theme's production asset tree.
   - Asset promotion must therefore be deterministic and part of the release process, not an undocumented manual prerequisite.

3. **P1 — Navigation and interaction parity**
   - Desktop nested menus require markup and behavior compatible with the reference CSS/JS.
   - Search, mobile navigation, filters, SEO disclosure panels, and other designed interactions must either be implemented or deliberately removed from both markup and assets.
   - Dead JavaScript attached to missing markup is evidence of an incomplete conversion.

4. **P1 — Blog information architecture**
   - The blog should preserve the reference archive/sidebar/search/category affordances where those are part of the approved design.
   - Single-post output must preserve TOC, related content, breadcrumbs, and sidebar/CTA structures where required.
   - The WordPress implementation must remain editable through native post fields without requiring administrators to paste implementation HTML or JavaScript.

5. **P2 — Chrome and polish**
   - Cookie consent, theme switching, floating contact controls, filters, and minor shell differences should be reconciled after the functional contracts above are restored.
   - Version metadata must have one authoritative release version.

### Important qualification

The source audit states that all 21 `Blocker=Yes` rows remain `PENDING`. That is a release-gate observation, not proof that every row is independently defective. The implementation team should change a requirement to `PASS` only after a corresponding test has actually been executed.

Likewise, absence of a class name alone is not sufficient evidence that functionality is broken. The decisive test is whether the required user-visible behavior and semantic structure survive the WordPress conversion. This is especially important for navigation and accessibility, where WordPress may intentionally generate different markup.

### Release conclusion

The repository should remain **REJECTED for production release** until the P0/P1 conversion gaps are either implemented or explicitly removed from the approved design/requirements. The audit should be treated as a release-gate document and as the basis for subsequent implementation PRs, not as permission to merge unfinished parity work.

**Signed:** GPT-5.6 Luna
