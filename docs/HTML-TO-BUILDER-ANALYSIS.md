# HTML structure analysis (Phase 2.1)

Source: `teznevise_work/*.html` (16 files). Builder types: hero, software_catalog,
challenges, service_cards, feature_list, process_steps, cta_band.

| File | HTML `<section>`s | Builder mapping | Left in template / content |
|------|-------------------|-----------------|----------------------------|
| `index.html` | 7 | service_cards, feature_list, process_steps, cta_band | Customizer hero visual + recent posts query |
| `about.html` | 6 | hero, feature_list ×2, process_steps, cta_band | Optional `the_content()` |
| `contact.html` | 5 | hero, feature_list (FAQ), cta_band | NAP from Customizer + form in content |
| `inquiry.html` | 1 (form-heavy) | hero, process_steps, cta_band | Gravity/HTML form in content |
| `downloads.html` | 2 | hero, software_catalog, cta_band | — |
| `privacy.html` | 3 | hero, cta_band | Policy headings in `post_content` |
| `service-*.html` (4) | 6 each | hero, challenges, catalog/cards, process_steps, cta_band | Shared `page-service.php` |
| `team.html` | 4 | hero, feature_list (stats), service_cards, cta_band | — |
| `tools.html` | 3 | hero, software_catalog, cta_band | — |
| `tool-descriptive-statistics.html` | 6 | hero, process_steps, feature_list, catalog, cta_band | Calculator in `post_content` |
| `post-sample.html` | 3 | cta_band | Native article, TOC, related posts |
| `blog.html` | 2 | — excluded | `home.php` loop |
| `404.html` | 1 | — excluded | coded `404.php` |

Run `node scripts/html-to-builder.mjs` to reprint live section counts.

Inquiry and the four service files share existing templates on purpose. Creating
`page-inquiry.php` / `page-service-thesis.php` would duplicate markup the builder
is meant to replace.
