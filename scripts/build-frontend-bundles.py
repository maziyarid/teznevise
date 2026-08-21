#!/usr/bin/env python3
"""Concatenate the 1.8.4 CSS/JS cascade into layered bundles.

Source files stay in assets/css/ so we can rebuild. Only the bundled
handles (tokens, components, pages, chrome + chrome.js) are enqueued.
"""
from __future__ import annotations

from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSS = ROOT / "assets" / "css"
JS = ROOT / "assets" / "js"

BANNER = """\
/**
 * AUTO-GENERATED — do not edit by hand.
 * Rebuild: python3 scripts/build-frontend-bundles.py
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * @package Teznevise
 */

"""


def read_css(name: str) -> str:
    text = (CSS / name).read_text(encoding="utf-8")
    text = text.replace("@charset \"UTF-8\";", "")
    text = text.replace("@charset 'UTF-8';", "")
    return text.strip() + "\n"


def wrap_layer(layer: str, files: list[str]) -> str:
    chunks = [f"@layer {layer} {{\n"]
    for name in files:
        body = read_css(name)
        open_n = body.count("{")
        close_n = body.count("}")
        if open_n != close_n:
            raise SystemExit(f"{name}: brace mismatch {{ {open_n} }} {close_n}")
        chunks.append(f"  /* === {name} === */\n")
        chunks.append(body)
        chunks.append("\n")
    chunks.append("}\n")
    return "".join(chunks)


def write(path: Path, content: str) -> None:
    path.write_text(content, encoding="utf-8")
    kb = path.stat().st_size / 1024
    print(f"wrote {path.relative_to(ROOT)} ({kb:.1f} KB)")


def build_css() -> None:
    components = BANNER + wrap_layer(
        "tez-components",
        [
            "redesign.css",
            "layout-refinements.css",
            "motion.css",
            "batch-fixes.css",
            "ui-round2.css",
            "site-polish.css",
            "builder-frontend.css",
            "wp-compat.css",
        ],
    )
    pages = BANNER + wrap_layer(
        "tez-pages",
        [
            "blog.css",
            "page-extras.css",
        ],
    )
    chrome = BANNER
    chrome += wrap_layer(
        "tez-chrome",
        [
            "header-form.css",
            "header-fix.css",
            "nav-touch.css",
            "nav-dropdown.css",
            "product-1.7.css",
            "mobile-fixes.css",
        ],
    )
    chrome += "\n"
    chrome += wrap_layer(
        "tez-fixes",
        [
            "react-parity.css",
            "react-loader.css",
        ],
    )
    chrome += """
/* ===== 1.8.5 last-wins: bottom nav height + skip-link + overflow ===== */
@layer tez-fixes {
  .skip-link,
  .skip-link.screen-reader-text,
  a.skip-link {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
  }

  .bottom-nav {
    --tz-bottom-nav-cols: 4;
  }
  .bottom-nav[data-nav-count="2"] { --tz-bottom-nav-cols: 2; }
  .bottom-nav[data-nav-count="3"] { --tz-bottom-nav-cols: 3; }
  .bottom-nav[data-nav-count="5"] { --tz-bottom-nav-cols: 5; }

  @media (max-width: 768px) {
    html {
      overflow-x: clip;
    }
    body,
    body.tz-react-shell {
      padding-bottom: calc(var(--tz-bottom-nav-height, 72px) + env(safe-area-inset-bottom, 0px)) !important;
    }
    .bottom-nav {
      display: grid !important;
      grid-template-columns: repeat(var(--tz-bottom-nav-cols, 4), minmax(64px, 1fr)) !important;
      align-items: stretch !important;
      position: fixed !important;
      inset-inline: 0 !important;
      bottom: 0 !important;
      z-index: 1100 !important;
      background: rgba(255, 255, 255, 0.97) !important;
      backdrop-filter: blur(16px);
      border-top: 1px solid rgba(20, 93, 74, 0.1) !important;
      padding: 6px 4px calc(8px + env(safe-area-inset-bottom, 0px)) !important;
      gap: 0 !important;
      box-shadow: 0 -8px 28px rgba(9, 40, 32, 0.06);
    }
    .bottom-nav-item,
    .bottom-nav a {
      min-width: 0 !important;
      flex: 1 1 0 !important;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    html { scroll-behavior: auto; }
    *, *::before, *::after {
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.01ms !important;
    }
  }
}
"""
    write(CSS / "components.css", components)
    write(CSS / "pages.css", pages)
    write(CSS / "chrome.css", chrome)


def strip_main_search(src: str) -> str:
    """Drop the duplicate search-overlay block; product-1.7.js owns it."""
    start = src.find("  // Search overlay functionality")
    end = src.find("  // FAQ accordion")
    if start == -1 or end == -1 or end <= start:
        return src
    return src[:start] + "  /* search overlay: owned by product-1.7 block in this bundle */\n\n" + src[end:]


def slim_react_loader(src: str) -> str:
    """Keep lang/dir/shell class; product-1.7 owns the search trap."""
    return """/**
 * Front-end boot that matches the React SiteShell loader.
 * Search overlay behaviour lives in the product-1.7 block of chrome.js.
 */
document.addEventListener('DOMContentLoaded', function () {
  var root = document.documentElement;
  if (!root.getAttribute('lang')) {
    root.setAttribute('lang', 'fa');
  }
  if (!root.getAttribute('dir')) {
    root.setAttribute('dir', 'rtl');
  }
  document.body.classList.add('tz-react-shell');
});
"""


def build_js() -> None:
    redesign = (JS / "redesign.js").read_text(encoding="utf-8")
    main = strip_main_search((JS / "main.js").read_text(encoding="utf-8"))
    nav_touch = (JS / "nav-touch.js").read_text(encoding="utf-8")
    product = (JS / "product-1.7.js").read_text(encoding="utf-8")
    dropdown = (JS / "nav-dropdown.js").read_text(encoding="utf-8")
    loader = slim_react_loader((JS / "react-loader.js").read_text(encoding="utf-8"))
    out = (
        BANNER
        + "/* ===== redesign.js ===== */\n"
        + redesign.rstrip()
        + "\n\n/* ===== main.js (search overlay removed) ===== */\n"
        + main.rstrip()
        + "\n\n/* ===== nav-touch.js ===== */\n"
        + nav_touch.rstrip()
        + "\n\n/* ===== product-1.7.js ===== */\n"
        + product.rstrip()
        + "\n\n/* ===== nav-dropdown.js ===== */\n"
        + dropdown.rstrip()
        + "\n\n/* ===== react-loader.js ===== */\n"
        + loader.rstrip()
        + "\n"
    )
    write(JS / "chrome.js", out)


if __name__ == "__main__":
    build_css()
    build_js()
    print("ok")
