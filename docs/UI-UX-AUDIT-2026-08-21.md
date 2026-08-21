# Teznevise UI/UX Audit

**Site:** https://teznevise.ir/  
**Audit date:** 2026-08-21  
**Primary language / direction:** Persian (`fa-IR`), RTL  
**Desktop audit viewport:** 1363 × 936 CSS px  

## 1. Executive summary

The site has a strong contemporary visual language, clear RTL typography, descriptive service copy, functioning statistical tools, consistent image alternative text, and a useful article reading experience. The live pages tested did not show desktop horizontal overflow, broken images, or unlabeled visible form fields.

The most urgent problems are not cosmetic. They are conversion, trust, routing, and content-rendering defects:

1. Raw shortcodes are present in crawlable/indexed content on important pages, including `/proposal/phd/` and `/contact-us/`.
2. Chapter/service pages generate a malformed internal phone URL such as `/tel:09302822091` instead of a valid `tel:` link.
3. “Enamad” and “Samandehi” trust marks link to the privacy page instead of an external verification record.
4. Competing URLs exist for the same user intent (`/tools/` vs `/online-calculation-tools/`, `/service-thesis/` vs `/thesis/`, `/contact/` vs `/contact-us/`, etc.).
5. The order form and calculators place the primary task below large hero/instruction sections, increasing time-to-action.
6. Claims conflict across the site: 20% vs 10% discount, 9:00–21:00 support vs “available every hour,” and old/current phone numbers in indexed templates.
7. The article author is exposed as the raw WordPress-style username `akumumono`; the team page uses anonymous role labels instead of verifiable expert profiles.

### Recommended priority

| Priority | Meaning | Count |
|---|---:|---:|
| P0 | Broken or misleading core experience; fix immediately | 3 |
| P1 | High conversion, trust, or IA impact | 8 |
| P2 | Accessibility, readability, or polish issue | 7 |
| P3 | Minor copy or visual polish issue | 1 |

## 2. Scope and method

### Coverage

- 73 distinct current HTML URLs collected from the live global navigation and homepage page graph.
- 13 representative pages completed an automated live-DOM sweep.
- Manual interaction tests covered the order form structure, a descriptive-statistics calculator calculation, the homepage, a service page family, and a long-form article.
- Indexed/rendered HTML was cross-checked for proposal, contact, team, testimonial, download, archive, tool, and legacy URLs.
- Additional orphan/alternate URLs were discovered from indexed navigation and archives.

The XML sitemap endpoint could not be opened as a document in the audit browser, so the inventory was reconstructed from the live site graph and indexed page graph. This is broad page coverage, but it is not proof that every unlinked orphan URL has been found. A server-side sitemap crawl should be run during final QA.

### Automated checks

- One H1 and heading order
- Main/header/nav/footer landmarks
- Form labels and field structure
- Image `alt` presence and broken images
- Duplicate IDs
- Empty or unsafe links
- Horizontal overflow
- Tap-target dimensions
- Fixed/sticky overlays
- Internal-link discovery
- Console warnings/errors where available

### Verified strengths

- All 13 fully swept pages had one visible H1.
- All 13 had zero desktop horizontal overflow at 1363 px.
- All 13 had no broken images and no missing visible form labels.
- The tested descriptive-statistics calculation produced the expected mean (3.000), sample variance (2.500), and sample standard deviation (1.581) for `1 2 3 4 5`.
- Article body typography was readable: 16.5 px text, about 31.35 px line-height, and a 668 px reading column.
- A skip-to-content link and semantic page landmarks are present.

## 3. Severity definitions

| Severity | Definition |
|---|---|
| P0 | A core page or action is broken, deceptive, or sends users to an invalid destination. |
| P1 | High risk to conversion, trust, discoverability, privacy expectations, or information architecture. |
| P2 | Meaningful accessibility, readability, consistency, or usability degradation. |
| P3 | Minor content or visual polish defect. |

## 4. Issue documentation

