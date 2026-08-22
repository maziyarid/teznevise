# Tokens

## Five-color foundation

| Token | Value | Role |
|---|---|---|
| Evergreen | `#145D4A` | Brand, actions, links, active state |
| Ink | `#10231D` | Primary text and dark surfaces |
| Mint | `#82D8B9` | Evidence, progress, positive accent |
| Paper | `#FFFFFF` | Reading surface |
| Mist | `#F3F7F5` | Secondary surface and quiet grouping |

Borders, hover states, and tonal variants are derived with `color-mix()` from these foundations. Do not add raw palette colors to component styles.

## Typography

Vazirmatn is self-hosted and is the single product family. Body copy uses a relaxed Persian line-height; headings use tighter rhythm and heavier weights. Long-form content is capped at `72ch`, while focused reading text uses `66ch`.

## Spacing (`--tz-space-*`)

4px base. Use these instead of ad-hoc padding.

| Token | Value | Use |
|---|---|---|
| `--tz-space-1` | 4px | micro gaps |
| `--tz-space-2` | 8px | chip/icon gaps |
| `--tz-space-3` | 12px | compact stacks |
| `--tz-space-4` | 16px | control padding |
| `--tz-space-6` | 24px | card inner |
| `--tz-space-8` | 32px | block gap (`--tz-block-gap`) |
| `--tz-space-10` | 40px | small section |
| `--tz-space-12` | 48px | |
| `--tz-space-16` | 64px | default section (`--tz-section-pad`) |
| `--tz-space-20` | 80px | large section |
| `--tz-space-24` | 96px | hero |

Runtime application lives in `assets/css/hotfix-199.css` (last-wins).

- Corners: control `12px`, card `20px`, panel `28px`, pill `999px`.
- Shadows: subtle elevation and floating navigation only; borders communicate most grouping.
- Motion: 120ms immediate, 200ms control, 320ms layout; standard easing `cubic-bezier(.2,.8,.2,1)`.

## Naming

Use `--tz-color-*`, `--tz-space-*`, `--tz-radius-*`, `--tz-shadow-*`, `--tz-duration-*`, and `--tz-ease-*`. Legacy aliases remain temporarily for migration, but new code uses semantic names.
