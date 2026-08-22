# Teznevise 1.9.9

**Date:** 2026-08-22  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`). React remains a separate prototype.

After deploy, an administrator should open wp-admin once so AI database `2.2.0` can run and WP-Cron can fire queued debates.

## Migration / rollback

- Forward-only CSS: `hotfix-199.css` is concatenated last. Removing that file from `teznevise_runtime_css_files()` rolls the visual 1.9.9 layer back.
- Engine: `functions.php` loads `teznevise-core/teznevise-core.php` when readable. Deleting or renaming that folder disables vault/router/oracle without touching existing agents.
- Keys: new saves are `enc.v1.` AES-256-CBC with WP salts. Legacy plaintext still decrypts (pass-through). Empty settings fields keep the stored key; a single `-` clears it.
- Research meta `_teznevise_ai_research` may now be an array (`brief`, `sources`, `provider`, `timestamp`, `hash`, `version`). The 1.9.8 string form is still read.
- Debate is `wp_schedule_single_event( 'teznevise_core_run_debate' )` — never blocks `save_post`.

## Where to change models and keys

- Free-model map: `teznevise-core/config/free-models.php` or Posts → هویت عامل‌ها (option `teznevise_core_free_models`).
- Provider keys: Settings → TezNevise AI. Encrypted at rest. Per-post override in «پیکربندی گفتگوی هوش مصنوعی».
- Agent aliases / displayed_model_name: Posts → هویت عامل‌ها (`teznevise_core_agent_profiles`). The agents table is not replaced.

## Operator notes

- Do not store VPS panel passwords in the theme. Rotate any root password that was pasted in chat.
- Official university crests were not licensed; wordmarks are placeholders until the universities provide SVG assets.
- Visit wp-admin after deploy so cron and AI schema upgrades run.
