# Live Page Coverage Audit

**Audit date:** 2026-08-21  
**Scope:** Public WordPress pages at `teznevise.ir`, compared against `inc/extracted-page-fields.json`.

## Result

The production catalogue contains **105 published pages**. The extracted content document provides direct path-level coverage for **84 pages**, leaving **21 unmatched pages**. The unmatched set is not a content-loss condition: it consists of a user-account route, four newer legal routes with classic-editor prose, three newly seeded services with intentionally empty classic-editor content, and thirteen top-level calculator routes.

| Group | Count | Content treatment |
| --- | ---: | --- |
| Direct extracted-page coverage | 84 | Builder sections and page meta are populated by the existing extracted-field path. |
| Legal classic-editor pages | 4 | The shared accessible disclosure renders their existing prose in `page-privacy.php`. |
| Newly seeded service pages | 3 | `page-service.php` uses seeded presentation fields and has no classic content to migrate. |
| Top-level calculator pages | 13 | `page-tool.php` preserves functional shortcodes; the seed migration now fills missing safe presentation metadata without replacing existing calculator markup. |
| Account route | 1 | `page-account.php` owns the page’s functional output; its classic editor is intentionally empty. |

## Unmatched paths

| Family | Paths |
| --- | --- |
| Account | `account` |
| Legal | `terms`, `cookies`, `refund`, `research-rules` |
| Services | `service-qualitative`, `service-project`, `service-article` |
| Calculators | `tool-pearson-correlation`, `tool-spearman`, `tool-ttest`, `tool-anova`, `tool-chi-square`, `tool-regression`, `tool-cronbach-alpha`, `tool-sample-size`, `tool-power-analysis`, `tool-content-validity`, `tool-mann-whitney`, `tool-wilcoxon`, `tool-kruskal-wallis` |

## Migration safety contract

The live forced migration was executed only after confirmation that a backup existed. It ran with **dry-run disabled**, **force overwrite enabled**, and **structural-shortcode cleanup disabled**. Its result was **115 processed, 1 migrated, 114 skipped, and 0 errors**. The migration intentionally left all original `post_content` untouched, preserving both editor prose and interactive calculator content.

The complementary calculator seed routine is non-destructive: it assigns `page-tool.php`, writes only missing `_teznevise_*` display fields, and adds the canonical shortcode only where the classic editor is empty. Existing calculator markup and populated custom fields remain unchanged.

## Verification

The source changes completed PHP linting, the builder conversion test, **97 React tests**, ESLint, TypeScript type-checking, and the development-mode production build successfully.
