#!/usr/bin/env python3
"""Teznevise 2.0 frontend runtime contract.

This gate validates the architecture used by the 2.0 branch rather than the
historical hotfix cascade. Legacy styles may remain tracked while dependency
probes are unfinished, but they must not be part of the public v2 manifest.
"""
from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PERF = ROOT / "inc" / "perf.php"
FOUNDATION = ROOT / "assets" / "css" / "v2-foundation.css"
ADAPTER = ROOT / "assets" / "css" / "v2-compat.css"
RESPONSIVE = ROOT / "assets" / "css" / "v2-responsive.css"

errors: list[str] = []


def fail(message: str) -> None:
    errors.append(message)
    print(f"ERROR: {message}", file=sys.stderr)


for path in (PERF, FOUNDATION, ADAPTER, RESPONSIVE):
    if not path.is_file():
        fail(f"Required v2 runtime file missing: {path.relative_to(ROOT)}")

if not errors:
    perf = PERF.read_text(encoding="utf-8")
    match = re.search(
        r"function\s+teznevise_runtime_css_files\s*\(\s*\)\s*\{\s*"
        r"\$files\s*=\s*array\((.*?)\);\s*return\s+array_values",
        perf,
        re.S,
    )
    if not match:
        fail("Could not parse teznevise_runtime_css_files() manifest")
    else:
        manifest = match.group(1)
        required = (
            "assets/css/v2-foundation.css",
            "assets/css/blog.css",
            "assets/css/page-extras.css",
            "assets/css/wp-compat.css",
            "assets/css/v2-compat.css",
            "assets/css/v2-responsive.css",
        )
        for stylesheet in required:
            if stylesheet not in manifest:
                fail(f"v2 runtime manifest is missing {stylesheet}")

        foundation_at = manifest.find("assets/css/v2-foundation.css")
        adapter_at = manifest.find("assets/css/v2-compat.css")
        responsive_at = manifest.find("assets/css/v2-responsive.css")
        if foundation_at < 0 or adapter_at < 0 or foundation_at >= adapter_at:
            fail("v2 foundation must load before the WordPress adapter")
        if adapter_at < 0 or responsive_at < 0 or adapter_at >= responsive_at:
            fail("responsive layer must load after the WordPress adapter")

        for hotfix in range(196, 211):
            name = f"hotfix-{hotfix}.css"
            if name in manifest:
                fail(f"Historical {name} must not be in the v2 runtime manifest")

    foundation = FOUNDATION.read_text(encoding="utf-8")
    adapter = ADAPTER.read_text(encoding="utf-8")
    responsive = RESPONSIVE.read_text(encoding="utf-8")

    if foundation.count("!important") != 4:
        fail("v2-foundation.css must contain exactly four !important declarations")
    if "!important" in adapter:
        fail("v2-compat.css must not contain !important declarations")
    if "!important" in responsive:
        fail("v2-responsive.css must not contain !important declarations")
    if "@media(prefers-reduced-motion:reduce)" not in foundation.replace(" ", ""):
        fail("The four foundation !important declarations must remain in reduced-motion handling")

    structural_selectors = (
        ".tez-builder-grid",
        ".tez-builder-steps",
        ".reason-list",
        ".faq-grid",
        ".tz-hero-orbit",
        ".nav-links > li.is-mega",
        ".footer-logo",
        ".tz-livechat",
    )
    for selector in structural_selectors:
        if selector not in adapter:
            fail(f"WordPress v2 adapter is missing structural selector {selector}")

    if ".menu-btn { display: none; }" not in adapter:
        fail("Desktop hamburger hide contract is missing")
    if ".faq-item.open .faq-a" not in adapter:
        fail("FAQ open-state contract is missing from the v2 adapter")

    responsive_contracts = (
        ".mobile-nav-links .nav-dropdown-toggle",
        ".mobile-nav-links .menu-item.has-dropdown.is-open > .sub-menu",
        '.bottom-nav[data-nav-count="5"]',
        ".tz-fab-wrap",
        ".tez-builder-section-service_cards .tez-builder-grid",
        ".tez-builder-section-process_steps .tez-builder-steps",
        ".article-grid",
    )
    for selector in responsive_contracts:
        if selector not in responsive:
            fail(f"Responsive v2 layer is missing contract {selector}")

    for width in (1024, 900, 680, 420):
        if f"@media (max-width: {width}px)" not in responsive:
            fail(f"Responsive layer is missing the {width}px gate")

    if "left: 50%" not in responsive or "translateX(-50%)" not in responsive:
        fail("Responsive header does not preserve true logo centring")
    if "scroll-snap-type: x mandatory" not in responsive:
        fail("Phone card rails are missing scroll-snap containment")
    if "grid-column: 1 / -1" not in responsive:
        fail("Mobile footer full-span brand/contact contract is missing")

if errors:
    print(f"{len(errors)} v2 runtime check(s) failed", file=sys.stderr)
    raise SystemExit(1)

print("v2 runtime contract passed")
