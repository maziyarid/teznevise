# Teznevise

A custom WordPress theme for the Teznevise website.

## Version

Current development version: **1.3.0**

## SEO implementation

The theme includes a plugin-aware technical SEO foundation:

- Contextual meta descriptions for posts, pages, taxonomies, and the site.
- Canonical URLs for singular, blog, taxonomy, and post-type archive views.
- `noindex,follow` for search and 404 pages.
- Plugin-aware fallback behavior to avoid duplicate SEO output.
- WebSite, Article, and BreadcrumbList JSON-LD when no major SEO plugin is active.
- WordPress core sitemap compatibility and a sitemap entry in `robots.txt`.
- Escaped output and JSON-LD generated through WordPress APIs.
- Header logo constrained to a maximum 180px width and 52px height.

Use one SEO owner: either the theme fallback or an SEO plugin. Do not enable overlapping title, description, or schema modules in multiple systems.

## Changelog

### 1.3.0 — 2026-08-17

- Added plugin-aware technical SEO foundations.
- Added contextual descriptions, canonical URLs, robots directives, JSON-LD, and sitemap robots support.
- Constrained the header logo dimensions for consistent desktop and mobile rendering.

### 1.2.0 — 2026-08-17

- Added the automatic cPanel deployment workflow.
- Added the footer sitemap link and updated the theme version.
