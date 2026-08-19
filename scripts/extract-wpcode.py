#!/usr/bin/env python3
"""Extract WPCode calculator/shortcode snippets into theme files (one-shot)."""
from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SRC = ROOT / "docs" / "wpcode-snippets-export-2026-08-18.json"
OUT_PHP = ROOT / "inc" / "legacy-wpcode.php"
OUT_CSS = ROOT / "assets" / "css" / "legacy-wpcode.css"

PHP_TITLES = {
    "Sample Size Calculator Styles & Shortcodes",
    "careers page terms checkbox",
    "cronbach alpha calculator shortcode & schema",
    "pearson-correlation-calculator",
    "content-validity-calculator",
    "power-analysis-calculator",
    "Service CTA Shortcode",
    "Spearman's Rank Correlation",
    "t-test-calculator",
    "descriptive-statistics-calculator",
    "kr20-kr21-calculator",
    "cohens-kappa-calculator",
    "anova-calculator",
    "mann-whitney-calculator",
    "wilcoxon-calculator",
    "kruskal-wallis-calculator",
    "regression-calculator",
    "chi-square-calculator",
    "goodness-of-fit-calculator",
    "icc-calculator",
    "online-calculation-tools",
    "Price Calculator Addons",
    "Download Template",
}

CSS_TITLES = {
    "Calculators' common styles",
    "Sample Size Calculator",
    "cronbach alpha calculator",
    "Service CTA Shortcode Styles",
    "careers page styles",
    "Price Calculator Styles",
}

SKIP_FUN = re.compile(
    r"function\s+(teznevise_register_|teznevise_download_cpt)",
)


def strip_style(html: str) -> str:
    css_parts = re.findall(r"<style[^>]*>([\s\S]*?)</style>", html, re.I)
    if css_parts:
        return "\n\n".join(p.strip() for p in css_parts)
    return html.strip()


def primary_symbol(code: str) -> str:
    m = re.search(r"add_shortcode\(\s*'([^']+)'\s*,\s*'([^']+)'", code)
    if m:
        return m.group(2)
    m = re.search(r"function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(", code)
    return m.group(1) if m else ""


def wrap_snippet(title: str, code: str) -> str:
    code = code.strip()
    if code.startswith("<?php"):
        code = code[5:].lstrip()
    symbol = primary_symbol(code)
    guard = f"! function_exists( '{symbol}' )" if symbol else "true"
    # Keep original add_shortcode; the function_exists guard prevents redeclare.
    return (
        f"/* --- {title} --- */\n"
        f"if ( {guard} ) {{\n"
        f"{code}\n"
        f"}}\n"
    )


def main() -> None:
    data = json.loads(SRC.read_text(encoding="utf-8"))
    php_chunks = [
        "<?php\n",
        "/**\n",
        " * Legacy WPCode shortcodes restored after the HTML → WordPress migration.\n",
        " * Loaded only as fallbacks so leftover [tz_*] tags render the original UI.\n",
        " *\n",
        " * @package Teznevise\n",
        " */\n",
        "if ( ! defined( 'ABSPATH' ) ) {\n\texit;\n}\n\n",
    ]
    css_chunks = [
        "/**\n * Legacy WPCode calculator / CTA / careers styles.\n",
        " * Extracted from docs/wpcode-snippets-export-2026-08-18.json.\n */\n",
    ]
    seen_php = []
    seen_css = []
    for sn in data:
        title = sn.get("title") or ""
        code = sn.get("code") or ""
        ctype = sn.get("code_type") or ""
        if title in PHP_TITLES and ctype == "php":
            php_chunks.append(wrap_snippet(title, code))
            php_chunks.append("\n")
            seen_php.append(title)
        elif title in CSS_TITLES and ctype == "html":
            css_chunks.append(f"\n/* --- {title} --- */\n")
            css_chunks.append(strip_style(code))
            css_chunks.append("\n")
            seen_css.append(title)

    missing_php = PHP_TITLES - set(seen_php)
    missing_css = CSS_TITLES - set(seen_css)
    if missing_php or missing_css:
        raise SystemExit(f"missing php={missing_php} css={missing_css}")

    OUT_PHP.write_text("".join(php_chunks), encoding="utf-8")
    OUT_CSS.write_text("".join(css_chunks), encoding="utf-8")
    print(f"wrote {OUT_PHP} ({OUT_PHP.stat().st_size} bytes)")
    print(f"wrote {OUT_CSS} ({OUT_CSS.stat().st_size} bytes)")


if __name__ == "__main__":
    main()
