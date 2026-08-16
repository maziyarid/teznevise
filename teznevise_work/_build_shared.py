#!/usr/bin/env python3
"""Shared template parts for Teznevise pages, extracted from index.html.
Provides head() and chrome() builders so each page is standalone and consistent."""

SRC = "/home/user/workspace/teznevise-redesign/index.html"
with open(SRC, encoding="utf-8") as f:
    html = f.read()

# --- Extract raw blocks ---
sprite_start = html.index('  <!-- SVG Sprite')
sprite_end = html.index('  </svg>', sprite_start) + len('  </svg>')
SPRITE = html[sprite_start:sprite_end]

HEADER_RAW = html[html.index('  <!-- ==================== HEADER ==================== -->'):
                  html.index('  <!-- ==================== MOILE DRAWER')]

MOBILE_DRAWER_RAW = html[html.index('  <!-- ==================== MOILE DRAWER ==================== -->'):
                         html.index('  <!-- ==================== SEARCH OVERLAY')]

SEARCH_OVERLAY_RAW = html[html.index('  <!-- ==================== SEARCH OVERLAY ==================== -->'):
                          html.index('  <main id="main">')]

FOOTER_RAW = html[html.index('  <!-- ==================== FOOTER ==================== -->'):
               html.index('  <!-- ==================== FLOATING ELEMENTS ==================== -->')]

FLOATING_RAW = html[html.index('  <!-- ==================== FLOATING ELEMENTS ==================== -->'):
                    html.index('  <!-- Scripts -->')]

SCRIPTS = """  <!-- Scripts -->
  <script src="assets/js/main.js"></script>
</body>
</html>
"""

# Desktop nav: which href is "active" (only main 6). Pages outside main nav -> none.
DESKTOP_NAV_KEYS = {
    "home": "index.html",
    "thesis": "service-thesis.html",
    "proposal": "service-proposal.html",
    "statistics": "service-statistics.html",
    "tools": "tools.html",
    "blog": "blog.html",
}

# Mobile drawer links keyed by page filename (without active marker).
# Mobile drawer active uses class "mobile-nav-link active".

def _set_active_desktop(active_key):
    """Return header with correct nav-link active. active_key in DESKTOP_NAV_KEYS or None."""
    h = HEADER_RAW
    # First strip all existing ' active' on nav-link.
    h = h.replace(' class="nav-link active"', ' class="nav-link"')
    if active_key and active_key in DESKTOP_NAV_KEYS:
        href = DESKTOP_NAV_KEYS[active_key]
        h = h.replace(f'<a href="{href}" class="nav-link">',
                      f'<a href="{href}" class="nav-link active">')
    return h

def _set_active_mobile(page_file):
    """Return mobile drawer with correct mobile-nav-link active."""
    m = MOBILE_DRAWER_RAW
    m = m.replace(' class="mobile-nav-link active"', ' class="mobile-nav-link"')
    if page_file:
        m = m.replace(f'<a href="{page_file}" class="mobile-nav-link">',
                      f'<a href="{page_file}" class="mobile-nav-link active">')
    return m

def _set_active_bottom(page_file):
    """Return floating block with correct mobile-bottom-nav active."""
    f = FLOATING_RAW
    # Strip the home link's active (only the bottom-nav home is class="active").
    f = f.replace('<a href="index.html" class="active">', '<a href="index.html">', 1)
    if page_file:
        # The FAB (inquiry) keeps its class="fab"; do not mark it active.
        if page_file == "inquiry.html":
            return f
        # Target links (contact, tools, blog) have no class attribute.
        f = f.replace(f'<a href="{page_file}">',
                      f'<a href="{page_file}" class="active">', 1)
    return f

def head(title, description, og_title, og_desc, canonical_path, keywords=None,
         schema_type="WebPage", schema_extra=""):
    kw = ""
    if keywords:
        kw = f'\n  <meta name="keywords" content="{keywords}">'
    extra = schema_extra
    if extra and not extra.startswith(","):
        extra = ", " + extra if extra.strip() else ""
    schema = ""
    if schema_type:
        schema = f'''
  <!-- Structured Data -->
  <script type="application/ld+json">
  {{
    "@context": "https://schema.org",
    "@type": "{schema_type}",
    "name": "{og_title}",
    "description": "{og_desc}",
    "url": "https://teznevise.ir/{canonical_path}"{extra}
  }}
  </script>'''
    return f'''<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{title}</title>
  <meta name="description" content="{description}">{kw}

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:locale" content="fa_IR">
  <meta property="og:title" content="{og_title}">
  <meta property="og:description" content="{og_desc}">
  <meta property="og:image" content="assets/img/logo.jpg">
  <meta property="og:url" content="https://teznevise.ir/{canonical_path}">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{og_title}">
  <meta name="twitter:description" content="{og_desc}">

  <!-- Canonical -->
  <link rel="canonical" href="https://teznevise.ir/{canonical_path}">

  <!-- Favicon -->
  <link rel="icon" type="image/jpeg" href="assets/img/logo.jpg">

  <!-- Fonts: Vazirmatn (Persian-optimized) -->
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="assets/css/styles.css">

  <!-- SVG Icon Sprite -->
  <link rel="preload" href="assets/icons/sprite.svg" as="image" type="image/svg+xml">{schema}
</head>
<body data-pref-theme="">

  <!-- Skip link for accessibility -->
  <a href="#main" class="sr-only" style="position:absolute;top:0;right:0;z-index:999;background:var(--color-primary);color:#fff;padding:1rem;">پرش به محتوای اصلی</a>
'''

def chrome(desktop_active=None, mobile_active=None, bottom_active=None):
    """Build the full page chrome: sprite + header + drawer + search (before <main>)
       and footer + floating + scripts (after </main>)."""
    header = _set_active_desktop(desktop_active)
    drawer = _set_active_mobile(mobile_active)
    floating = _set_active_bottom(bottom_active)
    pre_main = SPRITE + "\n\n" + header + "\n\n" + drawer + "\n\n" + SEARCH_OVERLAY_RAW + "\n\n"
    post_main = "\n\n" + FOOTER_RAW + "\n\n" + floating + "\n\n" + SCRIPTS
    return pre_main, post_main

def page(head_html, main_html, desktop_active=None, mobile_active=None, bottom_active=None):
    pre, post = chrome(desktop_active, mobile_active, bottom_active)
    return head_html + pre + "  <main id=\"main\">\n" + main_html + "\n  </main>" + post

if __name__ == "__main__":
    print("SPRITE chars:", len(SPRITE))
    print("HEADER chars:", len(HEADER_RAW))
    print("MOBILE_DRAWER chars:", len(MOBILE_DRAWER_RAW))
    print("SEARCH chars:", len(SEARCH_OVERLAY_RAW))
    print("FLOATING chars:", len(FLOATING_RAW))
    # quick self-test
    pre, post = chrome(desktop_active=None, mobile_active="about.html", bottom_active=None)
    assert "mobile-nav-link active" in pre
    print("OK")
