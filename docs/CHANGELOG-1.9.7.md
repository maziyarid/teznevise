# Teznevise 1.9.7

**Date:** 2026-08-22  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`). React remains a separate prototype.

After deploy, an administrator must open wp-admin (or run `wp teznevise classic-content import`) so the 1.9.7 Classic Editor re-import can finish. A notice remains until `teznevise_classic_import_version` is `1.9.7`.

## Classic Editor

Live `/thesis/` still showed only `[tz_thesis_hub]` because 1.9.6 either had not run or treated hub shortcodes as the editor field. 1.9.7:

- bumps `teznevise_classic_import_version` to `1.9.7` so shortcode-only pages are scanned again;
- never overwrites administrator-authored prose (any remainder after shortcodes);
- writes extracted-section HTML, then live builder JSON, then page fields, into `post_content`;
- keeps calculator/form shortcodes in `_teznevise_functional_shortcodes`;
- treats `tz_thesis_*` / `tz_proposal_*` / statistics / simulation hubs and `tz_home` as layout-only so they leave the editor without duplicating the builder.

## Navigation

`hotfix-196.css` made mega panels full-width of the nav (`position:static` on the item + `inset-inline:12px`) and JS bound `mouseenter` on the panel, so hovering the page centre opened Tools/Proposal.

1.9.7 (`hotfix-197.css` priority 110 + `chrome.js`): the item is `position:relative`, the panel hangs from that item (`max(260px, min(680px, 78vw))`), last-three items align to the inline end, and hover is bound only to the parent `<li>`. Closed panels use `pointer-events:none`.

## AI chat

The tools screenshot was a contact-style form. The canonical UI is now `TezNevise_AI_Chat::render_chat` / `[teznevise_ai]`:

- chat log, bubbles, agent + model meta, thinking `<details>`, fullscreen, Enter to send;
- Settings → TezNevise AI: provider API keys and named-agent CRUD (provider, model, endpoint, optional per-agent key, color, thinking, sort, active);
- allow-listed HTTPS hosts: OpenAI, Gemini, OpenRouter, Groq, xAI, Anthropic, Mistral, Together, DeepSeek;
- Gemini uses `generateContent`; Anthropic uses Messages; others are OpenAI-compatible;
- session tokens cannot take over another user’s session; thinking is parsed from `<think>` when enabled;
- default agent inserts are insert-if-missing (TN-H15).

## Account, security, inquiry, blog, comments

- `/account/` is the customer dashboard (not wp-admin). Subscribers are redirected and lose the admin bar.
- Generator, RSD, WLW, shortlinks, XML-RPC, and core `ver=` query args are removed. Login is branded. `DISALLOW_FILE_EDIT` is set when undefined.
- Compact first-party inquiry form in builder heroes, service/page/tools/home heroes. Posts to `teznevise_lead`.
- Featured images use `teznevise-hero` (1440×810) with `sizes` so LCP stays bounded. Key takeaways and AI overview render above the article when filled. Related posts: 4, category or tag.
- Comments: “readers” vs “AI discussion”. Backend **Posts → گفتگوی هوش مصنوعی** stores prompt, interaction, order, speaker names/roles/tags. Admins can reply as humans. `DiscussionForumPosting` JSON-LD is printed.

## Visual system

- FAQ and process steps stay boxed; feature/service cards are unboxed with unique 9-tone palettes (no repeated teal/red).
- Feature icons cycle through a 12-icon Font Awesome set when the item still has a generic tick.
- Motion: `tzRise` stagger, button lift; `prefers-reduced-motion` disables it.
- Page titles centre; leftover H1s remain demoted.

## Branches

`origin` only tracks `main`. Historical local branches (`feat/wordpress-content-template-coverage`, `fix/1.8.5-*`, `fix/1.8.6-unlayer-css`) contain already-merged 1.8.x work, not unreleased product features.
