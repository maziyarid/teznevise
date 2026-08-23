# Teznevise 1.9.13

**Date:** 2026-08-23  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`).

After deploy, open wp-admin once (or load any public page) so the eight agents + skills seed and the backfill queue starts. WP-Cron then processes **one published post at a time** (overview + debate).

## What changed

- Each named agent has relevant skills in `teznevise-core/config/skills.php`, the skills table, SKILL.md overlay, chat chips, and the تزنویسه hub.
- AI overview auto-renders on the post. New publishes queue research + debate. Existing published posts are queued in the background.
- Editing «نمای کلی هوش مصنوعی» in the post editor stamps **بازبینی انسانی**. The next AI run keeps that text unless you check «بازنویسی نمای کلی».

## Rollback

Remove `hotfix-203.css` from `teznevise_runtime_css_files()` to drop the badge/chips. Do not disable the backfill hook while jobs are `running`.
