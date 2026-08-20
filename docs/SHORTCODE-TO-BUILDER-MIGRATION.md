# Shortcode to Builder Migration – Complete Data Extraction & Analysis

**Repository:** `maziyarid/teznevise`  
**Status:** Implemented in theme 1.6.6  
**Created:** August 19, 2026  
**Author:** Maziyar ID

---

## Executive Summary

This document captures the complete data extraction from the uploaded database export (`sep_posts.sql`) and WPCode snippets (`wpcode-snippets-export-2026-08-18.json`). It identifies **29 custom fields**, **30+ shortcode patterns**, **3 custom post types**, and **3 custom taxonomies** that need to be mapped to the Flexible Page Builder.

**v1.6.6 implementation**
- Generator: `scripts/extract-shortcode-pages.py`
- Data: `inc/extracted-page-fields.json` (published pages only)
- Writer: `inc/extracted-pages.php` → `_teznevise_builder_sections` + `_teznevise_*`
- Trigger: Appearance → Teznevise Setup, or auto-run migrator v1.2.0
- Auto-run is provenance-safe: it fills empty pages and replaces default-seed / matching extracted hashes, never administrator-owned builder JSON
- Manual provenance is honored on both the extracted-fields path and the later shortcode-to-builder candidate loop, even when builder JSON is `[]`
- Non-builder `_teznevise_*` metabox/REST writes stamp `manual` provenance
- Force-replace is an explicit Setup checkbox
- Mixed interactive shortcodes (`tz_careers_terms`, `tz_join_form`, calculators, Gravity Forms) still render beside builder sections
- Privacy/cookie templates keep legal copy in `post_content`; builder only stores hero/CTA
- **Never updates `wp_posts` rows** (no slug/title/content/status change)
- **Never touches `post_type=post`**


**Total Scope**
- 100–200 pages with shortcodes (from database)
- 15 HTML files in `teznevise_work/`
- 29 custom meta fields across all post types
- 30+ shortcode patterns to migrate

---

## Complete Custom Fields Inventory

### Download System (Post Type: `download`)
Source: Snippet #745

```php
register_post_type('download', [
    'labels' => [
        'name' => 'دانلودها',
        'singular_name' => 'دانلود',
        'add_new' => 'افزودن دانلود',
        'add_new_item' => 'افزودن دانلود جدید',
        'edit_item' => 'ویرایش دانلود',
    ],
    'public' => true,
    'has_archive' => 'download',
    'rewrite' => ['slug' => 'download', 'with_front' => false],
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments'],
    'menu_position' => 6,
    'menu_icon' => 'dashicons-download',
    'show_in_rest' => true
]);

register_taxonomy('download_category', 'download', [
    'labels' => ['name' => 'دسته‌بندی دانلودها'],
    'hierarchical' => true
]);

register_taxonomy('download_tag', 'download', [
    'labels' => ['name' => 'برچسب دانلودها'],
    'hierarchical' => false
]);
```

| Meta Key | Type | Persian Label | Description |
|----------|------|---------------|-------------|
| `_teznevise_download_links` | array | لینک‌های دانلود | Multiple download URLs |
| `_teznevise_download_count` | integer | تعداد دانلود | Download counter |
| `_teznevise_version` | string | نسخه | Software version |
| `_teznevise_license` | string | مجوز | License type |
| `_teznevise_lang` | string | زبان | Language |
| `_teznevise_source` | string | منبع | Source/author |

### Price Calculator System (Post Type: `tz_service`)
Source: Snippet #761

| Meta Key | Type | Persian Label | Description |
|----------|------|---------------|-------------|
| `_tz_price_min` | float | حداقل قیمت | Minimum price |
| `_tz_price_max` | float | حداکثر قیمت | Maximum price |
| `_tz_unit` | string | واحد | Unit of measure |
| `_tz_duration` | string | مدت زمان | Duration |
| `_tz_desc` | string | توضیحات | Description |
| `_tz_note` | string | نکته | Additional note |
| `_tz_icon` | string | آیکون | Icon class |
| `_tz_factors` | array | عوامل | Pricing factors |

