# Changelog

## 1.9.6 — 2026-08-22 — WordPress-wide Classic content and mobile/backend hardening

- Flatten the 1.9.6 files into the active theme root so cPanel deploys them over production, not into an unused nested `teznevise/` folder.
- Replace the homepage-only static «مشاهده بیشتر» block with one dynamic, sanitized Classic Editor disclosure on every page immediately before the footer.
- Separate calculators/forms from editorial copy. A versioned non-destructive importer moves recovered prose into `post_content` and preserves functional shortcodes in private meta.
- Never overwrite administrator-authored Classic Editor content, including copy shorter than 40 characters; store a backup/revision first. Display-quality length is used only for fallback copy.
- Demote embedded H1 headings and namespace editor DOM IDs with a per-ID occurrence counter so pages keep one H1 and unique IDs.
- Centre repeated mobile card systems and the mobile footer; constrain drawer scrolling and reserve safe-area space for bottom navigation/contact controls.
- Mobile cards, steps, FAQs, stats, testimonials, service tiles, and the footer are centred (icon on top, title and copy in the middle). Footer SSL/enamad seals are a compact inline badge.
- FAQ titles no longer print leftover accordion arrows; answers stay visible as numbered boxes. Process steps keep the colourful number.
- Mobile drawer is a column accordion. Desktop mega menu stays RTL with `auto-fit`. `hotfix-196.css` is enqueued last (priority 100).
- Redirect the dead `/statistics/` URL to the published `/service-statistics/` page and update internal theme links.
- Restrict public REST user routes, public author archives, and `?author=` probes; harden browser headers; rate-limit accepted lead submissions after validation; distinguish stored vs delivered leads; require HTTPS for AI providers; and use atomic quota/burst locks.

## 1.9.5 — 2026-08-22 — RTL nav, classic «مشاهده بیشتر», WXR copy, UI audit

- Top-level menu no longer prints Font Awesome icons (those empty squares). Dropdown chevron is a CSS triangle so it cannot tofu. Mega panel is RTL with wrapping labels.
- Shared «مشاهده بیشتر» box on every singular page: classic-editor HTML, never raw shortcodes. Footer prints it once if a template forgot. Calculators still run even when the page has no builder sections.
- 22 legal/about/team pages from the 2026-08-21 WordPress export are seeded (`inc/wxr-classic-content.json`). Visiting wp-admin once writes that HTML into the classic editor when the live page still only has `[tz_*]`. Old `tel:0933…` numbers in that copy rewrite to the current line.
- `[gravityform]` is not Gravity Forms — it always renders the native lead form. Lead forms POST to WordPress (no PII in the URL / WhatsApp GET).
- Inquiry form sits above contact cards. Hours stay «شنبه تا پنجشنبه، ۹ تا ۲۱». Discount copy stays ۱۰٪.
- Competing URLs 301 to one canonical (`/contact/` → `/contact-us/`, `/service-thesis/` → `/thesis/`, `/tools/` → `/online-calculation-tools/`, …).
- Homepage no longer shows internal design-note copy. Article bylines never print raw logins like `akumumono`. Footer section titles are not heading-level skips.
- PR #448 (builder-route 500 analysis) was already merged; leftover rendering no longer calls `the_content()` a second time, which was a likely source of those fatals.



## 1.9.4 - 2026-08-21

- RTL mega menu: full-width panel, icons at inline-start, nested lists no longer position LTR.
- Classic-editor leftover ("مشاهده بیشتر") on homepage, default pages, and single tools.
- Gravity Forms shortcodes always render the native lead form.
- Telephone CTAs use `teznevise_tel_href()` so they never become `/tel:` routes.
- React app: FA7 header/dashboard icons, tool AI chat with thinking/collab/credits, redesigned user dashboard.

## 1.9.3 — 2026-08-21 — Restore bootstrap, mega nav, tool AI, dashboard

