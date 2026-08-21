# Contributing — Teznevise Theme

**Author signature:** `MAZ//ID (Maziyar)`  
**Brand system:** [maziyarid/M-Z](https://github.com/maziyarid/M-Z)

## Commit prefixes (MAZ conventions)

- `MAZ:` feature work
- `MAZ:fix` bug fixes
- `MAZ:docs` documentation

## Code headers

Every new PHP/JS/CSS file should include:

```php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 */
```

## Pull requests

1. Keep RTL Persian fidelity and brand green `#145D4A` for Teznevise product UI
2. Do not remove MAZ//ID attribution
3. Prefer Customizer / page meta over hard-coded copy
4. **Do not add new `assets/css/*-fix.css` overlay files.** Fixes go in the sheet that owns the selector, then rebuild with `python3 scripts/build-frontend-bundles.py`. Public CSS is `tokens` + `components` + `pages` + `chrome` only.

— M•Z
