# Teznevise 1.9.5

**Date:** 2026-08-22

## Navigation

- Top-level items (`خانه`, `پایان‌نامه`, …) no longer output Font Awesome icons. Those empty squares were missing glyphs on the compact header.
- Dropdown disclosure uses a CSS triangle, not `fa-chevron-down`.
- Mega panel is `direction: rtl`, column flow RTL, icons at inline-start, labels wrap.

## Classic «مشاهده بیشتر»

- Same disclosure used on the homepage now runs on every singular page via leftover + a footer fallback (prints at most once).
- Content is classic-editor HTML after shortcodes are stripped. Raw `[tz_*]` / Gravity Forms tags never appear.
- Calculators and the native lead form still execute when a tool/contact page has no builder sections.
- 22 pages from `WordPress.2026-08-21.xml` are stored in `inc/wxr-classic-content.json`. If a live page still only contains a leftover shortcode, that HTML is shown in the box. Visiting wp-admin once writes it into the classic editor.

## PR #448

Already merged (`docs/PRODUCTION-BUILDER-ROUTE-ANALYSIS.md`). Follow-up: leftover no longer re-enters `the_content()`, which could 500 builder-backed routes.

## Gravity Forms

Not used. Old `[gravityform]` tags still map to the native first-party lead form so they cannot leak as text. The form POSTs to WordPress; name/phone/project details are not placed in a WhatsApp GET URL.

## UI/UX audit (TZ-001 … TZ-019)

| ID | Fix |
|---|---|
| TZ-001 | Unknown/curly-quoted shortcodes expand or strip; Gravity Forms → native form |
| TZ-002 | `teznevise_tel_href()` + content rewrite so `tel:` never becomes `/tel:` |
| TZ-003 | Trust seals render only with a real external verification URL |
| TZ-004 | Theme 301s aliases onto one canonical slug |
| TZ-006 | Inquiry form is the first action; contact cards sit below |
| TZ-007 | Lead intake is first-party POST with Iranian mobile validation |
| TZ-008 | Hours and phone come from one Customizer source |
| TZ-009 | Tool template prints the calculator immediately after the H1 |
| TZ-010 | Result tables get a caption and `aria-live` |
| TZ-012 | Footer column titles are not H4 |
| TZ-013 | Homepage design-note copy rewritten as customer outcomes |
| TZ-014 | Bylines never print raw WP logins |
| TZ-019 | `پرپوزال` in search suggestions is normalized to `پروپوزال` |
