# Teznevise

A custom WordPress theme for the Teznevise website.

## Version

Current development version: **1.2.0**

## Features

- Responsive WordPress theme structure.
- Custom homepage, archive, post, page, and 404 templates.
- Native post-editor controls for blog presentation fields.
- RTL-ready layout foundation and theme.json configuration.

## Blog post fields

The **Teznevise Blog Settings** panel appears on WordPress posts and provides:

- Kicker / eyebrow.
- Subtitle / standfirst.
- Reading time.
- Featured label.
- Table-of-contents visibility.
- Related-post heading metadata for the next blog-template phase.

All fields are optional and use safe theme defaults when empty. No custom-field plugin is required.

## Changelog

### 1.2.0 — 2026-08-17

- Added the automatic cPanel deployment workflow.
- Added the footer sitemap link and updated the theme version.

### Deployment test — 2026-08-17

- Triggered a harmless README-only commit to verify GitHub-to-cPanel automatic synchronization.

### 1.1.0 — 2026-08-17

- Added native post-editor custom fields for blog presentation controls.
- Added sanitised, nonce-protected persistence for post metadata.
- Added single-post template integration for kicker, subtitle, reading time, featured label, and TOC visibility.
- Added a reusable `teznevise_post_field()` helper.

### 1.0.0 — 2026-08-17

- Added initial WordPress theme conversion structure.
- Added responsive header, footer, navigation, homepage, article cards, page, post, and 404 templates.
- Added asset fallback resolution and deployment configuration.