- Restore the 1.9.2 WordPress bootstrap. `functions.php` no longer requires missing Underscores files (`inc/template-functions.php` and friends), which fatally took the live site down.
- Enqueue the real public bundles (`tokens`, `components`, `pages`, `chrome`, `modernization`) and self-hosted Font Awesome so header icons match the compact chrome.
- Mega-menu panel is a 3-column card with wrapping labels (no truncated «علوم پا»).
- Tool pages render a vanilla JS assistant under the calculator. First message: «اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری». Agent name under every bubble. Per-agent API endpoint/model, visible thinking, collaboration modes, logged-in history, guest/signed-in caps and Tezcoin cost from Settings → TezNevise AI.
- Account dashboard adds an AI history tab and restyled tabs.
- `tel:` CTAs no longer become `/tel:…`. Trust seals only link when a real Enamad/Samandehi URL is configured.

See `docs/CHANGELOG-1.9.3.md` for the attached audit matrix.


## 1.9.2 — 2026-08-21 — Calculator runtime and complete page content coverage

- Always enqueue `legacy-wpcode.css` so dumped calculator HTML (`.tzss-*`, `.tzpc-*`, …) is no longer unstyled on live.
- Add `assets/js/calculators.js` to hydrate sample-size (Cochran + Morgan), confidence pills, tabs, FA icons, and FAQ toggles — the JS never made it into saved post_content.
- Widen the leftover-shortcode CSS matcher so `page-tool.php` and `*calculator*` slugs pick up styles even when the page stores expanded HTML instead of `[tz_sample_size]`.
- Keep interactive calculator, hub, and service-CTA shortcodes visible next to the shared classic-editor disclosure.
- Complete missing template and presentation metadata for existing calculator pages without overwriting populated editor content; document the production catalogue audit for all 105 live pages.
## 1.9.1 — 2026-08-21 — Export templates, comments, RTL article, footer

- Consume enclosing interactive shortcodes (`[tz_descriptive]…[/tz_descriptive]`) so leftover classic copy never shows the closing tag.
- Classic-only pages stay fully visible; the `مشاهده بیشتر` disclosure is only used next to builder/interactive widgets.
- Memoize page-URL candidate lookups and keep footer trust seals on their own row.
- Comment form: label above the textarea, branded submit button, flex layout.
- RTL article grid: wide reading column on the right, TOC on the left, Persian “در این مقاله” label.


## 1.9.0 — 2026-08-21 — Editorial Signal modernization and compatibility hardening

- Add `assets/css/modernization.css` as a final, namespaced compatibility layer for Bootstrap semantics, focus visibility, responsive sizing, safe-area behavior, and reduced motion.
- Make selected React entrances opt in through `.tz-reveal` without disabling the established shared-template motion system.
- Strengthen async ticket/comment loaders against stale responses, make the disabled-auth fallback hook-safe, and test Unicode sanitization for skin-tone modifiers and meaningful symbols.
- Align metadata with WordPress 6.9 and PHP 8.3+, retaining the PHP 8.5 readiness review and source-only release validation.

## 1.8.8 — 2026-08-21 — Live-theme audit and RTL remediation

- Correct the physical RTL article grid: the reading column is now the wide right-hand column and the bounded TOC is the left-hand sidebar.
- Style native WordPress comment, reply, nested-comment, logged-in, and logged-out states with accessible labels and focus treatment.
- Stabilize desktop/tablet header density, responsive footer grids, long contact values, trust marks, and legal-link wrapping.
- Restore a Persian keyboard skip link and add consistent visible focus indicators across interactive controls.
- Document the production audit matrix, resolved defects, and CMS/server-owned follow-ups in `docs/LIVE-THEME-AUDIT-2026-08-21.md`.
- Establish Teznevise Design System v2 with shared runtime/editor/React tokens, component contracts, editorial patterns, and a phased Premium v2 architecture roadmap.
- Add progressively enhanced instant search, focus mode, bounded reading-size controls, responsive image presets, deferred public JavaScript, and local font preloading.

## 1.8.7 — 2026-08-21 — Single-post layout, Persian chrome, local icons

