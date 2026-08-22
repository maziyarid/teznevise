# Teznevise 1.9.8

**Date:** 2026-08-22  
**Scope:** live WordPress theme (`maziyarid/teznevise` `main`). React remains a separate prototype.

After deploy, an administrator should open wp-admin once so AI database `2.2.0` (You agent, professional columns) can run. Classic import `1.9.7` still applies if that notice remains.

## Instant paint

PageSpeed reports for `/` and `/thesis/` were blocked by 11 render-blocking CSS files, global AI/calculator JS, lazy LCP images, and GTM/Clarity in the head.

1.9.8:

- inlines `assets/css/critical.css` plus the Regular Vazirmatn `@font-face`;
- concatenates tokens → components → pages → chrome → modernization → hotfix-196 → hotfix-197 → hotfix-198 into `uploads/teznevise-cache/runtime-{hash}.css`;
- loads that bundle and Font Awesome with `media=print` + `onload`;
- delays Google Tag Manager, gtag, and Microsoft Clarity until first input or ~4s idle (console `ERR_NAME_NOT_RESOLVED` is DNS/adblock — the theme no longer lets those requests block paint);
- keeps the first two images eager so Edge/Chrome stop replacing LCP with lazy placeholders;
- enqueues calculators and AI chat only on tool pages.

## Visual builder

The backend field list is hidden. The page builder is a live canvas: click a section, edit title/text in place, use the inspector for icons/items. Advanced page-meta tables sit in a closed `<details>`. Admin Font Awesome is the self-hosted 7.x copy, not cdnjs.

## AI agents and discussion

- Agent CRUD: system prompt, role, language, temperature, max tokens, colour.
- You.com (`api.ydc-index.io`) is the researcher. Collaboration mode `research` runs You first, then other agents read that brief.
- Each post stores `_teznevise_ai_research` and `_teznevise_ai_discussion` (JSON). Generating a discussion reads up to ~900 words of the article, not a 220-word excerpt.
- Frontend comments are threaded (`thread_comments` forced on). Each commenter has `--tz-commenter`. AI names and colours are options under Posts → گفتگوی هوش مصنوعی.

## Tools chat

The tools composer matches ChatGPT/Claude: message box, tools popover (agent/model, collaboration including You-first research, thinking), send, new chat, fullscreen.

## Operator notes

- Do not store VPS panel passwords in the theme. Rotate any root password that was pasted in chat.
- Site Kit may still inject GTM (`GT-TXZHRSLT`). The theme delays those handles; if the hostname does not resolve on a network, the console error remains after idle — it no longer blocks first paint.
