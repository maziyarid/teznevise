# Teznevise Premium Theme v2 roadmap

## Architecture decision

Teznevise v2 remains a progressively enhanced WordPress theme while the existing TanStack/Vite application remains the only modern frontend. Tailwind v4 stays inside `react-app/`; React and TypeScript are used selectively for complex interfaces. Alpine, GSAP, Gutenberg React, WPGraphQL, and Next.js are not installed by default. Each may enter only when a measured product gap justifies its runtime and maintenance cost.

## Target folders

```text
design-system/              Visual language and contracts
assets/css/tokens.css       Runtime semantic tokens
assets/css/components.css   Generated shared components
assets/css/pages.css        Generated editorial/page layouts
assets/css/chrome.css       Generated navigation/search/footer
assets/js/chrome.js         Generated progressive interactions
inc/                        WordPress domain modules
react-app/                  TanStack + Vite + TypeScript + Tailwind v4
patterns/                   Future native WordPress patterns
blocks/                     Future selective blocks, only when justified
```

## Migration strategy

1. Foundation: align tokens, editor presets, README, and component contracts.
2. Consolidation: remove duplicated late-cascade rules while keeping screenshots stable.
3. Interaction: port public chrome to one TypeScript/Vite entry, then remove legacy bindings.
4. WordPress modernization: introduce native patterns and narrowly scoped blocks alongside the existing builder.
5. Content API: stabilize one REST contract; adopt WPGraphQL only if REST cannot represent nested editorial structures efficiently.
6. Headless cutover: move public routing to one SSR frontend. Next.js may replace TanStack, never run beside it.

## Performance plan

- Budget public CSS toward 80 KB compressed and one deferred public interaction bundle.
- Keep Vazirmatn local; preload only the regular above-fold face.
- Use WordPress responsive image markup, `teznevise-card` and `teznevise-hero` crops, AVIF/WebP where the server supports them, eager loading only for the actual LCP image, and lazy loading below the fold.
- Load service/tool code only on owning routes; remove duplicate menu/search listeners before adding new code.
- Measure LCP, CLS, INP, font transfer, and unused CSS in production after cache purge.

## Component-library plan

The canonical contracts are in `design-system/components/`. Classic templates use `tz-*` classes and legacy aliases during migration. React components consume the same five semantic colors and geometry through Tailwind mappings. Every component ships pointer, keyboard, focus-visible, RTL, mobile, disabled/error, and reduced-motion states as applicable.

## UI/UX plan

The signature pattern is the research margin: a wide physical-right reading column with a compact physical-left context rail. Archives use evidence-led editorial hierarchy rather than generic card walls. Forms expose help, validation, and privacy context; tools separate input, method, and result. Reading progress, bounded type controls, focus mode, and instant search progressively enhance server-rendered pages.

## SEO plan

- Keep WordPress core as canonical/title/sitemap owner.
- Preserve plugin-aware fallbacks for Open Graph, Twitter cards, Article, Organization, Person, Breadcrumb, and future FAQ schema.
- Add FAQ schema only from visible structured FAQ content; never synthesize hidden answers.
- Keep stable slugs and redirect maps through any headless migration.
- Ensure REST/headless responses expose canonical URL, title, excerpt, dates, author, terms, media, and builder structure.

## Security checklist

- Escape template output by context and sanitize all stored/editor input.
- Require capability checks and nonces for writes; use parameterized WordPress APIs.
- Public search remains read-only, bounded, debounced, abortable, and safely rendered with `textContent`.
- Do not expose private post meta through REST. Public meta needs explicit schema and sanitization.
- Keep secrets outside Git and browser bundles; no AI provider keys in theme JavaScript.
- Preserve plugin/session compatibility and test authenticated comments on staging.

## Accessibility checklist

- WCAG 2.2 AA target; visible first-Tab skip link and focus indicators.
- Semantic landmarks, heading order, labelled dialogs/forms, announced dynamic status, 44px controls, and no color-only meaning.
- Verify actual physical RTL geometry, mixed Persian/Latin text, 200% zoom, reduced motion, and keyboard focus restoration.

## Search roadmap

Phase 1 uses the WordPress search REST endpoint for debounced suggestions with full server-search fallback. Phase 2 adds grouped posts/pages/services, author/category facets, normalized Persian character handling, and typo tolerance backed by a measured index. Semantic search remains an optional future service behind the same response contract.

## Headless REST contract

Prefer WordPress REST initially:

```text
GET /wp-json/wp/v2/search?search={query}&per_page=6
GET /wp-json/wp/v2/posts/{id}?_embed=1
GET /wp-json/wp/v2/pages/{id}?_embed=1
GET /wp-json/wp/v2/{public-cpt}/{id}?_embed=1
```

A normalized frontend content model should include `id`, `type`, `slug`, `canonicalUrl`, `title`, `excerpt`, `content`, `publishedAt`, `modifiedAt`, `author`, `terms`, `featuredMedia`, `seo`, and sanitized `builderSections`. Cache public reads at the CDN, preserve preview authentication, and version custom fields before cutover.

## Release gates

- No framework without an architecture decision record.
- No new catch-all fix stylesheet.
- Generated bundles are idempotent.
- PHP syntax/coding standards, TypeScript build, JSON validation, browser interactions, RTL geometry, accessibility, and representative route screenshots pass.
- Staging verifies comments, plugin compatibility, cache-busted assets, and production-like Core Web Vitals.