- Article body is the wide main column; table of contents is the 300px sticky sidebar. Named grid areas so RTL no longer swaps them.
- TOC title is «در این صفحه» (was English “On this page”). Prev/next, related, and read-time copy are Persian.
- Top-level desktop nav icons and empty chevron buttons are hidden so the 72px pill stops showing blank squares. Mega-menu and mobile keep icons.
- Font Awesome 7.1 is self-hosted under `assets/vendor/fontawesome/` so icons no longer depend on jsDelivr.
- Reading progress bar, breadcrumb, share-bar styling, and a post-sidebar inquiry card.

## 1.8.6 — 2026-08-21 — Unlayered last-wins (header, hero, nav)

- Stop wrapping bundled CSS in `@layer`. Layers invert `!important`, so `layout-refinements` beat `react-parity` and the sticky header went transparent (green orbs showing through).
- Unlayered last-wins restore a white header, compact 72px nav, 176px hero order button, visible orbit tags, and bottom-nav height token.
- Snapshot of live content: `docs/wp-rest-export.json` (105 pages, 11 posts from the REST API). No WXR file existed in the repo.


## 1.8.5 — 2026-08-21 — CSS cascade collapse, local fonts, one chrome JS

- Public CSS is four layered files: `tokens.css`, `components.css`, `pages.css`, `chrome.css`. The 24-file last-wins pile is no longer enqueued.
- Vazirmatn is self-hosted from `assets/fonts/` (jsDelivr font-face stylesheet removed).
- One public JS handle: `chrome.js`. Duplicate search-overlay bindings from `main.js` are gone. `tezneviseProduct` localizes onto `teznevise-chrome`.
- Bottom nav is capped at 5 items, uses `data-nav-count` + `--tz-bottom-nav-height`, and no longer hard-codes `repeat(4, 1fr)` / 72px padding that covered CTAs.
- `legacy-wpcode.css` (48 KB) and service sheets load only on pages that need them — not the homepage.
- Unused `assets/css/styles.css` archived. Ban new `*-fix.css` overlay files.
- Rebuild bundles with `python3 scripts/build-frontend-bundles.py`. Source sheets stay in `assets/css/` for that rebuild.


## 1.8.4 — 2026-08-20 — Nine services, six steps, FAQ boxes, mega isolation

- Homepage services are 9 cards: تیم پژوهشگران → شبیه‌سازی, plus تحلیل کیفی, انجام پروژه دانشجویی, انجام مقاله. Render-time rewrite updates stale builder JSON so cPanel rsync shows them without an admin re-seed.
- Process steps are 6 centered cards (`شش قدم تا یک مسیر پژوهشی روشن`) instead of 4.
- Inner-page cards pick up the same 9-tone palette as the homepage.
- FAQ blocks are numbered colorful boxes with answers always visible — no accordion arrows.
- Mega menu only opens on the nav item (or keyboard `.is-open`). Closed panels are `display:none` so mid-page hover cannot reveal them.
- Font Awesome 7.1.0. React `react-app/src` absorbs the WordPress page conversion (tools/thesis/legal single-page layout).


## 1.8.3 — 2026-08-20 — Mega width, stacked heroes, card steps, motion

- Desktop mega menu stretches the full nav bar (3-column grid). `left/right: auto` after `inset-inline` had shrink-wrapped it into a scrolling strip.
- Builder `.section-head` stacks eyebrow / title / lead so heroes and FAQs are no longer a two-column split.
- Process steps are compact cards in a wrapping grid, not full-width empty rows.
- CTA buttons on dark bands stay a pill (`flex: none`, nowrap) instead of a wrapped white square.
- Feature/FAQ cards use `auto-fill` so the last row keeps the same card width; icon stacks above the title.
- Hover lift and press-scale on cards and buttons; mega panel fades in. `prefers-reduced-motion` still wins.


## 1.8.2 — 2026-08-20 — SiteShell chrome: compact nav, centered search, page rhythm

