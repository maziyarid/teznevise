# Changelog

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