### TZ-001 — Raw shortcode displayed/indexed instead of page content

**Severity:** P0  
**Category:** Rendering / content integrity  
**Affected:** `/proposal/phd/`, `/contact-us/`; stale shortcode output is also indexed for recently rebuilt chapter pages.

**Evidence**

- `/proposal/phd/` exposes `[tz_proposal_phd]` beneath its H1 in rendered/indexed HTML.
- `/contact-us/` exposes `[gravityform id=”3″ title=”true”]` where the contact form should appear.
- The Gravity Forms shortcode uses curly quotation marks, which will not parse as valid shortcode attributes even when the plugin is active.

**Impact**

- Core lead-generation pages appear unfinished or broken.
- Search snippets can expose implementation syntax.
- Users lose the expected form and cannot complete the intended task.

**Fix**

1. Replace curly quotes with ASCII quotes if Gravity Forms is retained.
2. Confirm the shortcode/plugin registration executes on the current template.
3. Prefer server-rendered blocks/components instead of content-dependent shortcodes for critical landing pages.
4. Purge page/cache/CDN layers and request reindexing after validation.

**Acceptance criteria**

- No visible or source-indexable text matches `\[[a-zA-Z_].*\]` on public pages.
- The intended form/content renders with JavaScript disabled where feasible.
- Page output remains correct for anonymous users and crawlers.

---

### TZ-002 — Malformed telephone links become internal URLs

**Severity:** P0  
**Category:** Navigation / conversion  
**Affected:** Chapter-one through chapter-five pages and the tested thesis discipline pages; likely the shared thesis/proposal template.

**Evidence**

The live internal-link graph resolves the phone action to:

```text
https://teznevise.ir/tel:09302822091
```

instead of:

```text
tel:+989302822091
```

**Impact**

- The call CTA can navigate to a nonexistent site route instead of opening the dialer.
- Mobile conversion is directly lost.

**Fix**

Use an absolute telephone scheme in the template:

```html
<a href="tel:+989302822091">۰۹۳۰۲۸۲۲۰۹۱</a>
```

**Acceptance criteria**

- Every phone action has an `href` beginning with `tel:+98`.
- No internal URL contains `/tel:`.
- Phone links work on iOS, Android, and desktop browser handoff.

---

### TZ-003 — Trust marks do not verify trust

**Severity:** P0  
**Category:** Trust / credibility  
**Affected:** Global footer on current templates.

**Evidence**

Both “اینماد” and “ساماندهی” link to `https://teznevise.ir/privacy/`. “SSL” is a static label.

**Impact**

- Users expect trust badges to open the issuing authority’s verification record.
- Linking to an unrelated internal policy can look deceptive.

**Fix**

- Replace placeholders with official verification widgets/links supplied by the issuer.
- If no active verification exists, remove the badges until it does.
- Keep privacy/security claims separate from certification marks.

**Acceptance criteria**

- Each badge opens the correct external verification record in a safe new tab.
- The verification record matches the same domain/legal entity.
- No badge links to a generic internal page.

---

### TZ-004 — Competing URLs for the same user intent

**Severity:** P1  
**Category:** Information architecture / conversion  
**Affected:**

- `/tools/` and `/online-calculation-tools/`
- `/service-thesis/` and `/thesis/`
- `/service-proposal/` and `/proposal/`
- `/service-statistics/` and `/statistics/`
- `/contact/` and `/contact-us/`
- `/our-team/` and `/team/`
- `/blog/`, `/posts/`, and category archives
- `/download/` and `/downloads/`
- `/privacy/` and `/privacy-policy/`

**Evidence**

- The homepage service card points to `/service-thesis/`, while the main navigation points to `/thesis/`.
- The footer’s “tools” link points to `/tools/`, while the main navigation points to `/online-calculation-tools/`.
- `/service-*` pages are very thin hero-and-CTA shells, while their canonical service pages contain the actual decision-making content.

**Impact**

- Users get materially different depth depending on which link they click.
- Analytics and conversion attribution are fragmented.
- Duplicate intent weakens search and navigation clarity.

