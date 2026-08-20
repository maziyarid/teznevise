# Premium stack plan — Teznevise 1.8.4

**Status:** proposed
**Branch:** `docs/premium-stack-plan`
**Against:** `main` @ `22e1b81` (`TEZNEVISE_VERSION` 1.8.4)
**Audience:** merge to `main` so cPanel VersionControl can pick it up. This PR is documentation only.

## Decision in one paragraph

Do **not** add Tailwind, Next.js, Alpine, GSAP, and Gutenberg React onto the live WordPress theme. The premium stack is already in [`react-app/`](../react-app/) (Tailwind v4, Vite 8, TypeScript, React 19, TanStack Start, Radix / shadcn-style primitives, local Vazirmatn, `@theme` tokens). The live site is still a classic PHP theme with **24 CSS files (~312 KB) fighting in last-wins order**, six vanilla JS files, and a React SiteShell overlay (`tz-react-shell` + `react-loader.css` at priority 999). Style bugs and layout regressions come from that cascade and from dual chrome — not from a missing framework. Collapse the cascade, share one token file, keep WordPress as the CMS, and treat the existing TanStack app as the **one** modern frontend. Next.js is an optional later **replacement** of TanStack, never a third runtime.

---

## What is actually running today

| Layer | Reality on `main` |
|---|---|
| Public site | Classic WP theme, PHP 8+, WP 6.4+, RTL, custom page builder |
| Version | `functions.php` / `style.css` / `readme.txt` = **1.8.4**. [`README.md`](../README.md) still says **1.8.3** |
| CSS | 24 files under `assets/css/`. `styles.css` (47 KB) is **not enqueued** |
| JS | `redesign.js`, `main.js`, `nav-touch.js`, `product-1.7.js`, `nav-dropdown.js`, `react-loader.js` |
| Tokens | [`theme.json`](../theme.json) brand `#145D4A`, Vazirmatn, `contentSize` 1180px — **dequeued on the frontend** |
| Companion app | [`react-app/`](../react-app/) TanStack Start + Vite + Tailwind v4 + React 19 + Radix |
| Deploy | VPS cron → cPanel VersionControl on **`main` only**. GitHub Actions deploy stays off |
| Reference HTML | `teznevise_work/` is historical. Do not deploy it ([`docs/CANONICAL.md`](CANONICAL.md)) |

### Enqueue order (this is the bug)

From [`functions.php`](../functions.php) + [`inc/frontend-compat.php`](../inc/frontend-compat.php):

1. **Default** `teznevise_enqueue_assets`: `style.css` (header only) → CDN Vazirmatn → CDN Font Awesome 7.1.0 → `redesign` → `layout-refinements` (55 KB) → `motion` → `batch-fixes` → `ui-round2` → `site-polish` → `header-fix` → `mobile-fixes` → `blog` → `nav-touch` → `product-1.7` → `nav-dropdown`.
2. **Priority 30** `teznevise_enqueue_compat_assets`: `header-form` → `page-extras` → `legacy-wpcode` (48 KB) → `wp-compat` → service sheets on singular.
3. **Priority 100** dequeue Gutenberg: `wp-block-library`, `global-styles`, `classic-theme-styles`. `theme.json` never reaches the browser.
4. **Priority 999** `teznevise_enqueue_parity_css`: `react-parity.css` (22 KB) + `react-loader.css` (17 KB) + `react-loader.js`. Last-wins SiteShell layer.

Anything earlier can be silently overwritten. `!important` in later sheets is how regressions survive code review.

### Heaviest sheets

| File | Size | Loaded? |
|---|---:|---|
| `layout-refinements.css` | 55 KB | every page |
| `legacy-wpcode.css` | 48 KB | every page |
| `styles.css` | 47 KB | **never** — delete or archive |
| `redesign.css` | 24 KB | every page |
| `react-parity.css` | 22 KB | every page, last-wins |
| `react-loader.css` | 17 KB | every page, last-wins |
| `motion.css` | 15 KB | every page |

