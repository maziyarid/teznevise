# Templates & icon backends

## Page templates (assign in Page editor → Template)

| File | Name in admin | Suggested slug |
|------|---------------|----------------|
| `page-service.php` | صفحه خدمت (Service) | service-thesis, service-proposal, … |
| `page-contact.php` | تماس / درخواست | contact, inquiry |
| `page-sitemap.php` | نقشه سایت بصری | sitemap |
| `page-privacy.php` | حریم خصوصی | privacy |
| `page.php` | Default | about, tools, generic |

## Automatic templates

| File | When |
|------|------|
| `404.php` | Not found — search + quick links + service cards |
| `category.php` | Category hub — chips, count, posts |
| `tag.php` | Tag hub |
| `archive.php` | Other archives |

## Icons on landing pages (manual)

**Pages → edit page → فیلدهای تزنویسه → آیکون**

1. Select a preset from the Font Awesome dropdown, **or**
2. Type a custom class in **کلاس آیکون سفارشی** (overrides preset)
3. Choose icon color (`icon-teal`, `icon-indigo`, …)

Registered keys:

- `_teznevise_service_icon`
- `_teznevise_service_icon_custom`
- `_teznevise_service_color`

Helper: `teznevise_get_page_icon( $post_id )`

## Category / tag icons

**Posts → Categories/Tags → edit** → field **آیکون (Font Awesome)**

Registered: `_teznevise_term_icon` via `register_term_meta` (REST-ready).

## Create sitemap & privacy pages

1. Pages → Add New → title «نقشه سایت» → slug `sitemap` → Template: نقشه سایت بصری
2. Pages → Add New → title «حریم خصوصی» → slug `privacy` → Template: حریم خصوصی → paste policy body in editor