**Fix**

Choose one canonical URL per intent, update every internal link, and 301-redirect alternates. Recommended canonical set:

- `/thesis/`
- `/proposal/`
- `/statistics/` or one consolidated statistics landing page
- `/online-calculation-tools/`
- `/contact-us/`
- `/our-team/`
- `/blog/`
- `/downloads/`
- `/privacy/`

**Acceptance criteria**

- One canonical URL per intent.
- Alternates return one-hop 301 redirects.
- Navigation, cards, breadcrumbs, footer, sitemap, and canonicals agree.

---

### TZ-005 — Legacy templates and stale indexed output coexist with the new design

**Severity:** P1  
**Category:** Cross-site consistency / search experience  
**Affected:** Legacy service pages, archives, downloads, some calculators, contact/team variants, and cached chapter pages.

**Evidence**

Indexed output contains:

- `طراحی RTL واکنش‌گرا — WordPress Theme 1.5.0`
- Theme versions 1.5.0 and 1.6.3 on different pages
- Old and current header/footer structures
- Old phone `09331663849` alongside current `09302822091`
- Raw shortcode residue on previously rebuilt pages

**Impact**

- Search snippets may show internal implementation text or an obsolete phone number.
- The experience feels like multiple sites stitched together.
- Hidden legacy DOM creates excessive navigation noise for crawlers and assistive technology.

**Fix**

1. Remove theme/version marketing text from public output.
2. Consolidate all public templates onto one header/footer/component system.
3. Remove hidden duplicate menus rather than hiding them with CSS.
4. Purge caches and submit changed URLs for re-crawl.

**Acceptance criteria**

- Public HTML contains one active header and footer.
- No public output exposes theme name/version.
- One phone number is used everywhere.
- Search snippets no longer contain shortcode or template boilerplate.

---

### TZ-006 — The order form is below the fold on the order page

**Severity:** P1  
**Category:** Conversion UX  
**Affected:** `/inquiry/`

**Evidence**

At 1363 × 936, the first viewport shows a large hero followed by four contact-method cards. The actual order form begins near/below the bottom of the viewport.

**Impact**

The page title and CTA promise “submit an order,” but the primary action is visually delayed. Users may click WhatsApp or abandon before discovering the form.

**Fix**

- Put the form immediately below/alongside the H1 and short reassurance copy.
- Move contact cards after the form or reduce them to a compact secondary row.
- Keep the first step to 3–4 fields and reveal optional details progressively.

**Acceptance criteria**

- On a 768 px-high desktop and common mobile viewport, the first required field and primary submit CTA are visible without a full-page scroll.
- The form is the dominant action; alternate contact methods are secondary.

---

### TZ-007 — WhatsApp GET forms expose lead data in the URL and do not validate phone format

**Severity:** P1  
**Category:** Form UX / privacy expectation  
**Affected:** `/inquiry/`

**Evidence**

The main lead form submits via GET to `https://wa.me/989302822091` with `name`, `phone`, `service`, `message`, and a hidden `text` field. The callback form also sends a phone number as the WhatsApp `text` query parameter. The phone inputs use `type="tel"` but no `pattern`, normalization, or visible format guidance.

**Impact**

- Name, phone, and project details can appear in URL/history and third-party logs.
- This conflicts with the adjacent “confidential” reassurance.
- Arbitrary values can pass native validation.

**Fix**

- Submit to a first-party POST endpoint, then create a minimal WhatsApp handoff after explicit user choice.
- Do not place sensitive project details in GET query parameters.
- Normalize Persian/Latin digits and validate Iranian mobile numbers.
- Explain that WhatsApp is a third-party channel before handoff.

**Acceptance criteria**

- No personal/project data appears in the page URL.
- Invalid phone numbers show an inline Persian error.
- Privacy text accurately describes every receiving party/channel.

---

### TZ-008 — Conflicting commercial and support claims

