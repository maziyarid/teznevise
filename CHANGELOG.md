# Changelog

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

