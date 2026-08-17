# Teznevise → WordPress Theme Conversion Plan (v1.0.0)

**Repository:** maziyarid/teznevise  
**Source:** `teznevise_work/` static RTL HTML redesign  
**Target:** Clean WordPress theme at **repository root**  
**Primary brand token:** `#145D4A`  
**Language / Direction:** `fa_IR` / RTL  
**Date:** 2026-08-17  

---

## Definition of “Complete” (from fasdent-theme VERSION-1.5)

Taken from the authoritative gate in `VERSION-1.5/REQUIREMENTS.md` + `RELEASE_CHECKLIST.md`:

- Every requirement whose **Blocker = Yes** must be **Status = PASS** with recorded evidence.
- There is **only one approval path**. No second opinion, no “good enough”, no open redesign.
- Blocker=No items may stay PENDING/DEFERRED without blocking the package.
- Canonical source only (no historical folders mixed into the production package).
- Version recorded, assets 200, no PHP fatal, RTL correct, mobile nav works, no overflow, forms work, HTTPS-ready, console clean.
- Evidence must be reproducible (screenshots, network, syntax checks, live render).
- Post-approval changes are patches only; taste-based redesigns are out of scope.

We produce an equivalent `REQUIREMENTS.md` + `RELEASE_CHECKLIST.md` for Teznevise 1.0.0 and treat the same gate as the definition of done.

---

## 1. Project Classification & Routing (Skills Applied)

| Skill | Decision |
|-------|----------|
| **wordpress-router** + **wp-project-triage** | Currently **static HTML redesign** (not yet a WP theme/plugin). Will become a **classic PHP theme** with optional `theme.json` support (hybrid, not pure block theme). |
| **wp-block-themes** | Add lightweight `theme.json` for global styles/colors/fonts; keep templates as PHP for full control over the existing custom markup. |
| **wp-plugin-development** / **wp-abilities-*** | No companion plugin required for v1.0 unless forms need CPT/settings later. Keep forms theme-native first. |
| **wp-patterns** | Extract reusable section patterns later if needed; not blocking. |
| **wp-interactivity-api** | Optional enhancement for FAQ/SEO toggle/FAB; start with the existing vanilla JS that already works. |
| **wp-performance** | Enqueue once, version assets, prefer local fonts where possible, respect `prefers-reduced-motion`. |
| **wp-phpstan** / **wp-playground** | Syntax + static analysis + optional Playground smoke test before push. |
| **accessibility-a11y** + **seo-best-practices** + **ux-design** + **ui-ux-pro-max** + **premium-frontend-ui** + **ui-styling** | Motion must be meaningful, reduced-motion safe, contrast OK, keyboard/focus visible, semantic landmarks, proper meta, Core Web Vitals friendly. |
| **grok-build** / **project-bootstrap** | Use for structured implementation tasks once the plan is approved. |

**Theme type decision:** Classic theme (header.php / footer.php / front-page.php / page-*.php / single.php / etc.) + `theme.json` for design tokens. This preserves the exact existing HTML structure and motion system while making it WordPress-native.

---

## 2. High-Level Architecture

```
teznevise/                          ← repo root = theme root
├── style.css                       ← Theme header (Version 1.0.0)
├── functions.php
├── theme.json                      ← colors, typography, spacing tokens
├── header.php / footer.php
├── front-page.php                  ← homepage (richest motion)
├── index.php / home.php / single.php / page.php / 404.php
├── page-*.php or template-*.php    ← about, contact, inquiry, services, tools, blog, team, privacy…
├── template-parts/
│   ├── header-announcement.php
│   ├── nav-main.php
│   ├── mobile-nav.php
│   ├── fab.php
│   ├── bottom-nav.php
│   ├── hero-home.php
│   ├── services-grid.php
│   ├── cta-band.php
│   └── …
├── assets/
│   ├── css/                        ← consolidated + motion enhancements
│   ├── js/                         ← redesign.js evolved + motion enhancements
│   ├── img/ / icons/
│   └── fonts/ (local Vazirmatn preferred)
├── inc/
│   ├── enqueue.php
│   ├── setup.php
│   ├── seo.php
│   ├── customizer.php (logo, phone, WhatsApp, Telegram, hours, NAP)
│   └── helpers.php
├── docs/
│   ├── REQUIREMENTS.md
│   ├── RELEASE_CHECKLIST.md
│   ├── CANONICAL.md
│   ├── CONVERSION-PLAN-1.0.md      ← this file
│   └── evidence/
└── readme.txt / README.md
```

