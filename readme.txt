=== Teznevise ===
Contributors: maziyarid
Tags: rtl-language-support, custom-logo, custom-menu, featured-images, education, blog, translation-ready
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 1.6.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

RTL-first WordPress theme for Teznevise — thesis, proposal, and statistical analysis consulting.

== Description ==

Teznevise is a professional RTL (Persian) WordPress theme for academic research consulting sites.

Features:

* Homepage sections editable via Customizer
* Page templates: Service, Contact, Privacy, Visual Sitemap
* Manual Font Awesome icon picker on pages
* Category and tag hub templates
* Motion system with prefers-reduced-motion support
* Asset promote + page seeder under Appearance → راه‌اندازی تزنویسه

Author signature: MAZ//ID (Maziyar) — brand system: https://github.com/maziyarid/M-Z

== Installation ==

1. Upload the theme folder to /wp-content/themes/teznevise
2. Activate via Appearance → Themes
3. Appearance → راه‌اندازی تزنویسه → create recommended pages
4. Optionally promote assets from teznevise_work into assets/
5. Appearance → Customize for homepage and contact details
6. Assign menus: Primary, Mobile, Bottom

== Changelog ==

= 1.6.3 =
* Finish remaining shortcode-to-builder mappings (price box/CTA, calculation hub, careers terms, remaining calculators)
* Auto-run batches with has_more; never mark complete on a partial batch
* Load builder-download-catalog hydrator; defensive TEZNEVISE_* constants
* Stop tracking credential-bearing dump files

= 1.6.2 =
* Load auto-run shortcode-to-builder migration on admin
* Footer uses dynamic TEZNEVISE_VERSION constant (no more hardcoded version)
* Aligned style.css / readme.txt / README version metadata with functions.php

= 1.6.1 =
* Seed Flexible Page Builder sections from teznevise_work HTML
* 16-file inventory including post-sample; blog/404 stay native
* Appearance → Teznevise Setup writes builder JSON without overwriting edits

= 1.3.0 =
* screenshot.png theme preview (auto-ensured)
* MAZ//ID code signatures across theme PHP
* readme.txt, CONTRIBUTING, languages stub
* Brand attribution per maziyarid/M-Z

= 1.2.2 =
* Asset resolver skips empty placeholders
* Restored theme JS under assets/

== Credits ==

Design & development: MAZ//ID (Maziyar) — https://maziyarid.com/
Brand identity: https://github.com/maziyarid/M-Z
