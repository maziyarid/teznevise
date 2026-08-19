# Changelog

## 1.6.6 — 2026-08-19 — Designed HTML chrome (header / footer / mobile)

### Changes

- Desktop pill now uses the five short labels from `teznevise_work/index.html` (خانه، خدمات، ابزارها، بلاگ، درباره ما) with designed dropdowns instead of `wp_nav_menu` dumping the full site tree.
- Search is an overlay (`data-search-open`) rather than a link to `/search/`.
- Footer class is `.footer-new` with the four designed columns (brand / خدمات / دسترسی سریع / ارتباط).
- Mobile drawer is the nine-link HTML list; bottom bar is the 4-up خانه / ابزارها / بلاگ / تماس.
- `html-parity.css` loads last: nowrap labels, 1050px hamburger, 44px taps, search overlay, orbit tags that keep their layout `transform` while floating.
- `teznevise_logo_url()` and `teznevise_is_current()` live in `inc/helpers.php`.
- Appearance → Menus shows an admin notice; locations stay registered but no longer render header/footer/bottom chrome.

### Testing

- Static prototype served for visual QA (desktop 1280 and mobile 390).
- Guard: every `teznevise_*()` called in `header.php` is defined.

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