Static HTML in `teznevise_work/` becomes **archived reference only** (not part of the production theme package).

---

## 3. Phase-by-Phase Plan

### Phase 0 – Inventory & Freeze (Blocker)
1. Full audit of every HTML file, CSS (redesign.css, layout-refinements, motion, batch-fixes, ui-round2, service-*), JS, images, icons.
2. Map each page → WP template hierarchy.
3. Extract all hard-coded NAP, phone (`۰۹۳۰۲۸۲۲۰۹۱` / `+989302822091`), email, Telegram, WhatsApp, address, hours into Customizer / theme mods.
4. Record current motion system (data-reveal, data-reveal-stagger, hero particles/rings/blots, counters, SEO expand, FAB, mobile drawer from right, bottom-nav).
5. Create `docs/CANONICAL.md` declaring the theme root as the single source of truth.

### Phase 1 – Theme Scaffold (Blocker)
1. `style.css` with proper Theme Name, Version 1.0.0, Text Domain `teznevise`, Requires at least WP 6.4 / PHP 8.0, Tags: rtl-language-support, custom-menu, etc.
2. `functions.php` → modular includes.
3. `theme.json` with brand color `#145D4A`, secondary accents, Vazirmatn font family, spacing scale, border-radius tokens that match existing CSS.
4. Register nav menus (primary, footer, mobile, bottom).
5. Theme support: title-tag, post-thumbnails, html5, custom-logo, responsive-embeds, align-wide, etc.
6. Proper `load_theme_textdomain` for future translations (content is already Persian).

### Phase 2 – Asset Pipeline & Enqueue (Blocker)
1. Consolidate CSS into a predictable cascade:
   - `theme.css` (or keep modular files loaded in correct order).
   - Keep / enhance `motion.css` (already excellent).
2. Localize or self-host critical fonts (Vazirmatn) to avoid FOIT/FOUT and third-party dependency risk.
3. Enqueue Bootstrap RTL + Font Awesome only if still required; prefer reducing external deps where the custom CSS already covers layout.
4. Version all assets with `filemtime` or theme version.
5. Conditional loading (service pages get their extra CSS only when needed).
6. `wp_head` / `wp_footer` correctly placed; no duplicate scripts.

### Phase 3 – Template Conversion (Blocker)
Convert page-by-page while preserving **exact** class names and data attributes so existing motion/JS continues to work:

| Static | WP Template |
|--------|-------------|
| index.html | `front-page.php` |
| blog.html | `home.php` / `archive.php` |
| post-sample.html | `single.php` + template-parts |
| service-thesis / proposal / statistics / simulation | `page-service-*.php` or template hierarchy + `page.php` |
| tools.html + tool-descriptive-statistics.html | dedicated page templates |
| about / contact / inquiry / team / privacy / downloads / 404 | matching `page-*.php` or generic + ACF/Customizer later |
| Shared header / footer / mobile-nav / FAB / bottom-nav | `header.php`, `footer.php`, template-parts |

All links become `home_url()`, `get_permalink()`, `get_theme_mod()`, etc. No broken relative paths.

### Phase 4 – Motion & Professional Effects Upgrade (Core Request)
**Home page receives the richest treatment**; other pages get consistent, lighter motion.

Existing system (keep & harden):
- `data-reveal` / `data-reveal-stagger` + IntersectionObserver
- Hero entrance cascade, ink-blots, particles, network rings, orbit tags, order-button pulse
- Counter animation
- SEO disclosure expand
- Mobile drawer from right, FAB, reduced-motion media query