**Severity:** P1  
**Category:** Content consistency / trust  
**Affected:** `/thesis/`, `/contact-us/`, header, proposal pages, indexed calculator/service pages.

**Evidence**

- Thesis page title advertises **20%** off, while the visible page advertises **10%**.
- The header says support is Saturday–Thursday, 9:00–21:00, while the contact page says the team is available “every hour of the day.”
- Indexed pages contain both `09331663849` and `09302822091`.

**Impact**

Contradictory offers and availability expectations reduce trust and can create support disputes.

**Fix**

Create one centrally managed source for phone number, hours, discount amount, and offer expiry. Remove hard-coded values from templates and page content.

**Acceptance criteria**

- The same discount, expiry, phone, and hours appear in title, metadata, hero, FAQs, and footer.
- Expired offers automatically deactivate.

---

### TZ-009 — Calculator task is delayed by hero and instructions

**Severity:** P1  
**Category:** Task flow  
**Affected:** All 18 calculator detail pages.

**Evidence**

On the descriptive-statistics calculator, the first viewport contains the header, hero, a large gap, guide heading, and instruction cards. The data input is below that content.

**Impact**

Users arriving with a clear intent (“calculate”) must scroll past explanation before they can act.

**Fix**

- Put the calculator card directly after the H1/one-sentence description.
- Collapse instructions into “How to use” disclosure below or beside the form.
- Preserve result state and scroll/focus the result heading after calculation.

**Acceptance criteria**

- Primary inputs and calculate button are visible in the first viewport on desktop and near the top on mobile.
- Results receive focus/announcement after calculation.

---

### TZ-010 — Result tables lack a programmatic caption and explicit state announcement

**Severity:** P2  
**Category:** Accessibility / tool UX  
**Affected:** Calculator result pages/components.

**Evidence**

The descriptive-statistics result renders a correct table but has no `<caption>`. The surrounding “summary” label is a generic element, and no live-region behavior was evident in the DOM snapshot.

**Impact**

Screen-reader users may not receive a clear announcement that results appeared or what the table represents.

**Fix**

- Add a visible or visually hidden `<caption>`.
- Wrap new result status in `aria-live="polite"` or move focus to the result heading.
- Associate interpretation text with the table via `aria-describedby`.

**Acceptance criteria**

- Result appearance is announced without forcing users to explore the page.
- Every result table has an accurate caption and column headers.

---

### TZ-011 — Oversized, dense sticky header consumes too much workspace

**Severity:** P2  
**Category:** Navigation / responsive ergonomics  
**Affected:** Current global template.

**Evidence**

The combined sticky header is about 129 px high at the tested viewport, approximately 14% of the 936 px viewport. The top bar, main navigation, three icon actions, “about,” and order CTA compete for attention.

Several controls are 36–41 px high; these are not necessarily WCAG failures, but they are below the commonly recommended 44 px touch target.

**Impact**

- Less content is visible above the fold.
- Dense controls increase cognitive load and mobile mis-taps.

**Fix**

- Collapse the utility bar after scroll or merge it into the main header.
- Keep one primary CTA.
- Ensure touch targets are at least 44 × 44 px where practical.

**Acceptance criteria**

- Sticky header height is ≤88 px on desktop and ≤72 px on mobile after scroll.
- All icon actions have visible tooltips/labels and adequate target size.

---

### TZ-012 — Heading hierarchy skips levels in the global footer

**Severity:** P2  
**Category:** Accessibility / semantics  
**Affected:** All 13 live pages in the automated sweep; likely the current global template.

**Evidence**

The footer begins with H4 headings such as “خدمات” after the main page’s H2/H3 structure. The automated outline detected a level skip on every swept page.

The dropdown arrow is also included as a private-use Font Awesome glyph in accessible link text (for example `مایان-نامه `).

**Impact**

- Heading navigation is less predictable for screen-reader users.
- Decorative font glyphs may be announced as meaningless characters.

**Fix**