Roughly **240 KB of CSS on every public request** before images or Font Awesome. That is the style-bug machine.

### Dual chrome

- PHP: `header.php` / `footer.php` / `template-parts/{bottom-nav,fab,mobile-nav,search-overlay}.php` wrapped as `tz-react-shell`.
- React: `react-app/src/components/layout/{SiteShell,SiteHeader,SiteFooter,BottomNav,Fab,SearchOverlay}.tsx`.
- CSS is copied both ways (`react-parity` / `react-loader` try to make PHP look like React). Two sources of truth for the same header.

### Bottom nav (live layout risk)

- WP fallback in `template-parts/bottom-nav.php`: 4 items (خانه / ابزارها / بلاگ / تماس).
- CSS in `motion.css` and `react-app/src/styles.css`: `grid-template-columns: repeat(4, 1fr)` and `body { padding-bottom: 72px }` under `max-width: 768px`.
- React `BottomNav` can swap in `getPublicSite().nav` of **any length**. The WP `bottom` menu location can too.
- Five or six items wrap to a second row (~120–190 px) while the page only reserves 72 px. Content sits under the bar. Do not “fix” this by adding another overlay stylesheet.

### What `react-app/` already is

Confirmed from [`react-app/package.json`](../react-app/package.json) and [`react-app/src/styles.css`](../react-app/src/styles.css):

- Tailwind **v4.3** via `@tailwindcss/vite`, `@import "tailwindcss"`, `@theme { --color-brand: #145d4a; … }`, `@layer base`.
- Vite 8, TypeScript 5.7, React 19, TanStack Start / Router / Query.
- Radix primitives + `class-variance-authority` + `tailwind-merge` + `clsx` (the shadcn stack). `src/components/ui/` currently only ships `FaIcon.tsx` — primitives are installed, the generated component kit is not.
- Local Vazirmatn woff2 (no jsDelivr).
- Full route tree: home, thesis, proposal, tools, blog, dashboard, login, contact, inquiry, team, legal, …
- `SiteShell` is the chrome the PHP theme has been chasing since 1.8.1.

Adding Next.js + Alpine + GSAP + Gutenberg React on top of this is a third/fourth runtime.

---

## Premium-stack mapping (keep / later / never)

The architecture note said: *do not add every framework.* This table is the contract.

| Idea | Verdict | Why |
|---|---|---|
| Tailwind v4 | **Keep — already in `react-app/`** | Do not compile Tailwind into the PHP theme. That would be a second utility layer on 24 sheets. |
| Design tokens | **Keep, share one file** | `theme.json` + `react-app/src/styles.css` `@theme` already agree on `#145D4A`, Vazirmatn, 1180 px. Emit CSS variables once; both runtimes consume them. |
| CSS `@layer` | **Phase 0–1 on the PHP theme** | Wrap remaining WP CSS so cascade is explicit: `reset` → `tokens` → `chrome` → `components` → `pages` → `overrides`. |
| TypeScript + Vite | **Keep — already in `react-app/`** | Phase 2: one Vite entry for WP chrome JS. Do not add a second bundler. |
| shadcn / Radix | **Keep, grow as needed** | Add `button` / `dialog` / `dropdown` in `react-app/src/components/ui/` when a screen needs them. No Material, no Bootstrap, no extra kit. |
| React as frontend | **Keep TanStack Start** | It already SSR-routes the marketing site and the dashboard. |
| WordPress | **Keep as CMS + editor** | Customizer, builder JSON, CPTs, SEO. Public HTML can stay PHP until Phase 3, then WP becomes API-only. |
| `theme.json` | **Keep as the token source** | Stop dequeuing `global-styles` *or* compile it to `assets/css/tokens.css`. Do not leave tokens in a file the frontend never loads. |
| WPGraphQL (or REST) | **Phase 3, when headless is real** | Do not add a GraphQL plugin while PHP still renders the public pages. |
| Next.js App Router | **Phase 4 optional replacement** | Only if the Next ecosystem is required. **Never run Next and TanStack together.** |
| React Gutenberg blocks | **No** | Frontend already dequeues Gutenberg. The editor is the custom builder (`inc/class-teznevise-builder.php`). A `@wordpress/scripts` block plugin is a third editor. |
| Alpine.js | **No** | React + six vanilla files is already two JS models. Alpine is a third. |
| GSAP (site-wide) | **No, unless a specific motion is underserved** | `motion.css` + `tz-rise` + hover lift + `prefers-reduced-motion` already exist. A hero timeline can take GSAP later as a **page-local** import, not a global. |
| Bootstrap / MUI / Elementor | **Never** | |
| Second CSS framework on PHP | **Never** | |