**Shortcodes:** `[tz_price_box]`, `[tz_price_cta]`

### Case Studies System (Post Type: `case_study`)
Source: Snippet #772

| Meta Key | Type | Persian Label | Description |
|----------|------|---------------|-------------|
| `_tz_cs_client` | string | مشتری | Client name |
| `_tz_cs_field` | string | حوزه | Field/industry |
| `_tz_cs_region` | string | منطقه | Geographic region |
| `_tz_cs_duration` | string | مدت زمان | Project duration |
| `_tz_cs_degree` | string | درجه | Academic degree |
| `_tz_cs_service` | string | خدمت | Service type |
| `_tz_cs_challenge` | string | چالش | Challenge faced |
| `_tz_cs_solution` | string | راهکار | Solution provided |
| `_tz_cs_result` | string | نتیجه | Outcome |
| `_tz_cs_quote` | string | نقل قول | Client testimonial |
| `_tz_cs_metrics` | string | معیارها | Metrics/results |
| `_tz_cs_tools` | string | ابزارها | Tools used |
| `_tz_cs_icon` | string | آیکون | Icon class |

**Taxonomy:** `case_category` – دسته‌بندی مطالعات موردی

---

## Shortcode Inventory

### Elementor / UX Builder (5 patterns)
| Shortcode | Parameters | Priority |
|-----------|------------|----------|
| `[row]` | span, class, style | HIGH |
| `[col]` | span, class, style | HIGH |
| `[title]` | size, class, style | HIGH |
| `[ux_text]` | class, style | HIGH |
| `[ux_html]` | class, style | HIGH |

### Custom Teznevise Core
| Shortcode | Parameters | Post Type | Priority |
|-----------|------------|-----------|----------|
| `[tz_home]` | none | N/A | HIGH |
| `[teznevise_download_category]` | slug, count | download | HIGH |
| `[tz_price_box]` | id, title | tz_service | MEDIUM |
| `[tz_price_cta]` | id, text | tz_service | MEDIUM |
| `[tz_calculation_hub]` | none | N/A | MEDIUM |
| `[tz_careers_terms]` | none | N/A | LOW |

### Calculator Shortcodes (20)
`[tz_spearman]`, `[tz_ttest]`, `[tz_anova]`, `[tz_chi_square]`, `[tz_regression]`, `[tz_cohens_kappa]`, `[tz_mann_whitney]`, `[tz_wilcoxon]`, `[tz_kruskal_wallis]`, `[tz_icc]`, `[tz_kr20]`, `[tz_goodness_of_fit]`, `[tz_descriptive]`, `[content-validity-calculator]`, `[power-analysis-calculator]`, `[sample-size-calculator]`, `[cronbach-alpha-calculator]`, `[pearson-correlation-calculator]`

### Gravity Forms
| Shortcode | Parameters | Priority |
|-----------|------------|----------|
| `[gravityform]` | id, title, description, ajax | HIGH |

---

## Builder Section Type Mapping

| Builder Type | Maps From | Notes |
|--------------|-----------|-------|
| `hero` | `[title]`, `[ux_text]` | First occurrence on page |
| `software_catalog` | `[teznevise_download_category]` | Uses `download_category` taxonomy |
| `challenges` | `[row][col][ux_text]` | Multi-item challenge sections |
| `service_cards` | `[row][col]` | Service / feature cards |
| `feature_list` | `[ux_text]` with icons | Parse icon classes |
| `process_steps` | Sequential `[row][col]` | Timelines |
| `cta_band` | `[gravityform]`, `[ux_text]` | Preserve form shortcode |

**Storage key:** `_teznevise_builder_sections` (JSON array, `JSON_UNESCAPED_UNICODE`)

---

## Migration Roadmap (14 Days)

| Phase | Days | Tasks | Deliverables |
|-------|------|-------|--------------|
| 1. Data Extraction | 1–3 | Extract pages, fields, shortcodes | CSV exports, this mapping document |
| 2. Script Development | 4–7 | PHP migration script | Tested script + validation |
| 3. Testing | 8–10 | Staging + UAT | Test reports, approval |
| 4. Production | 11–12 | Batch migration | Migrated database |
| 5. Post-Migration | 13–14 | Cleanup, docs, training | User guides |

