# Teznevise static redesign

This package is a static RTL HTML redesign prepared for later PHP / WordPress conversion.

## Main redesigned templates
- `index.html` — homepage
- `blog.html` — blog archive
- `service-thesis.html` — thesis service landing page
- `service-proposal.html` — proposal service landing page
- `service-statistics.html` — statistics service landing page

## Design files
- `assets/css/redesign.css` — new layout/design system
- `assets/js/redesign.js` — mobile navigation + FAQ accordion
- `assets/img/logo.jpg` — existing Teznevise logo retained

The primary brand token is `#145D4A`. The new templates are RTL, responsive, and use semantic HTML. Bootstrap RTL is linked by CDN for easy later integration, while the core layout is also defined in the custom stylesheet.

Existing secondary pages from the source ZIP are retained. Their legacy design-token primary color has also been changed to `#145D4A` so they stay closer to the new brand palette until they are migrated to the new templates.
