# Teznevise WP Theme — Remaining Redesign Work

> **Purpose:** Capture all analysis, decisions, and remaining tasks so the redesign
> can be resumed exactly where it left off. Companion to the completed work in
> commit `2fb93b4` on branch `honest-zenith-4607`.
>
> **Goal (user request):** Make the front-end of all WordPress theme pages match the
> static HTML reference in `teznevise_work/` so every page looks alike.

---

## ✅ Completed in commit `2fb93b4`

- **Synced theme assets** with `teznevise_work/`: overwrote all 11 CSS files + JS
  files in `assets/` (previously the theme CSS had diverged from the canonical
  reference).
- **Rebuilt `page-service.php`** to reproduce the full static service-page structure
  for all four services (thesis / proposal / statistics / simulation): hero + lead
  form, challenge grid, field-coverage grid, process showcase, CTA, SEO panel — all
  driven from page meta with baked Persian defaults per slug.
- **Added process-showcase JS handler** to `assets/js/redesign.js` (tab switching,
  auto-rotate, reduced-motion aware).

---

## 🧭 Root cause (from `docs/DESIGN-DEV-CONFLICTS-AUDIT.md`)

The static reference contains **two incompatible design systems** that were never
merged. The WP conversion only matched System A's chrome, dropping most page-specific
sections and the System B pages' features.

| System | Vocab | Pages | CSS file |
|---|---|---|---|
| **A** | `btn-tz`, `service-hero`, `lead-card`, `challenge-grid`, `page-hero-new`, `article-card`, `reason-item`, `cta-band`, `seo-panel`, `faq-item`/`faq-q`/`faq-a`, `tz-process-*` | index, blog, post-sample, about, tools, downloads, inquiry, all 4 services | `redesign.css` (+ refinements) |
| **B** | `btn`, `card`, `nav-wrapper`, `top-bar`, `section-title`, `grid grid-2/4`, `badge`, `bg-surface-2`, `faq-question`/`faq-answer`, `team-card`, `calc-grid`, `cta-section`, `breadcrumbs`, `toc-list`, SVG `<use>` icons, light/dark toggle | team, contact, privacy, tool-descriptive-statistics, 404 | `styles.css` (NOT enqueued in WP) |

**Decision:** Use **System A** as the unified visual language (the WP `header.php`/
`footer.php` already use it). However, many System B *component* classes are already
defined in `assets/css/layout-refinements.css` (which IS loaded), so for specialized
components (calculator, FAQ, team cards, page-hero, breadcrumbs) we can reuse those
class names as long as we don't depend on `styles.css` base classes (`btn`, `card`,
`grid`, `bg-surface-2`).

---

## 🔑 Critical CSS facts (verified)

### Enqueued stylesheets (in `functions.php` `teznevise_enqueue_assets`)
`teznevise-style` → `redesign` → `layout-refinements` → `motion` → `batch-fixes` →
`ui-round2` → `site-polish` → `header-fix` → `mobile-fixes`.

**NOT enqueued:**
- `assets/css/blog.css` — defines `.post-grid`/`.post-card`/`.blog-archive` used by
  current `home.php`/`single.php`/`post-card.php` → **these templates render UNSTYLED
  right now (broken).** Must enqueue it OR switch templates to System A blog classes.
- `assets/css/styles.css` (System B base) — defines `.btn`, `.card`, `.grid`,
  `.bg-surface-2`, `.nav-wrapper`, etc. Currently **not loaded**.
- Bootstrap RTL — loaded by static System A pages but **not enqueued** in WP. Minimal
  Bootstrap utility usage found in static markup (`text-center`, `mb-8`, `row/col`)
  — only in System B pages. Low risk; can skip enqueuing.

### Class availability in loaded CSS (key finding — most System B component classes ARE in layout-refinements.css)
```
calc-grid, calc-panel, results-table      → layout-refinements ✅
faq-question, faq-answer                  → layout-refinements ✅
team-card, team-avatar, team-detail       → layout-refinements ✅
stats-grid, stat-card, stat-value         → layout-refinements ✅
page-hero, page-hero-title                 → layout-refinements + redesign ✅
cta-section, cta-title                     → layout-refinements ✅
breadcrumbs, toc-list, steps-list          → layout-refinements ✅
filter-tabs, filter-tab, tools-filters     → NONE (need new CSS) ❌
call-me-card, inquiry-grid, inquiry-messengers, inq-msg → NONE ❌
results-empty, faq-group                   → NONE ❌
```

### System A blog classes (defined in redesign.css, used by static blog.html is BROKEN too)
`blog-layout`, `post-list`, `archive-post`, `article-card`, `article-grid`, `sidebar`,
`side-card`, `search-field`, `cat-list`, `pagination-new`, `date-badge` — all in
`redesign.css` ✅. Note: static `blog.html` itself uses `blog-card`/`blog-sidebar`/
`blog-widget` (in `layout-refinements.css`) — these also exist.