- Use H2 or H3 for footer section headings based on the page outline, or non-heading text if not part of the document outline.
- Mark decorative icons `aria-hidden="true"` and keep them outside the accessible name.

**Acceptance criteria**

- Automated outline has no level jumps.
- Screen-reader link names contain only the intended Persian label.

---

### TZ-013 — Internal design notes are visible as customer-facing copy

**Severity:** P1  
**Category:** Content UX  
**Affected:** Homepage.

**Evidence**

Visible text includes design/implementation notes rather than user-facing outcome copy.

**Impact**

The page sounds like a design review or template brief, breaking immersion and reducing perceived professionalism.

**Fix**

Rewrite as customer outcomes rather than implementation description.

**Acceptance criteria**

- No public copy describes the page’s design, layout, buttons, or implementation.

---

### TZ-014 — Anonymous/raw identities weaken academic credibility

**Severity:** P1  
**Category:** Trust / E-E-A-T / content design  
**Affected:** Articles and `/our-team/`.

**Evidence**

- Article byline is the raw username `akumumono`.
- The team page lists generic roles such as “senior epidemiology researcher” and “simulation specialist,” but no names, photos, verifiable credentials, publications, or profile links.
- Counters expose duplicated initial values in crawlable text, e.g. `120+ ۰`.

**Impact**

Users cannot connect academic advice or service claims to accountable experts. The raw account name also reveals unnecessary WordPress identity information.

**Fix**

- Create human author profiles with Persian name, role, relevant credentials, review policy, and selected publications/ORCID where appropriate.
- Use editorially reviewed organization authors only when a named reviewer is shown.
- Fix counter markup so assistive/crawler text contains one final value.

**Acceptance criteria**

- Every article has a credible author/reviewer profile.
- Team claims have verifiable evidence appropriate to privacy constraints.
- No raw login/usernames are exposed as public bylines.

---

### TZ-015 — Review statistics are not supported by an inspectable evidence path

**Severity:** P2  
**Category:** Trust UX  
**Affected:** `/testimonials/`.

**Evidence**

The page claims a 4.9 score from 2,840+ reviews, 98% satisfaction, 72% repeat business, 68% referrals, and 95% on-time delivery, while only 12 anonymized reviews are displayed. The page explains privacy but does not explain calculation period, collection method, denominator, or independent verification.

**Impact**

Large precise numbers without methodology may look synthetic, even when genuine.

**Fix**

- Add “How these figures are calculated,” date range, sample size, source, and last updated date.
- Distinguish verified customer reviews from editorial testimonials.
- Provide pagination or an accessible filtered archive if thousands of reviews exist.

**Acceptance criteria**

- Every aggregate metric has a visible methodology and update date.
- “Verified” has a defined, auditable meaning.

---

### TZ-016 — Duplicate or empty H1s in download templates

**Severity:** P2  
**Category:** Content structure / accessibility  
**Affected:** `/download-category/proposal/`, `/download/proposal-arshad-azad-olum-tahghighat/`, likely shared download templates.

**Evidence**

- The category output contains an empty H1 followed by `فرم پروزال` as another H1.
- The download detail repeats the same page title as two H1 headings.

**Impact**

The document outline is ambiguous and screen-reader heading navigation is noisy.

**Fix**

Keep exactly one non-empty H1. Use H2 for the detail-card title or remove the duplicate text.

**Acceptance criteria**

- Every indexable page has exactly one visible, non-empty H1.

---

### TZ-017 — Article checklist looks interactive but cannot be used

**Severity:** P2  
**Category:** Content interaction  
**Affected:** Long-form article templates using checklists, including `/how-to-write-first-chapter-thesis/`.

**Evidence**

The nine checklist items are rendered as disabled checkboxes.

**Impact**

Users read the control as a task checklist but cannot check items, reducing utility and creating a false affordance.

**Fix**

- Make checkboxes interactive and store state locally, or replace them with non-interactive check icons/bullets.
- Avoid disabled native controls when no disabled workflow exists.

**Acceptance criteria**

