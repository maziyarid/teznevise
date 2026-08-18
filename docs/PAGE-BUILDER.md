# Flexible page builder

The page builder lets editors compose repeatable sections on any page (and post) from the
editor screen, without template changes and without a fixed item count.

## Where

Post editor → meta box **صفحه‌ساز تزنویسه — بخش‌های سفارشی** (pages and posts).

Choose a section type, press **افزودن بخش**, then fill the section fields and add as
many items as needed. Each section and each item can be moved (drag handle or the
up/down buttons), duplicated, or removed. Sections have a **نمایش این بخش** checkbox so a
section can be parked without deleting it.

## Storage

| Meta key | Type | Content |
|----------|------|---------|
| `_teznevise_builder_sections` | string | JSON array of sections |

Each section is `{ "type": …, "enabled": true, <section fields>, "items": [ … ] }`.
The meta is registered with `show_in_rest => true`; both the REST sanitizer and the
meta-box save path run `teznevise_builder_sanitize_sections()`, which drops unknown
section types and unknown fields and coerces every value by field type.

## Section types

| Type | Section fields | Item fields |
|------|----------------|-------------|
| `hero` | eyebrow, title, text, cta_text, cta_url, background | title, icon |
| `software_catalog` | eyebrow, title, text, columns, background | title, text, icon, icon_svg, color, url, badge |
| `challenges` | same as above | same as above |
| `service_cards` | same as above | same as above |
| `feature_list` | eyebrow, title, text, columns, background | title, text, icon, color |
| `process_steps` | eyebrow, title, text, background | title, text, icon, color, image |
| `cta_band` | title, text, cta_text, cta_url, background | — |

Register additional types with the `teznevise_builder_section_types` filter; supply
`label`, `supports`, `item_fields` and a `render` callback. Available field types are
`text`, `textarea`, `url`, `select`, `icon`, `color` and `image`.

## Icons and images

- `icon` accepts a curated Font Awesome class from the dropdown or a hand-written class;
  values are reduced to `[a-z0-9- ]` and capped at 100 characters.
- `icon_svg` and `image` use the WordPress media library and are rendered as `<img>`
  (never inlined), so an uploaded SVG cannot execute in the page context.
- SVG uploads still require the site to allow the `image/svg+xml` MIME type; WordPress
  blocks it by default and the theme does not change that.
- `color` must be one of `teznevise_icon_color_choices()`; anything else falls back to
  the first choice.

## Templates

Templates render all enabled sections with a single call:

```php
teznevise_builder_render_sections();
```

It is already wired into `page.php`, `page-service.php`, `page-about.php`,
`page-contact.php`, `page-downloads.php`, `page-privacy.php`, `page-team.php`,
`page-tools.php` and `single.php`, after the template's built-in content.

`assets/css/builder-frontend.css` is enqueued only on singular views that actually have
enabled sections.

## Not implemented yet

Frontend click-to-edit (Phase 4 of `docs/PR-FLEXIBLE-PAGE-BUILDER.md`) is not part of
this module; all editing happens in the post editor.
