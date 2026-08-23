# QA 1.9.12

## Agents
- [ ] Eight agents exist after visiting wp-admin: teznevise, christina, ada, professor, parantez, elara, cyrus, mira.
- [ ] you / general / math / stats / gemini_flash / openrouter_free still exist.
- [ ] Each named agent SVG has alt/title. Identity lock answers only `displayed_model_name`.
- [ ] SKILL.md files load; per-post overlay still saves.

## Pipeline
- [ ] Saving a post with «پژوهش و مناظره تولید شود» queues `_teznevise_ai_job`.
- [ ] Order: overview (Teznevise) → first responder → peer → remaining debate sequence → optional visualizer → synthesis.
- [ ] Public replies have no `<thought>` tags; thoughts are in comment meta and a `<details>`.

## UI
- [ ] Mega menu opens after hover-intent delay, closes on grace timeout.
- [ ] Chat picker lists eight agents; thinking mode checkbox; reply types out.
- [ ] Dual tabs still «کاربران» / «هوش مصنوعی».
- [ ] Footer: Enamad exact HTML + TrustedSite script. No ساماندهی / ثبت ملی / اتحادیه.
- [ ] University crests unchanged.

## Security
- [ ] REST summarise still nonce-protected.
- [ ] Keys never printed to JS.
- [ ] Named agents cannot be deleted from the agent form.
