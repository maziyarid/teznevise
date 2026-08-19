# Changelog

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

