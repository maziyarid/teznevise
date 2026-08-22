# Teznevise 1.9.6

**Date:** 2026-08-22  
**Scope:** live WordPress theme; the React application is intentionally unchanged.

## Classic Editor contract

Every published WordPress page now ends with exactly one `.tz-classic-disclosure` immediately before `footer.php`. The source order is:

1. administrator-authored Classic Editor prose in `post_content`;
2. recovered WXR prose matched by full page path;
3. semantic prose generated from the exported builder sections;
4. ordinary excerpt/subtitle/features;
5. an explicit empty-state message when no editorial source exists.

The homepage hard-coded guide was removed. Page templates render only functional calculators/forms in the normal body position. Editorial content is rendered only by the footer disclosure. Rendered editor content is passed through WordPress content filters, sanitized with `wp_kses_post`, has embedded H1 elements demoted to H2, and has DOM IDs namespaced per page.

### Import

Authorized wp-admin requests run a non-destructive versioned import in batches of ten pages, avoiding a long blocking admin request. It only writes pages that are empty or shortcode-only; administrator prose is never overwritten. Functional shortcodes are retained in `_teznevise_functional_shortcodes` and therefore do not remain in Classic Editor content.

The equivalent deterministic CLI command is:

```bash
wp teznevise classic-content import
```

The last automatic result is stored in `teznevise_classic_import_report`. Review `scanned`, `updated`, `skipped`, and `errors` before clearing caches.

## Mobile contract

- At 768px and below, repeated marketing/service/process/testimonial/stat cards centre their icon, title, and short copy.
- Long-form content, forms, tables, and FAQ answers retain readable RTL start alignment.
- The drawer uses `100dvh`, contained scrolling, a stable gutter, and bottom safe-area clearance.
- Bottom navigation reserves document space; the contact FAB is smaller and sits above it.
- Mobile footer columns, social links, certificates, legal links, and brand copy share a centre axis.

## Backend hardening

- `/statistics/` now 301-redirects to `/service-statistics/`; theme defaults and footer use the published URL.
- `/wp-json/wp/v2/users*` is unavailable unless the requester can `list_users`.
- XML-RPC is disabled at application scope; login errors are generic.
- Lead POSTs have a honeypot, same-origin return target, three-per-minute rate limit, 90-day retention, and no stored raw IP.
- AI chat validates inputs, isolates guest quotas using a daily HMAC subject, rate-limits bursts, prevents anonymous multi-agent amplification, allow-lists provider hosts/models, stops raw IP storage, and never returns or persists private chain-of-thought.

## Deployment checks

1. Back up the database and active theme.
2. Deploy the complete 1.9.6 theme patch.
3. Prefer the CLI import. Otherwise browse wp-admin until `teznevise_classic_import_version` becomes `1.9.6` (up to twelve requests for the current 105-page catalogue).
4. Purge page/object/CDN caches.
5. Verify homepage, a classic page, a builder service, a calculator, contact form, drawer, footer, and `/statistics/` redirect.
6. Configure HSTS and a tested CSP at nginx/CDN level; the theme intentionally does not guess either policy.
