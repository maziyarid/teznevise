# Teznevise 1.6.0 — Release Checklist

## Gate semantics

Production approval is granted only when every current **Blocker=Yes** requirement in `docs/REQUIREMENTS.md` is **PASS** with recorded evidence.

Repository checks cannot substitute for deployed WordPress and live-browser evidence.

## Repository gate

- [ ] Confirm release branch contains only intended theme changes.
- [ ] Confirm `main` remains the production branch.
- [ ] Confirm `teznevise_work/` is reference material, not a page source.
- [ ] Confirm no secrets, private uploads, backups, or credentials are included.
- [ ] Confirm `functions.php` version is 1.6.0.
- [ ] Run `bash scripts/release-check.sh`.
- [ ] Run `php -l` over every PHP file when PHP is available.
- [ ] Run `node --check` over every JavaScript file when Node is available.
- [ ] Inspect all changed PHP for escaping, nonce, capability, autosave, revision, and direct-access guards.
- [ ] Confirm all included PHP files exist.
- [ ] Confirm no `PLACEHOLDER*` corruption markers remain.
- [ ] Confirm no GitHub Actions deployment has been enabled.

## Static parity gate

For every row in `docs/PAGE-MAP.md`:

- [ ] Compare original DOM hierarchy against WordPress template.
- [ ] Verify sections, classes, navigation, CTAs, forms, footer, and empty states.
- [ ] Verify all assets resolve from production paths.
- [ ] Verify desktop and mobile layouts at 375, 768, 1024, and 1440 CSS widths.
- [ ] Verify RTL and Persian typography.
- [ ] Verify heading hierarchy and landmarks.
- [ ] Verify internal links use WordPress URLs rather than hard-coded `.html` paths.

## Blog gate

- [ ] `/blog/` archive renders posts and pagination.
- [ ] Category archive renders title, description, cards, and pagination.
- [ ] Tag archive renders title, description, cards, and pagination.
- [ ] Search renders results and empty state.
- [ ] Single post renders native title/content/excerpt/featured image/category/tag/author/date fields.
- [ ] Kicker, subtitle, featured label, author override, reading-time override, TOC toggle, and related-heading fields save correctly.
- [ ] Autosave and revisions do not overwrite presentation fields incorrectly.
- [ ] Reading time counts Persian and mixed Unicode content.
- [ ] H2/H3 IDs are unique, stable, and present in the rendered headings.
- [ ] Every TOC link targets an existing heading ID.
- [ ] Duplicate explicit IDs are suffixed deterministically.
- [ ] TOC disappears when disabled or when no H2/H3 exists.
- [ ] Images, captions, lists, blockquotes, tables, and code blocks retain safe rendering.
- [ ] Related posts query is skipped when there are no categories.
- [ ] Previous/next navigation works at both ends of the post sequence.
- [ ] Comments render and submit through native WordPress behavior.
- [ ] Mobile TOC/navigation is keyboard accessible.

## SEO gate

- [ ] `title-tag` output is present and unique.
- [ ] Meta description is contextual and not duplicated.
- [ ] Canonical output is self-referencing where applicable.
- [ ] Search and 404 are `noindex,follow`.
- [ ] WordPress XML sitemap remains available.
- [ ] `robots.txt` declares the sitemap.
- [ ] Open Graph metadata is complete when theme fallback is active.
- [ ] Twitter/X metadata is complete when theme fallback is active.
- [ ] WebSite schema validates.
- [ ] Article schema validates on posts and uses visible title/author/dates/image.
- [ ] BreadcrumbList describes the visible page hierarchy.
- [ ] No duplicate theme SEO output appears when Yoast, Rank Math, AIOSEO, or SEOPress is active.
- [ ] No fabricated business facts are emitted.

## Accessibility gate

- [ ] One logical H1 per page where appropriate.
- [ ] Skip link targets `#main-content`.
- [ ] Main landmark exists.
- [ ] Navigation has an accessible label.
- [ ] Mobile menu button exposes `aria-expanded` and `aria-controls`.
- [ ] Search trigger and search form are keyboard accessible.
- [ ] Focus-visible states are visible.
- [ ] Form controls have labels.
- [ ] Images have meaningful or intentionally empty alt text.
- [ ] No clickable non-interactive elements require pointer-only interaction.
- [ ] RTL and language attributes are correct.
- [ ] Reduced-motion behavior is preserved.

## Deployment gate

- [ ] Confirm the intended commit is on `main` after explicit approval.
- [ ] VPS cron is active.
- [ ] cPanel VersionControl/update pulls `main`.
- [ ] cPanel VersionControlDeployment/create succeeds.
- [ ] `.cpanel.yml` deploys to `/home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise`.
- [ ] `.git` is excluded from the deployed theme directory.
- [ ] Deployed theme SHA matches the intended `main` SHA.
- [ ] `functions.php` on the server contains the release version.
- [ ] No `PLACEHOLDER*` corruption remains on the server.

## Live-site gate

- [ ] `https://teznevise.ir/` returns rendered WordPress HTML.
- [ ] `/blog/` works.
- [ ] One real post works.
- [ ] Category and tag archives work.
- [ ] Search works.
- [ ] `/404-test-path/` returns the WordPress 404 template and `noindex`.
- [ ] `/robots.txt` is valid and declares the sitemap.
- [ ] `/wp-sitemap.xml` is reachable.
- [ ] Page source contains exactly one intended description and no duplicate theme/plugin metadata.
- [ ] JSON-LD validates.
- [ ] No missing CSS/JS/image requests.
- [ ] No theme JavaScript console errors.
- [ ] Desktop, tablet, and mobile screenshots show parity with `teznevise_work/`.
- [ ] Logo dimensions match the original design.
- [ ] Desktop and mobile menus work.
- [ ] Contact/inquiry forms work with synthetic data.

## Current release-cycle evidence

- Live root checked 2026-08-19: full WordPress theme HTML (TZ-403 PASS).
- Release tooling updated to 1.6.0.
- Remaining blockers are primarily browser/VPS evidence items.

## Rollback

Use the previous known-good `main` commit, create a corrective commit, and allow the VPS cron/cPanel deployment chain to redeploy it. Preserve deployment logs and the failed SHA.