### Page Priority

**HIGH (Batch 1 – Week 1)**  
Home, About, Downloads, Contact, 4 Service pages, Tools

**MEDIUM (Batch 2 – Week 2)**  
Team, Privacy, Blog, Tool: Descriptive Statistics

**LOW (Batch 3)**  
404, Inquiry

**15 HTML files** in `teznevise_work/` are fully mapped to the same builder sections.

---

## Database Extraction Queries

```sql
-- 1. All pages with shortcodes
SELECT p.ID, p.post_title, p.post_name, p.post_content, p.guid
FROM wp_posts p
WHERE p.post_type = 'page'
  AND p.post_status = 'publish'
  AND (
    p.post_content LIKE '%[row%'
    OR p.post_content LIKE '%[col%'
    OR p.post_content LIKE '%[title%'
    OR p.post_content LIKE '%[ux_text%'
    OR p.post_content LIKE '%[ux_html%'
    OR p.post_content LIKE '%[tz_home%'
    OR p.post_content LIKE '%[teznevise_download_category%'
    OR p.post_content LIKE '%[gravityform%'
  )
ORDER BY p.post_title;

-- 2. All custom field data
SELECT p.ID, p.post_title, p.post_type, pm.meta_key, pm.meta_value
FROM wp_posts p
JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE pm.meta_key LIKE '_teznevise_%'
   OR pm.meta_key LIKE '_tz_%'
ORDER BY p.post_type, p.post_title, pm.meta_key;

-- 3. Download posts with custom fields
SELECT p.ID, p.post_title, p.post_name, pm.meta_key, pm.meta_value
FROM wp_posts p
JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE p.post_type = 'download'
  AND pm.meta_key LIKE '_teznevise_%'
ORDER BY p.ID, pm.meta_key;
```

---

## PHP Migration Script Template

Location (suggested): `inc/migration/shortcode-to-builder-migrator.php`