---

## Root cause of “style issues and bugs”

These are the defects a new framework will not fix:

1. **Last-wins overlay.** Every hotfix (`header-fix`, `mobile-fixes`, `batch-fixes`, `product-1.7`, `react-parity`, `react-loader`) is a new file that overrides the previous one. Specificity and order beat intent.
2. **`legacy-wpcode.css` (48 KB) on every URL.** Converted WPCode leftovers that still win against `redesign.css`.
3. **Dead `styles.css` (47 KB)** in the repo, unused, still a trap for future enqueues.
4. **CDN fonts** (jsDelivr Vazirmatn + Font Awesome) vs **local fonts** in `react-app/`. Two faces, two FOUTs, privacy/CDN risk.
5. **`theme.json` dequeued.** PHP chrome cannot see the tokens React already uses.
6. **Duplicate nav JS.** `nav-touch.js`, `nav-dropdown.js`, `redesign.js`, `product-1.7.js`, and `react-loader.js` all bind menus / drawers / search. Racey `is-open` / mega-panel bugs are inevitable.
7. **Dual chrome.** PHP SiteShell vs React SiteShell. Every visual change is done twice or faked in `react-parity.css`.
8. **Bottom-nav height vs reserved padding** when the `bottom` menu is not exactly 4 items.
9. **Stale audit.** [`docs/DESIGN-DEV-CONFLICTS-AUDIT.md`](DESIGN-DEV-CONFLICTS-AUDIT.md) still describes 1.4.x versions and “CSS missing from root”. Those findings are outdated; do not treat them as a 1.8.4 backlog. Remaining *product* gaps (contact vs inquiry, calculator seed, SEO panel markup) are content/template work, not a reason to adopt Next.js.

---

## Phases

Each phase is a mergeable PR (or a small stack of PRs) to `main`. Do not start Phase N+1 until Phase N is on production.

### Phase 0 — this week, no new technology

Goal: stop the bleeding. Still the classic theme.

- [ ] Align [`README.md`](../README.md) version **1.8.3 → 1.8.4** (policy in that file already requires it).
- [ ] Delete or move `assets/css/styles.css` out of the enqueue path (it is already unused). Do not “just in case” enqueue it.
- [ ] Host Vazirmatn locally (copy the woff2 set from `react-app/public/fonts/` into `assets/fonts/`). Drop the jsDelivr `@font-face` stylesheet.
- [ ] Self-host a **subset** of Font Awesome, or switch PHP chrome icons to the same SVG/lucide set React uses. CDN `all.min.css` is the largest render-blocking hit after the cascade.
- [ ] Wrap current enqueued CSS in `@layer tez-base, tez-chrome, tez-components, tez-pages, tez-fixes;` and put `react-parity` / `react-loader` in `tez-fixes` explicitly so the next person cannot accidentally outrank them with a new file.
- [ ] One nav script: keep `nav-dropdown.js` + `react-loader.js`, stop binding the same drawer in `nav-touch.js` / `redesign.js` / `product-1.7.js`. Delete dead `data-seo-toggle` handlers if the markup is still absent.
- [ ] Bottom nav: `grid-template-columns: repeat(auto-fit, minmax(64px, 1fr))`; `padding-bottom` from a `--tz-bottom-nav-height` variable (72 px for 1 row, 128 px for wrap). Cap the `bottom` menu at 5 top-level items in the walker / seeder.
- [ ] Page-conditional service CSS (already partly done) — do not load `service-statistics.css` on the homepage.
- [ ] Ban new `assets/css/*-fix.css` files in `CONTRIBUTING.md`. Fixes go into the sheet that owns the selector.

