# A2 dummy-content audit (1.9.9)

Searched templates, `inc/`, `teznevise-core/`, `wp_posts` exports in this repo, widgets, and hardcoded PHP.

## Removed / no longer published

| Finding | Location | Action |
|---|---|---|
| Exact A1 disclaimer (`این تب بحث ساختگی عامل‌های نام‌دار است…`) | Previously comments lead + discussion copy | **Removed.** Full-tree grep is zero. |
| `teznevise_ai_comments_placeholder()` published as approved comments | `inc/ai-comments.php` | **Kept as a function, never called.** Empty-body debates skip the turn instead of inventing copy. |
| Visible title `جزئیات و توضیحات بیشتر` | Classic disclosure + React `MoreContent` | **Removed.** Button is `ادامه مطلب`. |

## Kept (real content, not dummy)

| Finding | Location | Why it stays |
|---|---|---|
| «آرای ساختگی» | `inc/extracted-page-fields.json` ethics copy | Real warning against fake legal citations in theses. |
| «آزمایشی» / experimental method | Extracted academic copy | Research-methods vocabulary. |
| Form `placeholder=` attributes | search, comments, calculators | Native UX hints, not published prose. |
| «حجم نمونه» / «داده نمونه» | Sample-size tools | Academic term + calculator demo data loaders. |
| `archive-case_study.php` eyebrow «نمونه‌کار» | Case-study archive | Real CPT label. |
| Seeder sample post in setup | `inc/setup-pages.php` | Only runs on empty installs; not live homepage copy. |

## Flagged (do not fake)

| Finding | Location | Notes |
|---|---|---|
| University official crests | `template-parts/universities.php` | **No licensed SVG crests in the repo.** Typographic wordmarks only. Replace when universities supply assets. |

## Not found

Lorem ipsum, TODO/FIXME in production templates, fake testimonials, demo counters, staging watermarks.
