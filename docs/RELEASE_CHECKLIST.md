# Teznevise Version 1.0.0 — Release Checklist

## Gate Semantics (single authoritative rule)

**Production approval of the code package is granted if and only if:**

> Every requirement whose **current** Blocker column in `REQUIREMENTS.md` is **Yes** has Status **PASS** with recorded evidence.

There is no second path.

### Handling residual risk

If written acceptance of residual risk for a Blocker=Yes item is given:

1. Set Status = **DEFERRED**
2. Set Blocker = **No** (same edit)
3. Record the acceptance in the Evidence column

After that edit the item is no longer a gate.

## Before Build

- [ ] Confirm repository root is the only production theme source (TZ-001).
- [ ] Confirm `teznevise_work/` is reference only (TZ-004, Blocker=No).
- [ ] Confirm no secrets, backups, or private uploads are included (TZ-003).
- [ ] Confirm approved NAP, phone, email, social channels.

## Code Validation

- [ ] Run PHP syntax checks over every PHP file (TZ-301).
- [ ] Run JavaScript syntax checks (TZ-302).
- [ ] Check theme version header = 1.0.0 (TZ-002).
- [ ] Check all template and asset paths use WordPress APIs.
- [ ] Confirm stylesheets are enqueued once and in correct order.
- [ ] Confirm scripts are enqueued once and do not double-bind navigation (TZ-105).
- [ ] Confirm responsive rules do not create overflow (TZ-104).
- [ ] Confirm `prefers-reduced-motion` fallbacks (TZ-107).
- [ ] Confirm fallback behavior when Customizer settings are absent.

## Staging Validation

- [ ] Activate the exact release package in a clean WordPress installation.
- [ ] Verify homepage template and motion system (TZ-201, TZ-102, TZ-108).
- [ ] Verify logo, hero, fonts, icons, CSS, JS, menus, forms, service cards, footer, FAB, bottom-nav.
- [ ] Test key viewports: 375, 768, 1024, 1440.
- [ ] Check browser console and network requests (TZ-103, TZ-302).
- [ ] Run RTL checks (TZ-303).
- [ ] Run inquiry/contact form tests with synthetic data only (TZ-205).
- [ ] Test reduced-motion mode.

## Production Verification (cut-over)

- [ ] Confirm active theme version before replacement.
- [ ] Deploy only the approved package.
- [ ] Purge caches as configured.
- [ ] Verify HTTP→HTTPS and host redirects (TZ-306).
- [ ] Verify logo and hero on the live host.
- [ ] Verify no mixed-content or theme console errors.
- [ ] Verify forms and NAP.
- [ ] Capture desktop/tablet/mobile evidence under `docs/evidence/`.
- [ ] Record deployment versions (TZ-002).

## Delivery Gate Decision

Apply the single rule only:

> All current **Blocker=Yes** requirements in `REQUIREMENTS.md` are **PASS** with evidence → **APPROVE**.  
> Any current **Blocker=Yes** is FAIL or PENDING → **REJECT**.

Do not invent a second approval path.