**Enhancements (premium-frontend-ui + ui-ux-pro-max rules):**
1. **Hero** – stronger staggered text (word/character optional), subtle parallax on visual (respect reduced-motion), improved focus ring on order button, micro-interaction on CTAs.
2. **Service cards / article cards / reason items / steps** – refined hover lift + shadow + icon scale (already present; polish timing & will-change).
3. **Section entrances** – slightly longer, more elegant easing; staggered children with better delay curve.
4. **Header** – subtle scroll-aware state (background/blur intensify on scroll) without breaking sticky behavior.
5. **Buttons & links** – consistent press/hover scale + color transitions; focus-visible rings that meet contrast.
6. **FAB & bottom-nav** – smoother open/close, better aria.
7. **Page transitions** – optional lightweight fade on internal navigation (JS, non-blocking).
8. **Service landing pages** – dedicated entrance sequences for their unique sections without copying the full homepage intensity.
9. All new motion must:
   - Use CSS custom properties already defined (`--motion-ease`, `--motion-duration`).
   - Honor `prefers-reduced-motion: reduce` (disable or simplify).
   - Avoid layout thrashing / animating width/height.
   - Stay under ~300–700 ms for most interactions.
   - Be GPU-friendly (transform + opacity).

### Phase 5 – WordPress Features & Content Model
1. Customizer sections: Brand (logo, colors), Contact (phone, WhatsApp, Telegram, email, address, hours), Social, Homepage sections (optional toggles).
2. Menus fully manageable.
3. Dynamic blog loop, pagination, single post layout matching the sample.
4. Inquiry / contact forms → theme form handlers or shortcode-ready (nonce, sanitization, honeypot). Success/error states already designed in static.
5. SEO foundation (`inc/seo.php`): title, meta description, Open Graph, basic schema (Organization + Service), canonical, no mixed content.
6. 404 page using existing design language.

### Phase 6 – Accessibility, Performance, SEO, RTL Hardening
- Semantic landmarks, heading hierarchy, alt texts, ARIA on interactive components (menu, FAB, SEO toggle, FAQ).
- Keyboard navigation + visible focus.
- Contrast audit against `#145D4A` and text colors.
- Mobile bottom-nav ≤ 4 items, 44×44 touch targets.
- No horizontal overflow at 375 / 768 / 1024 / 1440.
- Image optimization path prepared (WebP later).
- RTL verified on every component (already strong; protect it).

### Phase 7 – Documentation & Gate Artifacts
Mirror Fasdent structure:
- `docs/REQUIREMENTS.md` (Blocker=Yes rows)
- `docs/RELEASE_CHECKLIST.md`
- `docs/CANONICAL.md`
- `docs/CHANGELOG-1.0.0.md`
- Evidence folder placeholders.

### Phase 8 – Validation, Push, Handoff
1. PHP syntax check on every file.
2. JS syntax check.
3. Local / Playground smoke test (activate theme, set front page, check menus, forms, mobile, reduced-motion).
4. Push theme files to **main repository root** (clean history preference; archive static folder if desired).
5. Update root README to reflect “WordPress theme 1.0.0 – ready for production”.

---

## 4. Requirements Register (Teznevise 1.0.0) – Gate Definition

Modeled exactly on Fasdent V15.

**Status values:** PASS / FAIL / PENDING / DEFERRED / N/A  
**Blocker=Yes** must be PASS for approval.

### Identity & Source
| ID | Requirement | Blocker | Notes |
|----|-------------|---------|-------|
| TZ-001 | Canonical theme source at repo root | Yes | No mixed static + theme |
| TZ-002 | Version 1.0.0 recorded in style.css + assets | Yes | |
| TZ-003 | No secrets / private data in package | Yes | |
| TZ-004 | Historical static folder archived or clearly non-production | No | |

### Visual / Assets / Motion
| ID | Requirement | Blocker |
|----|-------------|---------| 
| TZ-101 | Desktop + mobile logo correct | Yes |
| TZ-102 | Hero (home) breakpoints + motion intact + enhanced | Yes |
| TZ-103 | All CSS/JS/fonts/images return 200 | Yes |
| TZ-104 | No overflow / clipping any viewport | Yes |
| TZ-105 | Mobile nav (drawer from right) + bottom-nav work | Yes |
| TZ-106 | FAB + contact channels work | Yes |
| TZ-107 | `prefers-reduced-motion` respected | Yes |
| TZ-108 | Homepage has richer motion than secondary pages | Yes |
| TZ-109 | Service pages keep their specific visual language | Yes |

