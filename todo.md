# Full WordPress Page Migration Checklist

- [ ] Export and classify every live page by template, content source, shortcode usage, and custom-field state.
- [ ] Identify each safe shortcode-to-native-template migration and preserve a rollback record for every page.
- [ ] Consolidate existing page material into classic-editor and structured custom-field data without content loss.
- [ ] Replace only presentation shortcodes that have equivalent native templates; retain functional forms and calculators.
- [ ] Open every updated page and record content, navigation, and homepage-style verification results.
- [ ] Commit the migration code and page-level report for review before any production deployment.

## Resumed live-migration actions

- [x] Run the approved force-enabled production migration with dry-run and shortcode cleanup disabled; record the returned statistics.
- [x] Compare all 105 public live page paths against `inc/extracted-page-fields.json` and classify unmatched legacy paths by template family (see `docs/LIVE-PAGE-COVERAGE-AUDIT.md`).
- [x] Ensure unmatched pages retain classic-editor content through the shared disclosure instead of losing prose after a builder template renders.
- [x] Update the calculator seed routine so existing tools retain functional markup while receiving missing template and presentation metadata.
- [x] Run the source verification suite, create and merge the template-coverage pull request, then verify key service, tool, legal, and navigation routes on production.

## Isolated builder-route analysis branch

- [x] Create a branch from the current merged `main` state for production-error analysis; do not make additional WordPress admin changes.
- [x] Capture reproducible uncached HTTP response evidence for successful and failing routes, including deployment and theme-version observations (see `docs/PRODUCTION-BUILDER-ROUTE-ANALYSIS.md`).
- [x] Trace the common builder execution path against the last stable source revision (`e905f6d` 1.9.2 → `0fafae0` 1.9.3) and document the file-level diff plus behavioral implications (see `docs/PRODUCTION-BUILDER-ROUTE-ANALYSIS.md` § Baseline comparison).
- [x] Add a narrowly scoped diagnostic or corrective change only after it is locally validated, then open a separate pull request for further analysis.
