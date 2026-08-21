# Teznevise Design System v2

Teznevise is a Persian-first editorial system for research, education, and professional analysis. Its signature is **the research margin**: a calm reading field paired with compact contextual rails, evidence labels, and precise typographic hierarchy. It should feel authored and scholarly—not like a generic SaaS dashboard.

## Principles

1. **Reading before decoration.** Long-form Persian text, evidence, and actions determine layout.
2. **RTL is physical.** Verify actual left/right geometry; do not infer it from source order.
3. **One quiet field, one strong mark.** Use the deep green brand for decisions and focus, not as filler.
4. **Density communicates expertise.** Compact metadata may be dense; body content remains spacious.
5. **Motion explains state.** CSS handles ordinary transitions; reduced-motion is mandatory. No global animation runtime.
6. **Progressive enhancement.** WordPress server rendering remains complete without JavaScript.

## Runtime contract

- `assets/css/tokens.css` is the production CSS token source.
- `theme.json` mirrors editor-facing presets.
- `react-app/src/styles.css` maps Tailwind names to the same semantic values.
- Classic WordPress uses authored `tz-*` component classes, not Tailwind utilities.
- React/TypeScript is reserved for editor blocks and complex/dynamic interactions.
- No Alpine, GSAP, Next.js, or additional framework is added without a measured gap.

## Foundations

- [Tokens](tokens/README.md)
- [Components](components/README.md)
- [Editorial patterns](patterns/README.md)

## Contribution checklist

- Use semantic tokens; do not introduce an unexplained color, radius, shadow, or spacing value.
- Keep the palette to the five foundations documented in tokens.
- Use Vazirmatn for the product. A second family requires a documented editorial need.
- New public components require keyboard, focus, RTL, mobile, and reduced-motion states.
- Extend an owning stylesheet; never add another `*-fix.css` override.
- Verify generated bundles with `python3 scripts/build-frontend-bundles.py`.