Exit: homepage, a service page, blog, and a mobile 375 px screenshot with the bottom bar not covering CTAs. No new npm dependency.

### Phase 1 — one token file, four CSS files

Goal: the PHP theme has a readable cascade. Still no Tailwind in PHP.

**Token contract** (single source, generated or copied both ways):

```css
:root {
  --tz-brand: #145D4A;
  --tz-brand-dark: #0f4a3b;
  --tz-brand-light: #1b765f;
  --tz-accent: #82d8b9;
  --tz-surface: #f7fcfa;
  --tz-text: #2f433c;
  --tz-muted: #5a7268;
  --tz-content: 1180px;
  --tz-font: "Vazirmatn", Tahoma, sans-serif;
  --tz-radius-card: 1.25rem;
  --tz-radius-pill: 999px;
}
```

- `theme.json` palette stays the authoring source for WP admin.
- `react-app/src/styles.css` `@theme` maps `--color-brand` → `var(--tz-brand)` (or the reverse, but **one** direction only).
- PHP enqueue becomes:
  1. `tokens.css`
  2. `chrome.css` (header, mega, search overlay, footer, bottom-nav, FAB)
  3. `components.css` (cards, buttons, forms, FAQ, steps, tones)
  4. `pages.css` (home, blog, service, tools, legal) — split with `media` / body-class if a chunk is large
- Fold `layout-refinements`, `legacy-wpcode`, `batch-fixes`, `ui-round2`, `site-polish`, `header-fix`, `mobile-fixes`, `product-1.7`, `react-parity`, `react-loader` into those four. Delete the empty husks.
- Target: **< 80 KB** combined CSS on the homepage, no `!important` except `prefers-reduced-motion`.
- Update [`docs/ASSETS.md`](ASSETS.md) with the new list. `teznevise_resolve_asset()` must not fall back to `teznevise_work/` for CSS.

Exit: grep for `wp_enqueue_style` in the theme returns ≤ 8 handles (tokens, chrome, components, pages, FA/SVG, builder-admin, builder-frontend, maybe one service page).

### Phase 2 — one TypeScript module for WP chrome

Goal: PHP pages keep working; JS is no longer five competing IIFEs.

- Add a Vite library build in `react-app/` (or `assets/src/`) that emits `assets/js/chrome.js` + `chrome.css`.
- Port: mega-menu isolation (the 1.8.4 `display:none` closed state), search overlay focus trap, mobile drawer, FAB, SEO panel if markup is restored, counter animation.
- TypeScript, no jQuery, `prefers-reduced-motion` respected.
- `functions.php` enqueues that one file. Remove `redesign.js` / `main.js` / `nav-touch.js` / `product-1.7.js` / `nav-dropdown.js` / `react-loader.js` once the port has feature parity.
- Shared class names with `SiteHeader.tsx` so PHP and React stay aligned without `react-parity.css`.

Exit: one JS handle on the public front; Lighthouse unused-JS drops; mega menu still isolated (regression test from 1.8.4).

### Phase 3 — WordPress as CMS, TanStack as the public frontend

Goal: stop maintaining two SiteShells.

**Do this only after Phases 0–2 are on production.** Headless is a cutover, not a layer.

1. Pick **REST or WPGraphQL** (not both). REST is enough if the builder JSON and CPTs are the payload. Add WPGraphQL only if nested menus + builder blocks need a real graph.
2. Point `react-app` data loaders at that API (`getPublicSite()`, blog, tools, builder sections).
3. Serve the TanStack app as the public site (already has the routes). WordPress stays at `/wp-admin/` and as the API origin.
4. PHP `header.php` / `footer.php` become a slim fallback for the rare URL the React app does not own (or a reverse-proxy 404).
5. Delete `react-parity.css` / `react-loader.css` when nothing PHP-rendered needs them.