### WordPress / Templates
| ID | Requirement | Blocker |
|----|-------------|---------| 
| TZ-201 | front-page.php renders home design | Yes |
| TZ-202 | All major pages have templates or correct hierarchy | Yes |
| TZ-203 | Menus registered & assignable | Yes |
| TZ-204 | Customizer contact/NAP values used | Yes |
| TZ-205 | Forms (inquiry/contact) process without fatal + show feedback | Yes |
| TZ-206 | No demo/lorem content left in production templates | Yes |

### Quality / A11y / SEO / RTL
| ID | Requirement | Blocker |
|----|-------------|---------| 
| TZ-301 | No PHP fatal / notice on key pages | Yes |
| TZ-302 | Console clean (theme JS) | Yes |
| TZ-303 | RTL correct on all templates | Yes |
| TZ-304 | Keyboard + focus-visible usable | Yes |
| TZ-305 | Basic meta / OG / title-tag | Yes |
| TZ-306 | HTTPS / mixed-content ready | Yes |

---

## 5. Release Checklist (Executable Gate)

### Before Build
- [ ] Confirm `teznevise_work/` is reference only.
- [ ] Confirm brand tokens, phone, email, social links extracted.
- [ ] Confirm no secrets.

### Code Validation
- [ ] PHP syntax check every `.php`.
- [ ] JS syntax check.
- [ ] Asset paths use `get_template_directory_uri()` / `get_theme_file_uri()`.
- [ ] Styles/scripts enqueued once, correct dependencies & versions.
- [ ] `prefers-reduced-motion` media query present and effective.
- [ ] All interactive elements have accessible names / ARIA where needed.

### Staging / Smoke
- [ ] Fresh WP install, activate theme.
- [ ] Set static front page.
- [ ] Assign menus.
- [ ] Verify homepage motion (reveal, hero particles/rings, counters, SEO toggle).
- [ ] Verify every major page type.
- [ ] Mobile drawer, bottom-nav, FAB.
- [ ] Forms submit path.
- [ ] 375 / 768 / 1024 / 1440 no overflow.
- [ ] Reduced-motion mode.
- [ ] RTL visual check.

### Production Gate Decision
> All current **Blocker=Yes** rows in REQUIREMENTS.md are **PASS** with evidence → **APPROVE** and push to main root.  
> Any Blocker=Yes FAIL or PENDING → **REJECT**.

---

## 6. Motion Enhancement Priority List (Home-first)

1. Hero text cascade + visual depth (already good → polish timing & hover).
2. Staggered service / reason / step / article cards with refined spring-like ease.
3. Scroll-triggered header state.
4. Micro-interactions on primary CTAs and service cards.
5. Consistent entrance language across secondary pages (lighter intensity).
6. Counter + SEO expand already solid — keep.
7. Document every new animation with the reduced-motion fallback.

---

## 7. Risk & Non-Goals

**Risks**
- Breaking existing class names → motion dies.
- Over-animating → performance / a11y regression.
- External CDN dependency for fonts/Bootstrap if not self-hosted.

**Explicit non-goals for 1.0.0**
- Full block-theme conversion / FSE overhaul.
- Companion plugin (unless forms demand it).
- New visual redesign or color system change.
- Deep Rank Math / CWV campaign (can be Phase 1.1).
- React/Vite SPA (Fasdent had a parallel experiment; we stay classic PHP).

---

## 8. Immediate Next Actions

1. Create `docs/REQUIREMENTS.md` + `RELEASE_CHECKLIST.md` + `CANONICAL.md` in the working tree.
2. Scaffold `style.css`, `functions.php`, `theme.json`, basic `header.php`/`footer.php`.
3. Convert `front-page.php` first (richest motion target) and prove parity + enhancement.
4. Convert remaining templates systematically.
5. Run the full gate.
6. Push clean theme to repository root of `maziyarid/teznevise`.

---

**Status:** Plan approved for execution. Parallel implementation of Phase 0–1 started 2026-08-17.
