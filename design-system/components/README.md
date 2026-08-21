# Component contracts

All public components are server-renderable, RTL-safe, keyboard-operable, and usable without JavaScript.

| Component | Contract | Required states |
|---|---|---|
| Button | `.tz-button` plus `--primary`, `--secondary`, `--quiet` | hover, focus-visible, active, disabled, loading |
| Card | `.tz-card` | default, interactive, selected |
| Article card | `.tz-article-card` | category, title, excerpt, metadata, optional image |
| Author card | `.tz-author-card` | avatar, identity, expertise, profile action |
| Badge | `.tz-badge` | neutral, evidence, success, warning |
| Navigation | `.tz-navigation` | current, expanded, keyboard submenu, mobile |
| Dropdown | `.tz-dropdown` | closed, open, focus transfer, Escape |
| Dialog | `.tz-dialog` | labelled, described, trapped focus, restored focus |
| Search | `.tz-search` | idle, loading, results, empty, error |
| Newsletter | `.tz-newsletter` | consent, validation, success, error |
| Sharing | `.tz-share` | native share, copy success, service fallback |
| Field | `.tz-field` | help, required, focus, invalid, disabled |

Legacy classes such as `.btn-tz`, `.article-card`, and `.share-bar` are supported as migration aliases. New markup should use the canonical names; migrations must not change WordPress hooks, nonce fields, or plugin interoperability.

Icons are decorative only when adjacent text names the action; otherwise they require an accessible name. Do not use emoji as interface icons. Category emoji metadata may exist as controlled editorial content, but is not part of the control icon system.