SEO: TanStack Start already SSR. Keep permalinks. Do not change slugs as part of the cutover.

Exit: one chrome implementation (`SiteShell.tsx`). PHP theme is admin + API.

### Phase 4 — optional Next.js (replacement, not addition)

Only if there is a concrete need: Vercel/hosting policy, App Router ecosystem, or a hiring constraint. Then:

- Port `react-app/src/routes/*` → Next App Router.
- Reuse `src/components/**` and `styles.css` tokens.
- **Remove TanStack Start.** One SSR framework.
- WordPress remains the CMS from Phase 3.

If that need does not appear, **skip this phase forever**. TanStack Start is already the modern stack the architecture note asked for.

---

## Target architecture (after Phase 3)

```text
                    ┌─────────────────────────────┐
                    │  visitors (teznevise.ir)     │
                    └────────────┬────────────────┘
                                 │
                    TanStack Start (react-app)
                    Tailwind v4 · tokens.css · SiteShell
                                 │
                    REST or WPGraphQL (one)
                                 │
                    WordPress 6.8  (CMS only)
                    builder JSON · CPTs · Customizer · SEO
```

Until Phase 3 ships, the left side of this diagram is still PHP templates. That is acceptable. What is **not** acceptable is drawing Next.js next to TanStack next to Alpine next to 24 CSS files.

---

## Non-goals (explicit)

- Do not scaffold a new Next.js app in this repository while `react-app/` exists.
- Do not `wp_enqueue` a Tailwind CDN or compiled utility sheet beside `layout-refinements.css`.
- Do not install Alpine, GSAP globally, jQuery UI, Bootstrap, or MUI.
- Do not re-enable Gutenberg styles on the public site “so theme.json works” without a token compile step — that reintroduces block CSS wars.
- Do not treat [`docs/DESIGN-DEV-CONFLICTS-AUDIT.md`](DESIGN-DEV-CONFLICTS-AUDIT.md) as a current punch list; refresh it after Phase 0 or archive it.
- Do not upload `teznevise_work/` to production.
- Do not re-enable GitHub Actions deployment. Host still auto-syncs **`main` only**.

---

## Success criteria

| Check | Now (1.8.4) | After Phase 1 | After Phase 3 |
|---|---|---|---|
| Public CSS files enqueued | ~18–22 | ≤ 8 | ≤ 2 (app bundle) |
| Homepage CSS weight | ~240 KB | < 80 KB | app CSS only |
| JS handles (public) | 6 | 1 chrome bundle | app bundle |
| Token source | split + dequeued | one `:root` / `@theme` | same |
| SiteShell implementations | PHP + React | PHP + React, shared classes | React only |
| Bottom-nav overlap | possible | impossible (variable height) | same |
| New overlay CSS files | still happening | banned | n/a |

---

## Suggested PR sequence after this doc merges

1. `fix/readme-184-and-dead-styles` — Phase 0 docs/version + delete unused `styles.css`.
2. `fix/local-fonts-bottom-nav` — fonts + bottom-nav height variable.
3. `refactor/css-layers-phase0` — `@layer` wrap, no selector rewrites.
4. `refactor/css-collapse-phase1` — four files, delete husks. Largest PR; screenshot every template.
5. `refactor/chrome-js-vite` — Phase 2.
6. Headless spike (Phase 3) only after 1–5 have sat on production.

---

## Related

- [`docs/CANONICAL.md`](CANONICAL.md) — root is production; `teznevise_work/` is reference.
- [`docs/ASSETS.md`](ASSETS.md) — how CSS/JS currently resolve.
- [`docs/BRAND.md`](BRAND.md) — `#145D4A`.
- [`CHANGELOG.md`](../CHANGELOG.md) — 1.8.4 Nine services / six steps / FAQ boxes / mega isolation.
- Closed: CodeAnt tracker #398, migrator fatal #425 / PR #426. No open issues at plan time.