- Primary-nav labels match the React header (پایان‌نامه / پروپوزال / ابزار / تماس). Inquiry and About stay in the CTA cluster, not the link row.
- Mega panels use `.nav-panel.mega` markup; `#` group items render as headings, not dead links.
- Search overlay is forced to `position:fixed; inset:0; z-index:5000` so it never opens under the sticky bar. Tab trap uses `getClientRects()`.
- `react-loader.css` is the last-wins SiteShell layer (flex column, sticky header, white footer logo, blog/form cards).
- Blog archive header and lead forms pick up the same hero/card rhythm as the React pages.


## 1.8.1 — 2026-08-20 — Front-end never loads the migrator

- `shortcode-to-builder-migrator.php` is required only in wp-admin / WP-CLI. A parse error in that file can no longer white-screen the public site (issue #425).
- WordPress chrome boots through the same SiteShell wrapper as the React app (`tz-react-shell`): flex column, sticky header, React class aliases, white footer logo.
- Bump migration file stamp so VPS rsync replaces any leftover 1.7.6 copy.


## 1.8.0 — 2026-08-20 — Mega menu, footer, search overlay

- Desktop submenus are full-width mega panels (grid + scroll) so long tool/thesis lists stay on screen, with Font Awesome icons on every item.
- Closed panels keep `pointer-events: none` so hovering the page no longer opens a menu.
- Search overlay sits above the header, centered in the viewport (no upward clip under the bar).
- Footer is three columns (brand / services / contact). Company and legal links sit in a horizontal strip above the copyright.
- Footer logo is the mark only, inverted to white, with no plate behind it.
- Page heroes, builder cards, blog archive, and forms get denser rhythm and readable contrast.


## 1.7.9 — 2026-08-20 — Desktop header + skip-link

- Remove the English "Skip to content" link from `header.php`. It was painted on every page because the theme never shipped `.screen-reader-text`.
- Compact the desktop bar: hide walker chevrons (hover/focus-within still opens dropdowns), drop Inquiry/About items that already exist as CTAs, and override the 88px / 34px gap from layout-refinements.


## 1.7.8 — 2026-08-20 — Hotfix: migrator parse error (issue #425)

- Close the duplicated `[gravityform]` `if` in `teznevise_migration_parse_content()` that left an unclosed `{` from line 370 to EOF and fatally broke every request that loaded the theme.
- Coerce shortcode `title` / `text` attributes to strings before sanitization.


## 1.7.7 — 2026-08-20 — Empty-builder fill vs field overwrite

- Empty, non-manual builder JSON can be filled without overwriting existing `_teznevise_*` fields or a non-default template.
- `default-seed` pages still refresh fields/template even when builder JSON is empty.
- Dry-run reports field/template writes, not just builder JSON.

## 1.7.6 — 2026-08-20 — Remaining shortcode mappings + dump removal

- Map `[tz_price_box]`, `[tz_price_cta]` (including bare `[tz_price_cta]` and `text=`), `[tz_calculation_hub]` (with optional attributes), and `[tz_careers_terms]`.
- Seed Cohen's kappa, ICC, KR-20, and goodness-of-fit calculator pages.
- Catalog migration keeps `category_slug` / `count` and hydrates items; render-time hydrator honors the stored category.
- Empty parses write a content-hashed skip marker so later edits or schema bumps can retry.
- Fallback shortcodes for price CTA, calculation hub, and careers terms.
- Remove credential-bearing `docs/sep_posts.*` and WPCode dump from the tree.

## 1.7.5 — 2026-08-20 — Accessible nav disclosure

- `Teznevise_Nav_Walker` renders a separate `.nav-dropdown-toggle` beside parent items so the parent link always navigates.
- Desktop and mobile menus share the walker; hover-to-open is desktop-only.
- `nav-touch.js` no longer intercepts the first tap on walker-rendered parent links.
- Menu `target` / `rel` / `title` attributes and `nav_menu_*` filters are preserved.

## 1.7.4 — 2026-08-20 — Chrome URL and search a11y

- Blog chrome links resolve from `page_for_posts` (then a seeded `blog` page, then home) instead of a hard-coded `/blog/` path.
- Setup seeds `blog` as the posts page when unset, and seeds `testimonials`.
- Header “بازخورد مشتریان” now points at `/testimonials/` instead of `/contact/`.
- Search overlay traps Tab, restores focus to the trigger, and keeps `hidden` / `aria-hidden` / `aria-expanded` in sync.

## 1.7.3 — 2026-08-20 — Footer and FAB polish

- FAB stays 18px from the physical right (no RTL inset reset).
- Footer social icons no longer sit on the logo; extra bottom padding clears the mobile tab bar.

## 1.7.2 — 2026-08-20 — React visual parity

### Changes

- Opaque sticky header so the hero «ثبت سفارش» orb no longer paints through the nav.
- Desktop: hamburger hidden; search / Tezcoin / profile stay visible.
- Mobile: hamburger on the physical right, logo centered and sized down, icons on the left — no overlap.
- FAB pinned to the physical right; chat items open upward toward the left.
- Bottom nav uses icon + short label (خانه / ابزارها / بلاگ / تماس).
- Footer logo, spaced legal links, trust seals as a row, dark background matching the React replica.
- Builder `[data-reveal]` no longer leaves sections invisible if JS is late.

## 1.7.1 — 2026-08-20 — Account, tickets, payments, AI agents

### Changes

- Front-end `/account/` dashboard: wallet, buy Tezcoin (Zarinpal / AqayePardakht), ledger, referrals, profile bonus, tickets with isolated vault uploads, project tracking.
- 1000 Tezcoin after a complete profile (name, phone, university, field, degree). Referral bonus pays both sides once.
- Legal pages auto-seeded: terms, cookies, refund, research-rules.
- Tools Ask-AI: pick an admin-defined agent, optional Tavily context, OpenRouter key + Tezcoin cost in Appearance → تزکوین.
- Admin: AI agents CPT with Markdown skill upload, project status metabox, Tezcoin accounting, GA / Clarity IDs.
- Logged-in comments auto-approved; image alt filled from title when empty.
- Header credits/profile now open `/account/`.

## 1.7.0 — 2026-08-20 — Header, Tezcoin, footer, blog


### Changes

- Mobile header: hamburger on the right, centered logo, search / Tezcoin / profile icons on the left.
- Search icon opens an overlay with popular queries (not a dead link).
- Header Tezcoin chip + tooltip (1000 coins after signup and completed profile).
- WordPress admin: Appearance → تزکوین for coin price, packs, Zarinpal / AqayePardakht, Enamad.
- Footer trust seals (اینماد / ساماندهی / SSL), services column, legal links.
- University coworker strip on home and service pages.
- Chaty FAB visible on mobile, items open toward the left, staggered animation.
- Blog: category chips, «ادامه مطلب» buttons, social share with Tezcoin reward.
- Comments require login; approved comments award Tezcoin.
- Classic-editor leftover shortcodes are stripped on the front (interactive tool shortcodes kept).
- Bottom nav remains the customizable `bottom` menu location.

### Testing

- Hard-refresh home: header credits, search overlay, university strip, footer seals.
- Mobile 390px: logo centered, hamburger right, icons left, FAB above bottom nav.
- Appearance → تزکوین saves; profile fields grant 1000 coins once complete.

## 1.6.6 — 2026-08-20 — Shortcode + meta provenance

### Changes

- The shortcode-to-builder candidate loop now skips pages with `_teznevise_builder_provenance = manual` unless the Setup force-replace checkbox is used, even when builder JSON is empty `[]`.
- Administrator writes of `_teznevise_*` page fields (metabox save or REST meta) stamp `manual` provenance **only when a stored value actually changes**, so empty-builder automatic migration cannot overwrite those edits. Unchanged REST payloads that resend existing meta do not take ownership.
- REST `auth_callback` for page `_teznevise_*` meta is scoped to `edit_page` on the target page.

### Testing

- Manual provenance + `[]` builder + leftover shortcode content + no extracted entry → non-forced `teznevise_migration_run` skips.
- Same page with force-replace → parsed sections written.
- Changing a `_teznevise_*` metabox or REST field stamps `manual`; subsequent auto-run leaves the page alone.
- Unchanged REST/metabox saves (same stored values) do not stamp.

## 1.6.5 — 2026-08-20 — Empty-builder ownership

### Changes

- Automatic extracted migration no longer treats empty builder JSON (`[]` / missing) as permission to replace administrator-owned `_teznevise_*` fields or a non-default page template.
- Manual provenance is checked before the empty-builder path, so a page saved in the builder with an empty payload is skipped unless the Setup force-replace checkbox is used.
- Empty, non-manual builder JSON can still be filled. Existing custom fields and templates stay in place; default-seed and unedited v1.1 parser output remain replaceable.

### Testing

- Ownership matrix: manual + empty builder, empty builder + custom fields, default-seed, unedited extracted hash, force-replace.

## 1.6.4 — 2026-08-19 — Shortcode pages → custom fields

### Changes

- Extracted every published **page** from `docs/sep_posts.csv` and the matching WPCode snippets into `inc/extracted-page-fields.json` (builder sections + `_teznevise_*` meta). **Posts are not touched.**
- Original slugs, titles, parents, and `post_content` stay as they are (`/thesis/`, `/proposal/`, `/about-us/`, `/contact-us/`, calculator permalinks, nested `thesis/phd` vs `proposal/phd`, …).
- No invented copy: homepage WPCode placeholders are skipped; that page reuses the site’s own redesign defaults. Empty redesign seed pages (`about`, `service-thesis`, …) stay on `builder-defaults.json`.
- Migrator v1.2 writes extracted fields onto existing pages (Appearance → Teznevise Setup). Interactive calculators/forms still render from leftover shortcodes.
- Automatic migration is fill/provenance-only: administrator-edited builder JSON, `_teznevise_*` meta, and custom templates are never replaced unless an explicit force-replace checkbox is used. Default-seed and unedited v1.1 parser output remain replaceable.
- Mixed pages such as `/join-us/` keep `[tz_careers_terms]` and `[tz_join_form]` next to builder sections. Privacy/cookie pages keep the legal body in `post_content` (builder only stores hero/CTA).
- Extracted writes respect the migration batch limit/cursor and only mark v1.2 complete after a full unlimited pass.
- Templates prefer builder custom fields when present so hub shortcodes are not printed twice.

### Testing

- `python3 scripts/extract-shortcode-pages.py --check`
- PHP lint on changed theme files.

## 1.6.3 — 2026-08-19 — HTML→WordPress design parity


### Changes

- Restored `layout-refinements.css` (and other stylesheets) that had been truncated at 32KB mid-rule after the HTML migration, bringing back hero orbit, SEO, header utility, and component styles.
- Healed mid-token line-breaks in `redesign.css`, `motion.css`, `ui-round2.css`, `site-polish.css`, and `service-simulation.css` that dropped declarations in the browser.
- Added `wp-compat.css` plus `header-form.css` (the live enqueue was 404) so Gutenberg/global-styles, WP `.sub-menu` dropdowns, RTL menus, media overflow, and leftover shortcodes no longer break the HTML design.
- Restored WPCode calculator, hub, downloads, careers-terms, service-CTA, and price-calculator shortcodes inside the theme so leftover `[tz_*]` tags render the original UI instead of raw brackets.
- Curly/smart quotes around Gravity Forms attributes (`[gravityform id=”2″]`) are normalized before `do_shortcode`, and a catch-all still replaces any remaining `tz_*` / `teznevise_*` / `gravityform` tags.
- Default page template no longer prints the generic «صفحه» eyebrow; shortcode-only pages skip the empty chrome and show the designed landing or calculator.
- Contact and inquiry templates regain native forms, FAQ accordion, and messenger actions. `/contact-us/` maps onto the contact template.
- Homepage SEO disclosure panel restored; blog archive gains a sidebar; theme.json content width matches the 1180px HTML container.
- Block library / global-styles are dequeued on the frontend so classic theme CSS is the source of truth.

### Testing

- `node --check` on `assets/js/redesign.js`.
- Extractor `scripts/extract-wpcode.py` regenerates `inc/legacy-wpcode.php` from the WPCode export.
- Visual QA against teznevise.ir after the 5-minute theme sync.