- The visual affordance matches actual behavior.
- Interactive checklists are keyboard accessible and retain state.

---

### TZ-018 — Duplicate navigation/content noise is indexable

**Severity:** P2  
**Category:** Accessibility / crawl cleanliness  
**Affected:** Legacy pages, archives, some current indexed outputs.

**Evidence**

Indexed pages contain two full menu systems, placeholder items such as `-`, `Newsletter`, generic `Image 1`, repeated “no comments” strings, and contact/cookie-widget content mixed into page snippets.

**Impact**

- Search snippets can prioritize boilerplate over the page’s purpose.
- Screen-reader users traverse excessive repetitive content.
- Duplicate DOM increases maintenance and regression risk.

**Fix**

- Render only the active responsive menu; do not include a second legacy menu hidden by CSS.
- Remove placeholder menu items and generic image labels.
- Prevent widget/modal copy from dominating indexable output where appropriate.

**Acceptance criteria**

- One navigation system is present in public DOM.
- Search snippets lead with page-specific content.

---

### TZ-019 — Typo in a high-visibility search suggestion

**Severity:** P3  
**Category:** Copy quality  
**Affected:** Global search overlay/homepage suggestion.

**Evidence**

`فرم خام پرپوزال` should be `فرم خام پروپوزال`.

**Fix / acceptance criteria**

Correct the label and its search query; add a Persian copy QA checklist for shared navigation and CTAs.

## 5. Page coverage matrix

Issue IDs in this table refer to the documentation above. Global issues (`TZ-003`, `TZ-005`, `TZ-011`, `TZ-012`, `TZ-018`) should be retested on every template after remediation.

| URL | Profile | Primary issue IDs |
|---|---|---|
| `/` | Home | 003, 004, 005, 011, 012, 013, 018, 019 |
| `/privacy/` | Legal | 003, 005, 011, 012, 018; links to `/contact/` alternate |
| `/testimonials/` | Trust | 003, 005, 011, 012, 015, 018 |
| `/inquiry/` | Lead form | 003, 005, 006, 007, 011, 012 |
| `/thesis/` | Primary service | 003, 004, 005, 008, 011, 012 |
| `/thesis/chapter-one/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/thesis/chapter-two/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/thesis/chapter-three/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/thesis/chapter-four/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/thesis/chapter-five/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/thesis/humanities/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/engineering/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/pure-science/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/medical-health/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/art-architecture-media/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/agriculture-natural-resources/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/animal-science-veterinary/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/interdisciplinary/` | Discipline landing | 002, 003, 005, 008, 011, 012 |
| `/thesis/phd/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/thesis/international/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/proposal/` | Primary service | 003, 004, 005, 008, 011, 012 |
| `/proposal/phd/` | Service detail | 001, 003, 004, 005, 011, 012 |
| `/proposal/project/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/proposal/english/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/proposal/qualitative/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/proposal/quantitative/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/proposal/applied-research/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/proposal/medical/` | Service detail | 002, 003, 005, 008, 011, 012 |
| `/blog/` | Article index | 003, 004, 005, 012, 014, 018 |
| `/online-calculation-tools/` | Tool index | 003, 004, 005, 011, 012, 018 |
| `/online-calculation-tools/wilcoxon-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/descriptive-statistics-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/sample-size-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/kr20-kr21-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/kruskal-wallis-calculator/` | Calculator | 003, 005, 008, 009, 010, 011, 012 |
| `/online-calculation-tools/t-test-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/chi-square-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/mann-whitney-calculator/` | Calculator | 003, 005, 008, 009, 010, 011, 012 |
| `/online-calculation-tools/goodness-of-fit-calculator/` | Calculator | 003, 005, 008, 009, 010, 011, 012 |
| `/online-calculation-tools/cronbachs-alpha-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/regression-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/anova-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/power-analysis-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/content-validity-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/cohens-kappa-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/pearson-correlation-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/online-calculation-tools/icc-calculator/` | Calculator | 003, 005, 008, 009, 010, 011, 012 |
| `/online-calculation-tools/spearman-correlation-calculator/` | Calculator | 003, 005, 009, 010, 011, 012 |
| `/contact-us/` | Contact | 001, 003, 004, 005, 008, 011, 012, 018 |
| `/about-us/` | Corporate | 003, 005, 008, 011, 012, 014, 018 |
| `/service-thesis/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/service-proposal/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/service-statistics/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/tools/` | Alternate tool index | 003, 004, 005, 011, 012 |
| `/service-simulation/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/service-qualitative/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/service-project/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/service-article/` | Thin service shell | 003, 004, 005, 011, 012 |
| `/necessity-research/` | Article | 003, 005, 011, 012, 014, 017, 018 |
| `/thesis-journey/` | Category/archive | 003, 004, 005, 012, 014, 018 |
| `/statement-differences-proposal-first-chapter/` | Article | 003, 005, 011, 012, 014, 017, 018 |
| `/how-to-write-first-chapter-thesis/` | Article | 003, 005, 011, 012, 014, 017, 018 |
| `/statistics/` | Primary/alternate service | 003, 004, 005, 011, 012 |
| `/our-team/` | Team/trust | 003, 004, 005, 011, 012, 014, 018 |
| `/careers/` | Recruitment | 003, 005, 011, 012, 018 |
| `/join-us/` | Recruitment | 003, 005, 011, 012, 018 |
| `/our-story/` | Corporate | 003, 005, 011, 012, 014, 018 |
| `/achievements/` | Trust | 003, 005, 011, 012, 014, 018 |
| `/case-studies/` | Trust/archive | 003, 004, 005, 011, 012, 014, 018 |
| `/terms/` | Legal | 003, 005, 011, 012, 018 |
| `/cookies/` | Legal | 003, 005, 011, 012, 018 |
| `/refund/` | Legal | 003, 005, 011, 012, 018 |
| `/research-rules/` | Legal | 003, 005, 011, 012, 018 |

