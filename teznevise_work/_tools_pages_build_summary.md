# Tools Pages Build Summary

Built 2 tools-related HTML pages for the Teznevise website redesign.

## Files created

### 1. `/home/user/workspace/teznevise-redesign/tools.html` (39,862 bytes)
Online tools index page. Contains:
- **Page hero** (emerald gradient) with breadcrumbs: خانه > ابزارهای آنلاین
- Title: "ابزارهای محاسبه آنلاین", description as specified
- **Stats bar**: 18+ tools, 100% free, 0 data storage, 24/7 access
- **Filter tabs** (8 tabs): همه، آمار توصیفی، همبستگی، آزمون‌های فرض، ناپارامتریک، پایایی، حجم نمونه، نمودار
- **Tools grid** with 18 `.tool-card` entries, each linking to `tool-descriptive-statistics.html`
- Client-side filter script (filters cards by `data-category` attribute)
- Features section + CTA section
- Backend comments for tools taxonomy/config rendering

### 2. `/home/user/workspace/teznevise-redesign/tool-descriptive-statistics.html` (51,878 bytes)
Single calculator page. Contains:
- Breadcrumbs: خانه > ابزارهای آنلاین > آمار توصیفی
- Title: "ماشین‌حساب آمار توصیفی"
- **Functional input form**: textarea (accepts comma/newline/space/tab separated, Persian digits auto-normalized)
- **Results table**: count, mean, median, mode, std dev, variance, min, max, range, Q1, Q3, IQR, skewness, kurtosis
- **Copy results** and **Download CSV** buttons (both functional, CSV with UTF-8 BOM for Excel)
- "How to use" section (4 steps)
- Formula/theory section (mean, median, std/variance, quartiles/IQR, skewness/kurtosis)
- Related tools section (4 tool cards)
- **JSON-LD**: WebApplication schema + BreadcrumbList
- Backend comments for calculator logic

## Functional JavaScript calculator
The calculator computes (client-side, no server, no data storage):
- Mean, median, mode (multi-modal support)
- Sample variance (n−1), standard deviation
- Min, max, range
- Q1, Q3, IQR (linear interpolation, type 7 — matches R/SPSS default)
- Skewness (sample-adjusted G1)
- Excess kurtosis (Fisher G2, matches Excel/SPSS)
- Persian/Arabic digit normalization
- Ctrl/Cmd+Enter to compute
- Empty state, error state (minimum 2 numbers)

### Verified output on sample data `[12,15,18,22,25,28,30,30,33,36,38,41,45]`:
- n=13, mean=28.6923, median=30, mode=30
- variance=101.5641, std=10.0779
- min=12, max=45, range=33
- Q1=22, Q3=36, IQR=14
- skewness=−0.1259, kurtosis=−0.8206

## Design system compliance
- Both pages use exact SVG sprite, header, footer, mobile drawer, search overlay, floating elements, cookie banner copied from index.html
- Header "ابزارها" nav-link set as `active`; mobile bottom nav "ابزارها" also active
- RTL (`lang="fa" dir="rtl"`), Vazirmatn font CDN, links to `assets/css/styles.css` and `assets/js/main.js`
- Unique Persian meta titles/descriptions, Open Graph tags, canonical URLs
- Mobile-responsive (calc-grid collapses to single column under 900px)
- Used existing CSS classes: `.page-hero`, `.section`, `.card`, `.tool-card`, `.btn`, `.stat-card`, `.filter-tabs`, `.filter-tab`, `.breadcrumbs`, `.cta-section`, `.form-group`, `.form-input`, `.form-textarea`, `.eyebrow`, `.section-title`, etc.
- Added 2 new custom SVG icons: `#icon-sigma`, `#icon-copy`, `#icon-info` (inline in sprite)

## Backend comments included
- tools.html: `<!-- BACKEND: Render tools from configuration/taxonomy... -->`, filter tabs, stats bar, tools grid loop
- calculator: `<!-- BACKEND: Calculator tool. Input: array of numbers. Output: descriptive stats. Client-side JS computation. No data stored. -->`

## Validation
- tools.html: 18 tool cards, div tags balanced (129/129), sections balanced (5/5)
- calculator: div tags balanced (68/68), sections balanced (6/6)
- Both files contain sprite, header, footer, floating elements, main.js, styles.css references
- No external dependencies beyond existing Vazirmatn CDN
