# Teznevise 1.9.10

**Date:** 2026-08-22  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`). React remains a separate prototype.

After deploy, an administrator should open wp-admin once so the waitlist table (`dbDelta`) and AI schema can run.

## Migration / rollback

- Forward-only CSS: `hotfix-200.css` is concatenated after `hotfix-199.css`. Remove it from `teznevise_runtime_css_files()` to roll the 1.9.10 visual layer back.
- Legal copy: `inc/legal-copy.php` filters titles/content/menus at runtime. Removing the require from `functions.php` restores stored strings.
- Waitlist: table `{prefix}teznevise_tool_waitlist`. Phones are vault-encrypted; hashes are HMAC with WP salts.
- Trackers: IDs are defaults in `teznevise_tezcoin_defaults()`. Delayed via `data-tz-delay` until idle/first input (~4s).
- Enamad HTML in `footer.php` is raw and must not be escaped or rewritten.

## Where to change models and keys

- Provider keys + status cards: Settings → TezNevise AI.
- Handwritten model identity: the highlighted «نام مدل نمایشی» field when saving an agent (also Posts → هویت عامل‌ها).
- Per-post API vs global: meta box «منبع API». Custom keys per provider and per agent (`_teznevise_api_key_agent_{id}`).
- Per-post SKILL.md: textarea or «بارگذاری فایل SKILL.md از رسانه».

## Operator notes

- Do not store VPS panel passwords in the theme. Rotate any root password that was pasted in chat.
- University marks: `assets/img/universities/` with `SOURCES.txt`. Replace if a rights-holder requests it.
- Visit wp-admin after deploy so cron, waitlist `dbDelta`, and AI schema upgrades run.
