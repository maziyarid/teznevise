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

errors: list[str] = []


def fail(message: str) -> None:
    errors.append(message)
    print(f"ERROR: {message}", file=sys.stderr)


for path in (PERF, FOUNDATION, ADAPTER):
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
        )
        for stylesheet in required:
            if stylesheet not in manifest:
                fail(f"v2 runtime manifest is missing {stylesheet}")

        foundation_at = manifest.find("assets/css/v2-foundation.css")
        adapter_at = manifest.find("assets/css/v2-compat.css")
        if foundation_at < 0 or adapter_at < 0 or foundation_at >= adapter_at:
            fail("v2 foundation must load before the WordPress adapter")

        for hotfix in range(196, 211):
            name = f"hotfix-{hotfix}.css"
            if name in manifest:
                fail(f"Historical {name} must not be in the v2 runtime manifest")

    foundation = FOUNDATION.read_text(encoding="utf-8")
    adapter = ADAPTER.read_text(encoding="utf-8")

    if foundation.count("!important") != 4:
        fail("v2-foundation.css must contain exactly four !important declarations")
    if "!important" in adapter:
        fail("v2-compat.css must not contain !important declarations")
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
    if "@media (max-width: 1024px)" not in adapter:
        fail("1024px header/menu gate is missing")
    if "@media (max-width: 900px)" not in adapter:
        fail("900px header/menu gate is missing")
    if ".faq-item.open .faq-a" not in adapter:
        fail("FAQ open-state contract is missing from the v2 adapter")

if errors:
    print(f"{len(errors)} v2 runtime check(s) failed", file=sys.stderr)
    raise SystemExit(1)

print("v2 runtime contract passed")
