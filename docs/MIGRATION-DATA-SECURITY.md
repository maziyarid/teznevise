# Migration data security

## Files removed from the tree

`docs/sep_posts.sql`, `docs/sep_posts.csv`, and `docs/wpcode-snippets-export-*.json` are gitignored and must not be re-committed. They were credential-bearing dumps (full `wp_posts` / WPCode snippet source).

## Required actions before merge / public exposure

1. **Rotate credentials** that may appear in snippet post bodies (SMTP passwords, Turnstile secrets, API keys).
2. **Do not import** these dumps into production without a redaction pass.
3. Prefer **private storage or Git LFS** for multi‑MB dumps; keep only redacted samples in the theme repo.
4. After migration succeeds on staging, the dumps are optional reference material — they are not required at runtime.

## Runtime path (no dump import needed)

The theme migrator reads **live** `wp_posts` content on the site where the theme is active:

- `Appearance → راه‌اندازی تزنویسه → مهاجرت شورت‌کد → صفحه‌ساز`
- Writes `_teznevise_builder_sections` via `teznevise_builder_save_sections()`

CPTs (`download`, `tz_service`, `case_study`) are registered from `inc/cpts.php` when not already registered by WPCode.