---

## 📋 Remaining tasks

### Task 1 — Unify chrome / global enqueues
- [ ] **Enqueue `assets/css/blog.css`** in `functions.php` (after `mobile-fixes`) so
      current `home.php`/`single.php`/`post-card.php` render styled. Alternatively,
      switch those templates to System A `archive-post`/`sidebar` classes. **Recommended:
      enqueue blog.css** (the existing blog templates are well-built; just unstyled).
- [ ] **Add SEO disclosure panel** to `front-page.php` (the `.seo-panel seo-disclosure`
      `#seoGuide` with `seo-reading` + `seo-more-content` + `data-seo-toggle` button).
      The JS handler already exists in `redesign.js` but binds to nothing. Copy the
      static `index.html` SEO paragraphs as defaults; drive via a new Customizer mod
      or hardcode (it's the homepage SEO block).
- [ ] **Add nav dropdown walker** for multi-level `wp_nav_menu()` so desktop
      `.nav-dropdown` / `.nav-dropdown-l3` / `.nav-chevron` markup is generated
      (currently `header.php` calls `wp_nav_menu` with default walker → plain
      `.sub-menu`, dropdown styling has nothing to attach to). Add a `Walker_Nav_Menu`
      subclass in a new `inc/nav-walker.php` and pass `'walker' => new ...` in
      `header.php`.
- [ ] **Reconcile version numbers** (`style.css`, `readme.txt`, footer text) — low
      priority; pick one (currently `functions.php` = 1.6.0, footer text says 1.5.0).

### Task 3 — System B pages (port content into loaded-CSS vocabulary)

Rewrite these templates to reproduce the static IA using classes that ARE in loaded
CSS. Bake Persian defaults (copied from static HTML) so pages render with empty meta.

- [ ] **`page-contact.php`** (serves both `/contact/` slug and `/inquiry/` slug —
      detect via `get_post_field('post_name', get_the_ID())`):
  - `/contact/`: page-hero + breadcrumbs + 4 NAP cards (use `reason-item`/`icon-box`,
    NOT `.card`) + `lead-card` contact FORM (real `<form data-test="contact-form">`
    with `.field`/`.form-grid`: name, phone, email, subject select, message textarea)
    + `.faq-wrap`/`.faq-item`/`.faq-q`/`.faq-a` accordion (bake the 4 contact FAQ items
    from contact.html; JS already wires `.faq-q`) + messenger buttons (telegram/whatsapp)
    + `the_content()` in `.longcopy` + CTA.
  - `/inquiry/`: page-hero + the lead-capture flow from inquiry.html: `.lead-card`
    `#orderFormCard` order form (name, phone, service select, degree, field, description
    → real `<form id="orderForm">`), a "call me back" card `#callMeForm` (phone input +
    submit, `data-test="call-me-form"` — add minimal CSS for `.call-me-card`/
    `.call-me-row`/`.call-me-error`), messenger deep-links (`inq-msg` buttons:
    whatsapp/telegram/bale/rubika — add minimal CSS for `.inquiry-messengers`/`.inq-msg`),
    + `the_content()` + CTA.
  - Keep `teznevise_builder_render_sections()`, WP loop, get_header/get_footer.
  - Forms: real `<form>` (method post); no server-side handling required to exist, but
    must be real forms with labels + `data-test` attrs. Escape all output.

- [ ] **`page-team.php`** — page-hero + `.stats-grid` with `.stat-card` (use
      `data-counter`/`data-suffix` attrs for the redesign.js counter animation;
      wrap numeric value) driven from `team_stats` meta + `.filter-tabs`/`.filter-tab`
      filter buttons (`data-filter`: all/med/mgt/eco/soc/eng) + `.grid`→ use
      `services-grid` or add `.grid.grid-4` CSS; member cards `.team-card` with
      `data-cat` matching, containing `.team-avatar`, `.team-name`, `.team-role`,
      `.team-detail` rows. Render from `team_members` meta OR bake the 8 default
      researcher cards from team.html (م.ر/س.ک/ا.م/ر.ح/ف.ع/ن.ت/ح.ب/ز.گ). Then
      `the_content()` + CTA. **Add `.filter-tabs`/`.filter-tab` CSS** + a
      `[data-filter]` handler in `redesign.js` (toggle hidden on `[data-cat]` cards;
      "all" shows all; mark active button).

- [ ] **`page-tools.php`** — page-hero + `.tools-filters` filter buttons (`data-filter`:
      all/descriptive/correlation/parametric/nonparametric — add CSS) + a grid of
      `.service-card` tool cards from `tools_list` meta OR the 18 baked defaults from
      tools.html (each `data-category` + FA icon + link to tool page). Add the
      `[data-filter]` handler to `redesign.js` if not added for team. Then `the_content()`
      + CTA.

- [ ] **`page-tool.php`** — for slug `tool-descriptive-statistics`: reproduce the
      CALCULATOR from the static page in a `data-test="descriptive-stats-calculator"`
      block using `.calc-grid`/`.calc-panel`/`#dataInput` textarea/`#calcBtn`/
      `#clearBtn`/`#sampleBtn`/`#resultsTable`/`#resultsBody`/`#resultsActions`/
      `#copyBtn`/`#downloadBtn`. Port the ~350-line inline `<script>` from
      `teznevise_work/tool-descriptive-statistics.html` (it's self-contained:
      parses numbers incl. Persian digits, computes mean/median/mode/std/variance/
      quartiles/range/skewness/kurtosis, renders results table, copy + CSV download).
      Output the inline script at the bottom of the template only for this slug. For
      other slugs: generic tool page (hero + `the_content()` + CTA). Bake Persian
      labels. Add `.results-empty` CSS if used.

- [ ] **`page-privacy.php`** — page-hero + hero_note (last-updated date) + the 7
      policy sections from privacy.html (collection/usage/sharing/security/rights/
      cookies/contact). Use a 2-col layout: sticky `.toc`/`.toc-list` sidebar
      (anchored links to section IDs) + `.article-content longcopy` main. Bake the
      Persian policy text from privacy.html as defaults; allow `the_content()`
      override. Add `data-test="privacy-page"`.

- [ ] **`404.php`** — reproduce 404.html in System A: centered block with big ۴۰۴
      (gradient text), message, search form (use `get_search_form()` or a
      `.search-field`), quick-link cards (home/inquiry/tools/blog/contact as
      `.btn-tz btn-light-tz` or `.reason-item`). Keep get_header/get_footer.
      `data-test="404-page"`.

### Task 4 — Blog / single / archive templates
- [ ] Confirm blog styling after enqueuing `blog.css` (Task 1). Current `home.php`,
      `single.php`, `post-card.php`, `archive.php`, `category.php`, `tag.php`,
      `search.php` use `blog.css` classes (`.post-grid`, `.post-card`, `.blog-archive`,
      `.blog-post`, `.blog-post__*`, `.entry-toc`, `.post-toc-*`). These are
      well-structured; only need the stylesheet loaded.
- [ ] **Add blog sidebar** to `home.php` and archive templates to match static
      `blog.html` IA: a `.blog-sidebar`/`.blog-widget` with search form
      (`.blog-search`), categories list (`.cat-list`), and a CTA widget — using
      classes that exist in loaded CSS (`blog-sidebar`/`blog-widget` are in
      layout-refinements; or wrap existing blog.css structure). Use `get_search_form()`
      + `wp_list_categories()`.
- [ ] Verify `single.php` already has TOC (`.post-toc-*`), related posts, prev/next,
      breadcrumbs — it does (via `inc/blog.php` helpers). Just needs `blog.css` loaded.

---

## 🛠 New CSS to add (classes not in any loaded file)
Create `assets/css/page-extras.css`, enqueue after `mobile-fixes`, containing minimal
styles for: `.filter-tabs`, `.filter-tab`, `.tools-filters`, `.call-me-card`,
`.call-me-row`, `.call-me-error`, `.call-me-ok`, `.inquiry-grid`, `.inquiry-messengers`,
`.inq-msg`, `.results-empty`, `.faq-group` (or reuse existing `.faq-wrap`). Keep
consistent with System A tokens (`--tz-*` vars from redesign.css).

## 🛠 New JS handlers to add to `assets/js/redesign.js`
- `[data-filter]` handler (used by team `.filter-tab` and tools `.tools-filters`):
  clicking a filter button hides non-matching `[data-cat]`/`[data-category]` cards,
  "all" shows all, toggles `.is-active`/`active` on the button.
- Calculator script (inline in `page-tool.php`, not in redesign.js).

---

## 📁 Files to modify (remaining)
- `functions.php` (enqueue blog.css + page-extras.css)
- `front-page.php` (SEO panel)
- `header.php` + new `inc/nav-walker.php` (dropdown walker)
- `page-contact.php`, `page-team.php`, `page-tools.php`, `page-tool.php`,
  `page-privacy.php`, `404.php` (full rebuilds)
- `home.php` (+ maybe `archive.php`/`category.php`/`tag.php`/`search.php`) — blog sidebar
- new `assets/css/page-extras.css`
- `assets/js/redesign.js` (filter handler)

## 📁 Files NOT to modify
`header.php` chrome logic (only walker arg), `footer.php`, `template-parts/*`,
`inc/*` except new nav-walker, `front-page.php` existing sections (only add SEO panel).

---

## ✅ Verification steps when resuming
1. `node -c assets/js/redesign.js` (JS syntax).
2. PHP lint not available in env — review bracket balance carefully, or install php.
3. Load static preview (pm2 `repo-app` serves `teznevise_work/` on :3002) and compare
   each WP page to its static counterpart via playwright screenshots.
4. Check `docs/PAGE-MAP.md` matrix — flip remaining rows toward parity.
