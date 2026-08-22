#!/usr/bin/env python3
"""Build the canonical Teznevise CSS/JS bundles from maintained sources.

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
    """Concatenate in source order. Do NOT wrap in @layer.

    CSS cascade layers invert !important (earlier layer wins), which made
    layout-refinements beat react-parity and turned the sticky header
    transparent. File order is the contract: later files win.
    """
    chunks = []
    for name in files:
        body = read_css(name)
        open_n = body.count("{")
        close_n = body.count("}")
        if open_n != close_n:
            raise SystemExit(f"{name}: brace mismatch {{ {open_n} }} {close_n}")
        chunks.append(f"/* === {name} === */\n")
        chunks.append(body)
        chunks.append("\n")
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
/* ===== 1.8.6 unlayered last-wins (must stay OUT of @layer) ===== */
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

.site-header-new,
.site-header.site-header-new,
body.tz-react-shell .site-header-new {
  background: #fff !important;
  isolation: isolate;
}
.announce,
.announcement {
  background: linear-gradient(135deg, rgba(247, 252, 250, 0.98), rgba(232, 245, 241, 0.96)) !important;
  color: #38544b !important;
}

@media (min-width: 1051px) {
  .main-nav {
    height: 72px !important;
    min-height: 72px !important;
    gap: 8px !important;
    overflow: visible !important;
  }
  .main-nav .nav-links > li > a,
  .main-nav .nav-links > li > .nav-link {
    font-size: 13px !important;
    padding: 8px 8px !important;
    white-space: nowrap !important;
  }
}

.hero-visual {
  position: relative !important;
  min-height: 520px !important;
  overflow: visible !important;
}
.hero-network {
  z-index: 2;
}
.hero-order-button {
  width: 176px !important;
  height: 176px !important;
  z-index: 4 !important;
}
.orbit-tag {
  z-index: 5 !important;
  background: rgba(255, 255, 255, 0.96) !important;
}
.ink-blot {
  opacity: 0.42 !important;
  z-index: 0 !important;
}

.bottom-nav {
  --tz-bottom-nav-cols: 4;
}
.bottom-nav[data-nav-count="2"] { --tz-bottom-nav-cols: 2; }
.bottom-nav[data-nav-count="3"] { --tz-bottom-nav-cols: 3; }
.bottom-nav[data-nav-count="5"] { --tz-bottom-nav-cols: 5; }

@media (max-width: 768px) {
  html { overflow-x: clip; }
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
  .hero-visual { min-height: 420px !important; }
  .hero-order-button { width: 148px !important; height: 148px !important; }
}

@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* ===== 1.8.7 last-wins: header icons + post columns ===== */
.fa-solid, .fas {
  font-family: "Font Awesome 7 Free" !important;
  font-weight: 900 !important;
}
.fa-regular, .far {
  font-family: "Font Awesome 7 Free" !important;
  font-weight: 400 !important;
}
.fa-brands, .fab {
  font-family: "Font Awesome 7 Brands" !important;
  font-weight: 400 !important;
}

@media (min-width: 1051px) {
  .main-nav .nav-dropdown-toggle {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
  }
  .main-nav .nav-links > li > a > .nav-item-icon,
  .main-nav .nav-links > li > .nav-link > .nav-item-icon {
    display: none !important;
  }
  .main-nav .nav-links > li > a,
  .main-nav .nav-links > li > .nav-link {
    font-size: 13.5px !important;
    padding: 8px 10px !important;
    white-space: nowrap !important;
  }
}

.blog-post__layout {
  display: grid !important;
  grid-template-columns: minmax(0, 1fr) minmax(240px, 300px) !important;
  grid-template-areas: "content aside" !important;
  gap: clamp(28px, 3vw, 44px) !important;
  align-items: start !important;
}
.blog-post__content { grid-area: content !important; min-width: 0; }
.blog-post__aside { grid-area: aside !important; }
.blog-post__toc { position: static !important; }
@media (max-width: 900px) {
  .blog-post__layout {
    grid-template-columns: 1fr !important;
    grid-template-areas: "content" "aside" !important;
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

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.tz-interactive-page-content table, .tool-body table, [class*="tz"] table').forEach(function (table) {
    if (table.querySelector('caption')) return;
    var caption = document.createElement('caption');
    caption.className = 'screen-reader-text';
    caption.textContent = 'نتایج محاسبه';
    table.insertBefore(caption, table.firstChild);
  });
  document.querySelectorAll('.tz-interactive-page-content, .tool-body').forEach(function (box) {
    if (!box.hasAttribute('aria-live')) {
      box.setAttribute('aria-live', 'polite');
    }
  });
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