## 6. Additional indexed/alternate URLs to reconcile

These were found in the indexed page graph but not in the current 73-URL homepage graph. Confirm whether each belongs in the XML sitemap; keep, redirect, or noindex intentionally.

| URL | Recommended action |
|---|---|
| `/contact/` | 301 to `/contact-us/` |
| `/team/` | 301 to `/our-team/` |
| `/privacy-policy/` | 301 to `/privacy/` |
| `/posts/` | Consolidate with `/blog/` or canonicalize/noindex |
| `/download/` | Consolidate with `/downloads/` |
| `/downloads/` | Choose as download hub canonical if retained |
| `/download-category/proposal/` | Keep only if useful; fix empty/duplicate H1 |
| `/download/proposal-arshad-azad-olum-tahghighat/` | Fix duplicate H1 |
| `/price-calculator/` | Add to current IA or remove from index if obsolete |
| `/proposal/master-proposal-writing/` | Reconcile with `/proposal/` |
| `/proposal/class/` | Reconcile with `/proposal/project/` |
| `/uncategorized/` | Move content and 301/noindex the archive |
| `/thesis-journey/page/2/` | Verify archive pagination/canonical behavior |
| `/thesis/medical-health/medicine/` | Confirm hierarchy and sitemap inclusion |
| `/statistics/data-entry-cleaning/` | Add to statistics IA or remove if orphaned |
| `/statistics/spss-analysis/` | Add to statistics IA or remove if orphaned |
| `/statistics/epi-info/` | Add to statistics IA or remove if orphaned |

Also inspect the older nested humanities routes shown in indexed navigation (management, psychology, law, social science, philosophy, and history). They are absent from the current live homepage graph.

## 7. Remediation roadmap

### Phase 0 — Same day

1. Fix/remove raw shortcodes on `/proposal/phd/` and `/contact-us/`.
2. Fix all malformed `/tel:` links.
3. Remove or correctly verify Enamad/Samandehi badges.
4. Remove internal design-note copy from the homepage.

### Phase 1 — 1–3 days

