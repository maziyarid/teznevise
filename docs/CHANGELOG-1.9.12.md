# Teznevise 1.9.12

**Date:** 2026-08-23  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`).

After deploy, open wp-admin once so AI DB `2.3.0` inserts the eight named agents (existing you/general/math/stats rows are kept). WP-Cron runs the debate jobs.

## What changed

- Named 8-agent roster with original SVG logos (`assets/img/agents/{id}.svg`), identity lock, and `teznevise-core/skills/{id}.md`. Per-post SKILL.md overlays the global file.
- Primary/fallback OpenRouter free models live in `teznevise_core_agent_models` (slots from `teznevise_core_free_models()`).
- Async pipeline: research → overview → first responder by topic → peer → debate sequence → optional visualizer (text figures only) → synthesis.
- Admin hub at «تزنویسه» in wp-admin.
- Chat picker + thinking mode + typewriter reveal. Dual-tab comments show named-agent avatars.
- Footer still uses exact Enamad HTML + TrustedSite `cdn.ywxi.net/js/1.js`. ساماندهی leftover removed from تزکوین settings.

## Rollback

Leave `hotfix-202.css` out of `teznevise_runtime_css_files()` to drop avatar CSS. Do not delete the eight agents; deactivate them if needed.
