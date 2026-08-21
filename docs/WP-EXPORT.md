# Live WordPress content snapshot

There was no WXR (`wordpress-export.xml`) in the repository. Content was pulled
from the public REST API on 2026-08-21:

- [`wp-rest-export.json`](wp-rest-export.json) — 105 pages, 11 posts (id, slug, title, link, template)

The live theme already renders this catalog through the page builder + templates
(`page-service.php`, `page-tool.php`, `page-tools.php`, `front-page.php`, …).
Do not re-import as a WXR unless you are seeding a blank site.

Former static HTML in `teznevise_work/*.html` is the visual reference; production
copy lives in WordPress.
