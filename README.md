# Teznevise

A custom WordPress theme for the Teznevise website.

## Version

Current development version: **1.3.0**

## Blog conversion

The blog archive, taxonomy archives, and single-post page are powered by native WordPress queries and template hierarchy. Posts use native title, content, excerpt, featured image, categories, tags, author, date, comments, and status fields.

The post editor also includes the **Teznevise Blog Settings** panel for optional presentation controls:

- Kicker / eyebrow.
- Subtitle / standfirst.
- Reading time with automatic word-count fallback.
- Featured label.
- Author label override.
- Table-of-contents visibility.
- Related-post heading.

All metadata is nonce-protected, capability-checked, autosave-safe, revision-safe, and sanitized. Empty optional fields fall back to native WordPress values or theme defaults.

## Changelog

### 1.3.0 — 2026-08-17

- Completed the native WordPress blog archive and taxonomy archive templates.
- Completed the single-post presentation template.
- Added native post-editor fields for blog presentation controls.
- Added automatic reading-time fallback from post word count.
- Added generated table of contents from H2/H3 post headings.
- Added related posts by shared category.
- Added responsive blog layout and post-card styles.
- Added pagination and taxonomy descriptions.

### 1.2.0 — 2026-08-17

- Added the automatic cPanel deployment workflow.
- Added the footer sitemap link and updated the theme version.
