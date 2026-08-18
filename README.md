# Teznevise WordPress Theme

**Version:** 1.6.0

**Author:** MAZ//ID (Maziyar) · **Brand:** [MΛZ / maziyarid/M-Z](https://github.com/maziyarid/M-Z)

RTL-first WordPress theme for [teznevise.ir](https://teznevise.ir/) — thesis, proposal, research, and statistical consulting.

## Production source and deployment

- Production branch: `main`.
- Production theme path: `/home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise`.
- Deployment authority: VPS cron → cPanel VersionControl/update for `main` → cPanel VersionControlDeployment/create → `.cpanel.yml`.
- GitHub Actions deployment remains disabled intentionally. Do not re-enable it.
- `.cpanel.yml` excludes `.git` from the deployed theme directory.
- Never commit credentials, API tokens, SSH keys, passwords, or private uploads.

## Installation / setup

1. Install WordPress with PHP 8.0+ and WordPress 6.4+.
2. Install and activate Teznevise.
3. Run **Appearance → راه‌اندازی تزنویسه** to seed the recommended pages/templates.
4. Run the asset promotion step on a deployment where `teznevise_work/assets/` is retained; verify the required files exist under root `assets/` before removing reference-runtime fallback.
5. Configure homepage/NAP values under the Customizer.
6. Assign **Primary**, **Mobile**, **Bottom**, and **Footer** menus.
7. Set the WordPress Settings → Reading front page and posts page.
8. Set Settings → Permalinks to the intended production structure and flush permalinks once after page seeding.

## Static → WordPress mapping

See [`docs/PAGE-MAP.md`](docs/PAGE-MAP.md) for the complete mapping and audit matrix.

The canonical static reference remains `teznevise_work/`. It is not a production page source. Legacy `.html` links in post content are rewritten by `teznevise_rewrite_static_links()`.

## Blog

The blog uses native WordPress posts for title, content, excerpt, featured image, categories, tags, author, publication/modification dates, status, revisions, and comments.

Optional presentation fields are available in **Teznevise Blog Settings** on posts:

- Kicker / eyebrow
- Subtitle / standfirst
- Reading-time override
- Featured label
- Author label override
- TOC enable/disable
- Related-post heading

The theme generates H2/H3 TOC anchors from the prepared rendered content. Existing heading IDs are normalized through one deterministic uniqueness allocator, collisions are suffixed, and Persian headings are supported. Reading time counts Unicode whitespace-separated tokens rather than ASCII-only `str_word_count()`.

The homepage does not emit fabricated/demo blog cards when the site has no posts; it shows a controlled empty state instead.

## Flexible page builder

Pages and posts expose the meta box **صفحه‌ساز تزنویسه — بخش‌های سفارشی**, which composes
unlimited repeatable sections (hero, software catalog, challenges, service cards, feature
list, process steps, CTA band) with add / duplicate / reorder / remove controls, Font
Awesome or media-library icons, and per-section column and background options.

See [`docs/PAGE-BUILDER.md`](docs/PAGE-BUILDER.md).

## Navigation and accessibility

- The header search control uses `get_search_link()` and therefore remains functional without theme-specific JavaScript.
- Mobile navigation exposes `aria-expanded` and `aria-controls`, uses dialog semantics, restores focus to the opener, closes on Escape, and traps Tab focus while open.
- The mobile table of contents uses a native `<details>` disclosure while desktop keeps the sticky TOC presentation.
- Skip-link target and shared `#main-content` landmark are owned by `header.php`/`footer.php`; content templates do not create nested main landmarks.

## SEO ownership

WordPress core owns title tags, canonical handling, robots infrastructure, and the XML sitemap. The theme adds contextual descriptions, Open Graph/Twitter metadata, robots controls for search/404, WebSite/Article/BreadcrumbList JSON-LD, and the robots.txt sitemap declaration only when major SEO plugins are not active.

If Yoast SEO, Rank Math, AIOSEO, or SEOPress is active, the theme suppresses its fallback metadata/schema to avoid duplicate ownership. Structured data is generated only from visible page/post data.

## Assets

Production assets belong under `assets/`. The resolver currently checks root production assets first and `teznevise_work/assets/` second as a migration fallback. See [`docs/ASSETS.md`](docs/ASSETS.md).

The release check intentionally fails when a static dependency is still only under `teznevise_work/`; the reference fallback must not be treated as proof of asset promotion. Root production assets must be populated and network-verified before release approval.

## Testing

Run repository checks from the theme root:

```bash
bash scripts/release-check.sh
```

The script checks PHP syntax, JavaScript syntax where Node is available, placeholder corruption markers, forbidden deployment credential patterns, required templates, required production assets, theme version, and cPanel deployment configuration.

For a full WordPress runtime check, use the VPS commands in `docs/RELEASE_CHECKLIST.md` and test the deployed theme rather than relying on static linting alone.

## VPS deployment verification

On the server, verify the deployed revision and theme path:

```bash
cd /home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise
git rev-parse HEAD
git status --short
grep -n "TEZNEVISE_VERSION" functions.php
! grep -R "^PLACEHOLDER[0-9]*$" -n --include='*.php' --include='*.css' --include='*.js' .
```

The deployed SHA must match the intended `main` release SHA. Review cPanel VersionControl and VersionControlDeployment logs when the SHA does not advance.

The production hostname must return rendered WordPress HTML, not a placeholder or repository error.

## Rollback

1. Identify the last known-good `main` commit SHA.
2. In the repository, revert or create a corrective commit rather than rewriting history.
3. Allow the VPS cron to pull the corrected `main` revision through cPanel VersionControl.
4. Verify `.cpanel.yml` deployment completion.
5. Confirm the deployed theme SHA and live response.
6. Preserve the failing SHA and cPanel logs for diagnosis.

Do not manually replace production files with untracked copies unless the deployment system itself is unavailable and the emergency change is separately documented.

## Release gate

Production release is blocked until all current **Blocker=Yes** items in [`docs/REQUIREMENTS.md`](docs/REQUIREMENTS.md) are PASS with evidence. A repository-only result cannot substitute for live runtime evidence.

## Documentation

- [`docs/PAGE-MAP.md`](docs/PAGE-MAP.md) — static-to-WordPress mapping and parity matrix.
- [`docs/ASSETS.md`](docs/ASSETS.md) — asset ownership and promotion policy.
- [`docs/EDITABLE-BACKENDS.md`](docs/EDITABLE-BACKENDS.md) — editable content model.
- [`docs/RELEASE_CHECKLIST.md`](docs/RELEASE_CHECKLIST.md) — release, VPS, and browser verification.
- [`docs/REQUIREMENTS.md`](docs/REQUIREMENTS.md) — authoritative blocker register.
- [`CHANGELOG.md`](CHANGELOG.md) — release history and deployment notes.

## License

GPL-2.0-or-later · © Maziyar (MAZ//ID)

— M•Z
