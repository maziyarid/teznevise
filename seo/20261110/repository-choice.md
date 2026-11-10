# Repository and change scope

Use `maziyarid/teznevise`, base `main` at `8b432f7`.

Read-only checks on 2026-11-10 found the live home page loading `/wp-content/themes/teznevise-2-preview/` fonts, CSS and JavaScript. Its public `style.css` identifies **Teznevise**, the `teznevise` text domain, MAZ//ID attribution and the same description/header as this repository. The live page also uses this repository's `teznevise-critical`, `hero-new`, `tz-home-hero` and builder architecture. The repository's `.cpanel.yml` names the teznevise.ir theme deployment path. This is the code source; production post content lives in the WordPress database and is staged as versioned HTML plus a WP-CLI migration in this PR.

The live public header reports version `1.9.35`; the repository reports `1.9.31`. This is observed production drift, not evidence that the unrelated Teznevisan theme is the right source. The owner must reconcile the active preview theme with the reviewed module before installation. No existing production theme files are overwritten by the suggested minimal module installation.

`maziyarid/TeznevisanTheme` was inspected for repository structure and identity. Its different theme structure is unnecessary for this site's Teznevise source. No branch or PR was created there.

Branch: `seo/teznevise-onpage-20261110`. Exactly one PR is intended. No merge, release, server login, WP-CLI execution against production, external-site edits or deployment was performed.

## Search Console input supplied by the owner

2026-08-09 through 2026-11-08: 549 clicks, 16,661 impressions; traffic concentrates on calculator and download URLs. Money-page positions: `/thesis/` 21 with zero clicks, `/thesis/phd/` 77, `/proposal/` 7.7 on 20 impressions, `/proposal/phd/` 25, `/service-project/` 39.7. Plain `انجام پایان نامه` did not appear. Coverage totals supplied: 617 indexed, 182 not found, 47 robots blocked, 71 redirects, 22 noindex.

These are owner-supplied facts, not independently downloaded Search Console exports. The public crawl is a current snapshot and cannot enumerate historical, orphaned or private Search Console URLs. Its findings and limits are reported separately in `audit/`.

The existing GitHub/Vercel integration attempted an automatic preview for initial commit `88404af`; its check reported failure and no preview was deployed. `vercel.json` disables automatic deployment for this SEO branch only. No production deployment or WordPress mutation was run.

Authenticated read-only WPVibe site metadata on 2026-11-10 independently confirms active theme `Teznevise`, stylesheet/template `teznevise-2-preview`, version `1.9.35`, with Rank Math active. Only metadata and SELECT/GET source checks were performed; no remote write or apply was executed.
