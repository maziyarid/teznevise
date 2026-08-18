# Editable content backends

## Homepage (Customizer)

**Appearance → Customize → تزنویسه — محتوای سایت**

| Section | What you edit |
|---------|----------------|
| اطلاعات تماس | Phone, WhatsApp, Telegram, Bale, email, address, hours |
| صفحه اصلی — هیرو | Eyebrow, title parts, text, buttons, trust points |
| صفحه اصلی — خدمات | Section head + 6 service cards (title, text, URL, icon, color) |
| صفحه اصلی — درباره و مزایا | About panel + 4 reason items |
| صفحه اصلی — چهار قدم | Steps intro + 4 steps |
| صفحه اصلی — مقالات | Section head (posts still come from Blog) |
| صفحه اصلی — نوار CTA | Final CTA band |

Defaults match the original static HTML. Empty theme_mod falls back via `teznevise_mod()`.

## Pages (registered custom fields)

On any **Page** edit screen, meta box **فیلدهای تزنویسه**:

| Field key | Meta key | Purpose |
|-----------|----------|---------|
| eyebrow | `_teznevise_eyebrow` | Small label above title |
| subtitle | `_teznevise_subtitle` | Subtitle under H1 |
| cta_text / cta_url | `_teznevise_cta_text` / `_teznevise_cta_url` | Primary CTA |
| secondary_cta_* | `_teznevise_secondary_cta_*` | Secondary CTA |
| hero_note | `_teznevise_hero_note` | Helper note under title |
| service_icon | `_teznevise_service_icon` | Font Awesome class |
| service_color | `_teznevise_service_color` | `icon-*` class |
| features | `_teznevise_features` | One bullet per line |
| price_note | `_teznevise_price_note` | Price / timeline note |
| hide_title | `_teznevise_hide_title` | Hide default H1 |

Fields are registered with `register_post_meta()` (`show_in_rest` => true).

## Pages and posts (flexible page builder)

Meta box **صفحه‌ساز تزنویسه — بخش‌های سفارشی** adds, duplicates, reorders and removes
unlimited sections and items (`_teznevise_builder_sections`, JSON). See
[`docs/PAGE-BUILDER.md`](PAGE-BUILDER.md).

## Template helpers

```php
teznevise_mod( 'hero_title_1' );           // Customizer
teznevise_page_field( 'eyebrow' );         // Page meta
teznevise_url( '/inquiry/' );              // Relative → absolute
teznevise_get_contact( 'whatsapp' );       // Contact (Customizer)
teznevise_builder_render_sections();       // Flexible page builder sections
```