```php
<?php
/**
 * Shortcode to Builder Migration Script
 *
 * Usage:
 *   php inc/migration/shortcode-to-builder-migrator.php [--dry-run] [--limit=N]
 */

class Teznevise_Shortcode_Migrator {
    private $builder_key = '_teznevise_builder_sections';
    private $dry_run = false;
    private $limit = 0;

    public function __construct($args) {
        $this->dry_run = isset($args['dry-run']);
        $this->limit   = isset($args['limit']) ? (int) $args['limit'] : 0;
    }

    public function run() {
        $pages = $this->get_pages_with_shortcodes();
        if ($this->limit > 0) {
            $pages = array_slice($pages, 0, $this->limit);
        }

        foreach ($pages as $page) {
            $this->log("Processing: {$page->post_title} (ID: {$page->ID})");
            $sections = $this->parse_content($page->post_content);

            if (!empty($sections)) {
                $json = json_encode($sections, JSON_UNESCAPED_UNICODE);
                if (!$this->dry_run) {
                    update_post_meta($page->ID, $this->builder_key, $json);
                    $this->log("  ✓ Saved builder sections");
                } else {
                    $this->log("  [DRY RUN] Would save builder sections");
                }
            }
        }
    }

    private function get_pages_with_shortcodes() {
        global $wpdb;
        $shortcodes = ['row', 'col', 'title', 'ux_text', 'ux_html',
                       'tz_home', 'teznevise_download_category', 'gravityform'];

        $sql = "SELECT ID, post_title, post_content FROM {$wpdb->posts}
                WHERE post_type = 'page' AND post_status = 'publish'";

        foreach ($shortcodes as $sc) {
            $sql .= " AND (post_content LIKE '%[{$sc}%' OR post_content LIKE '%[{$sc} %')";
        }

        return $wpdb->get_results($sql);
    }

    private function parse_content($content) {
        $sections = [];

        // Gravity Forms → cta_band
        if (preg_match_all('/\\[gravityform\\s+id=\\"(\\d+)\\"/', $content, $matches)) {
            foreach ($matches[1] as $form_id) {
                $sections[] = $this->create_cta_with_form($form_id);
            }
        }

        // Download Category → software_catalog
        if (preg_match_all('/\\[teznevise_download_category\\s+slug=\\"([^\\"]+)\\"/', $content, $matches)) {
            foreach ($matches[1] as $slug) {
                $sections[] = $this->create_software_catalog($slug);
            }
        }

        // Elementor / title / ux_text → hero, service_cards, etc.
        $sections = array_merge($sections, $this->parse_elementor($content));

        // tz_home special case
        if (strpos($content, '[tz_home]') !== false) {
            $sections = array_merge($sections, $this->parse_tz_home());
        }

        return $sections;
    }

    private function create_cta_with_form($form_id) {
        return [
            'type'          => 'cta_band',
            'title'         => 'فرم تماس',
            'description'   => 'لطفاً فرم زیر را تکمیل کنید',
            'button_text'   => 'ارسال پیام',
            'button_link'   => '',
            'form_shortcode'=> "[gravityform id=\"{$form_id}\" title=\"true\"]"
        ];
    }

    private function create_software_catalog($slug) {
        $term = get_term_by('slug', $slug, 'download_category');
        return [
            'type'            => 'software_catalog',
            'title'           => $term ? $term->name : 'دانلودها',
            'category_slug'   => $slug,
            'items_per_page'  => 12,
            'show_search'     => true,
            'show_categories' => true
        ];
    }

    private function parse_elementor($content) {
        $sections = [];
        preg_match_all('/\\[title\\]([^\\[]+)\\[\\/title\\]/', $content, $titles);
        preg_match_all('/\\[ux_text\\]([^\\[]+)\\[\\/ux_text\\]/', $content, $texts);

        foreach ($titles[1] as $i => $title) {
            $text = $texts[1][$i] ?? '';
            $sections[] = [
                'type'     => 'hero',
                'title'    => trim(strip_tags($title)),
                'subtitle' => trim(strip_tags($text))
            ];
        }
        return $sections;
    }

    private function parse_tz_home() {
        return [
            [
                'type'     => 'hero',
                'title'    => 'به تزنویسه خوش آمدید',
                'subtitle' => 'سیستم جامع تحلیل آماری'
            ],
            [
                'type'  => 'service_cards',
                'title' => 'خدمات ما',
                'cards' => [
                    ['icon' => 'fa-chart-bar', 'title' => 'تحلیل آماری', 'excerpt' => '...'],
                    ['icon' => 'fa-database',  'title' => 'بانک اطلاعات', 'excerpt' => '...']
                ]
            ]
        ];
    }

    private function log($message) {
        echo date('Y-m-d H:i:s') . ' - ' . $message . "\\n";
    }
}

// CLI entry
$migrator = new Teznevise_Shortcode_Migrator($argv ?? []);
$migrator->run();
```

---

## Validation Checklist

**Pre-Migration**
- [x] Database backup available (`sep_posts.sql`)
- [x] 29 custom fields documented
- [x] 30+ shortcodes identified
- [ ] Staging environment ready
- [ ] Rollback procedure documented

**During Migration**
- [ ] Script tested on ≥5 sample pages
- [ ] Persian / UTF-8 encoding verified
- [ ] Custom fields preserved
- [ ] Error handling + logging enabled

**Post-Migration**
- [ ] All pages render correctly
- [ ] Mobile responsiveness maintained
- [ ] Forms submit properly
- [ ] Download system functional
- [ ] UAT passed

---

## Notes on Raw Data Files

The original large exports (`sep_posts.sql` ≈ 10 MB and `wpcode-snippets-export-2026-08-18.json` ≈ 1.5 MB) should be stored outside the repository or under a dedicated `migration-data/` folder (git-lfs recommended if committed). This document contains the complete extracted inventory and the migration plan derived from those files.

---

## Next Actions

1. Review and approve this analysis.
2. Import `sep_posts.sql` into a local/staging database and run the extraction queries.
3. Implement the production migration script based on the template above.
4. Test on staging, then run batched production migration.
5. Optionally clear legacy shortcodes from `post_content` after verification.

**Estimated effort:** 2–4 hours for the full page migration (batched) + 1–2 hours staging verification.
