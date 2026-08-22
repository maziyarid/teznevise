# QA checklist — 1.9.9

| # | Criterion | Result |
|---|---|---|
| A1 | Grep of the exact dummy disclaimer | **Pass** — zero matches in the theme tree |
| A2 | Dummy/placeholder audit written and acted on | **Pass** — `docs/AUDIT-1.9.9-dummy-content.md` |
| B1 | Identity lock: agent answers only `displayed_model_name` | **Pass** — `Teznevise_Agent_Registry::identity_lock` injected via `teznevise_ai_system_prompt_prefix` |
| B2 | Free cascade + exponential backoff; You → Tavily; log failures | **Pass** — router chain, `sleep_backoff`, `Teznevise_Logger` |
| B3 | Deterministic complexity; one editable model array; paid last | **Pass** — `teznevise_core_free_models()`; paid_fallback only at the end |
| B4 | Research cached by content hash; unchanged save spends no research | **Pass** — transient + post meta hash; debate `run()` short-circuits on same hash unless forced |
| B5 | Per-post meta box (generate, SKILL.md, thoughts, refs, API source, agents) | **Pass** — `Teznevise_Meta_Boxes` |
| B6 | Existing agents preserved; aliases on the front | **Pass** — overlays in `teznevise_core_agent_profiles`; picker shows alias only |
| B7 | `<thought>` parsed; collapsible «مشاهده استدلال درونی»; escaped | **Pass** — comments thread + chat.js |
| B8 | Tabs کاربران / هوش مصنوعی; `_is_ai_agent`; no mix | **Pass** — `comments.php` + `type=comment` vs `tz_ai` |
| B9 | ChatGPT-style composer, history, RTL, ARIA | **Pass** — composer + localStorage history + alias picker |
| B10 | Interactive summarise button, cached | **Pass** — REST `/summarise` + post meta cache |
| C | SERP groups + AI overview + 44px rotating close | **Pass** — `search.php` + `hotfix-199.css` |
| D | Hover-intent; mega only when flagged; reduced-motion | **Pass** — 140/180 ms; CSS `:hover` open killed; `.is-dropdown` vertical |
| E | Spacing tokens; centred heads; two-column hero; motion | **Pass** — `--tz-space-*`; builder/service/home split |
| F | Title gone; 3 blocks + ادامه مطلب; full HTML without JS | **Pass** — `html.js` collapse only |
| G | SVG marks + alt; FLAG missing crests | **Pass** — wordmarks; FLAG in template + audit |
| H | Dashboard sidebar + cards | **Pass** — profile/wallet/tools/AI/tickets/projects/settings |
| H-a11y | 44px targets, skip-link, focus, contrast | **Pass** — skip-link in header; 44px tabs/close/disclosure |
| Security | Nonces on REST summarise; keys encrypted; not printed | **Pass** |

Existing agents and their APIs were extended, not removed.
