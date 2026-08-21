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

## Scales

- Spacing follows a 4px base: `1, 2, 3, 4, 6, 8, 10, 12, 16, 20, 24`.
- Corners: control `12px`, card `20px`, panel `28px`, pill `999px`.
- Shadows: subtle elevation and floating navigation only; borders communicate most grouping.
- Motion: 120ms immediate, 200ms control, 320ms layout; standard easing `cubic-bezier(.2,.8,.2,1)`.

## Naming

Use `--tz-color-*`, `--tz-space-*`, `--tz-radius-*`, `--tz-shadow-*`, `--tz-duration-*`, and `--tz-ease-*`. Legacy aliases remain temporarily for migration, but new code uses semantic names.
