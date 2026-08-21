# Production Builder-Route Analysis

**Branch:** `chore/production-builder-route-analysis`  
**Scope:** Read-only diagnosis of uncached live responses following the content-template and bootstrap changes.  
**Live changes:** None made by this branch.

## Observed production behavior

The following matrix was collected with individual uncached HTTPS requests on 2026-08-21. A WordPress generic error document was returned for each 500 result; it does not expose a PHP stack trace. Browser and stateless fetch tools have sometimes received differing results for the same URL, so the result should be treated as a reproducible server-side condition requiring host-level logs rather than a user-interface conclusion.

| Route group | Representative routes | Latest observed response | Interpretation |
| --- | --- | ---: | --- |
| Builder-backed pages | `/service-thesis/`, `/service-statistics/`, `/tool-spearman/`, `/about-us/`, `/terms/`, `/privacy-policy/` | 500 | The failure is correlated with pages that hold builder-section metadata. |
| Canonical/template pages | `/tools/`, `/contact-us/`, `/blog/` | 200 | The theme and WordPress installation remain partially available. |
| Canonical service route | `/thesis/` | timed out in the most recent request; earlier request returned 200 | Requires host-log correlation; it is not classified as a reproducible 500. |

> The public error response states only: “یک خطای مهم در این وب سایت رخ داده است.” WordPress suppresses the underlying PHP exception in production, so route-level HTTP status alone cannot responsibly identify a single source line.

## Source observations

The current remote `main` includes release **1.9.3** (`0fafae0`, “restore 1.9.2 bootstrap, mega nav, tool AI chat”). Its theme bootstrap loads the builder before extracted-content support in the following order:

1. `inc/class-teznevise-builder.php`
2. `inc/builder-defaults.php`
3. `inc/extracted-pages.php`
4. `inc/builder-seed.php`

This sequence is structurally sound in the checked source. Repository-wide PHP linting and the builder conversion test passed before this analysis branch was created. The branch therefore does not claim that the newly merged source alone caused the live error.

## Builder execution trace

The affected template family shares the following request path:

1. A page template calls `teznevise_builder_has_sections()` or `teznevise_builder_render_sections()`.
2. `teznevise_builder_get_sections()` reads `_teznevise_builder_sections`, decodes its JSON payload, and sanitizes only registered section types and fields.
3. `teznevise_builder_render_sections()` dispatches enabled sections to their registered renderers.
4. Templates with retained editor content call `teznevise_the_page_leftover_content()`, which separates registered interactive shortcodes from the classic-editor disclosure.

Static inspection confirms the required theme helpers are present and loaded before rendering: `teznevise_url()` is provided by `inc/defaults.php`, while the builder and extracted-content modules are explicitly required by `functions.php`. PHP linting can validate syntax but cannot reproduce WordPress metadata, plugin callbacks, database state, or the host’s PHP configuration; none of those are exposed in the generic production error response.

## Diagnostic boundary

The missing evidence is the production PHP error record with timestamp, file, line, and exception. The next safe step is to inspect the host’s PHP/WordPress error log or restore authenticated WordPress administration access. Until that evidence is available, no speculative source rollback or destructive migration operation should be performed.