1. Choose canonical routes and add one-hop 301 redirects.
2. Replace all thin `/service-*` links with the richer canonical service pages.
3. Centralize phone, support hours, discount, and expiry values.
4. Remove public theme/version boilerplate and duplicate legacy menus.
5. Fix WhatsApp lead handling and phone validation.

### Phase 2 — 1 week

1. Move order/calculator interactions above explanatory content.
2. Reduce sticky-header height and simplify actions.
3. Correct footer heading hierarchy and decorative icon semantics.
4. Add credible author/reviewer and team profiles.
5. Add methodology to testimonial statistics.
6. Fix download H1 structure and interactive checklist affordances.

### Phase 3 — QA and measurement

1. Crawl the actual XML sitemap and compare it with canonicals and navigation.
2. Require every sitemap URL to return 200 or one intentional 301; no chains.
3. Run axe/Lighthouse on each unique template at 360, 390, 768, 1024, and 1440 px.
4. Keyboard-test navigation, search, forms, calculators, accordions, and share controls.
5. Verify conversion analytics for order submit, phone, WhatsApp, calculator completion, and form errors.

## 8. Release acceptance checklist

- [ ] No raw shortcode text on any public/indexable page.
- [ ] No internal URL contains `/tel:`.
- [ ] Trust badges open issuer verification records.
- [ ] One canonical URL per service/tool/contact/team/blog/download/privacy intent.
- [ ] No public theme/version or duplicate legacy navigation output.
- [ ] One phone number, support schedule, and discount source site-wide.
- [ ] Lead data is not placed in GET URLs.
- [ ] Order form and calculator inputs appear near the top of their pages.
- [ ] Exactly one non-empty H1 per indexable page.
- [ ] Heading levels do not skip.
- [ ] Result tables have captions and new results are announced.
- [ ] Every article has a credible author/reviewer profile.
- [ ] Testimonial aggregate statistics have methodology and update dates.
- [ ] XML sitemap, canonicals, internal links, redirects, and indexability agree.

## 9. Key evidence URLs

- https://teznevise.ir/
- https://teznevise.ir/inquiry/
- https://teznevise.ir/thesis/
- https://teznevise.ir/proposal/phd/
- https://teznevise.ir/contact-us/
- https://teznevise.ir/online-calculation-tools/
- https://teznevise.ir/online-calculation-tools/descriptive-statistics-calculator/
- https://teznevise.ir/how-to-write-first-chapter-thesis/
- https://teznevise.ir/our-team/
- https://teznevise.ir/testimonials/
- https://teznevise.ir/download-category/proposal/
- https://teznevise.ir/download/proposal-arshad-azad-olum-tahghighat/

---

## 10. Applicability note (added by AI assistant during triage)

The majority of the issues in this audit (TZ-001, TZ-003, TZ-004, TZ-005, TZ-006, TZ-007, TZ-008, TZ-013, TZ-014, TZ-015, TZ-016, TZ-018, TZ-019) describe **WordPress content, page-builder data, plugin configuration, or WordPress admin settings** (post content, menus, redirects, footer widget text, trust-badge links, contact/pricing copy, author profiles, review data). These cannot be fixed by a source-code commit to this repository; they require edits in the live WordPress admin (Pages, Menus, Widgets, and relevant plugin settings) plus server-level 301 redirects.

The remaining issues describe **theme template/behavior defects that can be fixed in code**: TZ-002 (malformed `tel:` links), TZ-009 (calculator layout order), TZ-010 (result table captions/live regions), TZ-011 (sticky header sizing/touch targets), TZ-012 (footer heading hierarchy and icon semantics), and TZ-017 (non-functional checklist checkboxes).

At the time this report was committed, the assistant did not have reliable read access to the current template source in this session (the file-content tool returned only file metadata, and direct raw-file fetches failed), so the code-level fixes above were not yet applied to `main`. They should be implemented as a follow-up commit/PR once full file contents can be safely read and verified, rather than rewritten blindly.