# QA 1.9.21

After deploy: visit wp-admin once (seeds 8 agents + skills, starts the backfill queue). WP-Cron processes one post at a time.

## AI

- [ ] Named roster (teznevise, christina, ada, professor, parantez, elara, cyrus, mira) exists
- [ ] Each agent has 3 skills in the skills table
- [ ] New published post gets overview + debate without blocking save
- [ ] Existing posts re-queue (option `teznevise_ai_auto_all_v` = 1.9.21)
- [ ] Failed empty jobs retry, not marked done
- [ ] Editing overview in the post editor shows «بازبینی انسانی»
- [ ] Dual tabs کاربران / هوش مصنوعی appear even when WP comments are closed
- [ ] 1.9.19/1.9.20 chat UI still works (closable agent menu, You.com modes, quote-debate)

## Pages

- [ ] Home: one services grid, `#services` still works, orbit says ثبت درخواست
- [ ] `/thesis/` `/proposal/` `/service-statistics/` `/online-calculation-tools/`
- [ ] Privacy / about / team / tools: no compact inquiry form
- [ ] Contact FAQ is a single accordion (answers collapsed until click)
- [ ] Tool pages: one H1, calculator still works
- [ ] 404 and sitemap use live slugs
- [ ] Search groups tools, pages, posts
- [ ] `/account/?view=lost` password reset (no wp-login.php for customers)
- [ ] Footer Enamad exact HTML + delayed TrustedSite
- [ ] University strip does not say همکاران دانشگاهی
