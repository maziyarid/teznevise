# Migration data security

## Files that must not live in git

| Path | Risk | Status |
|------|------|--------|
| `docs/sep_posts.sql` | High — full `wp_posts` dump; may embed WPCode/snippet bodies with mail config, keys, or PII | Ignored / removed from the tree in 1.6.3 |
| `docs/sep_posts.csv` | High — same content as CSV | Ignored / removed from the tree in 1.6.3 |
| `docs/wpcode-snippets-export-2026-08-18.json` | Medium — snippet source; review for secrets before sharing | Ignored / removed from the tree in 1.6.3 |

Keep local copies outside the repo if you still need them for mapping work. Do not re-commit them.

## Required actions

1. **Rotate credentials** that may have appeared in snippet post bodies (SMTP passwords, Turnstile secrets, API keys).
2. **Do not import** these dumps into production without a redaction pass.
3. Prefer **private storage** for multi‑MB dumps; keep only redacted samples in the theme repo.
4. After migration succeeds on staging, the dumps are optional reference material — they are not required at runtime.

## Runtime path (no dump import needed)

The theme migrator reads **live** `wp_posts` content on the site where the theme is active:

- `Appearance → راه‌اندازی تزنویسه → مهاجرت شورت‌کد → صفحه‌ساز`
- Writes `_teznevise_builder_sections` via `teznevise_builder_save_sections()`
- Auto-run continues in bounded batches on `admin_init` until no remaining candidates exist

CPTs (`download`, `tz_service`, `case_study`) are registered from `inc/cpts.php` when not already registered by WPCode.
