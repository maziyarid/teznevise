#!/usr/bin/env python3
"""Build downloads.html — free resource center"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

head = S.head(
    title="دانلود منابع رایگان پژوهشی | تزنویسه",
    description="مرکز منابع رایگان تزنویسه: فرم پروپوزال، پرسشنامه استاندارد، چک‌لیست پایان‌نامه، راهنمای نگارش و کتاب‌های مرجع. دانلود رایگان و بدون نیاز به ثبت‌نام.",
    og_title="مرکز منابع رایگان تزنویسه",
    og_desc="دانلود رایگان فرم پروپوزال، پرسشنامه، چک‌لیست، راهنما و کتاب‌های مرجع پژوهشی.",
    canonical_path="downloads.html",
    keywords="دانلود رایگان, فرم پروپوزال, پرسشنامه, چک لیست پایان نامه, راهنمای نگارش, کتاب پژوهشی",
    schema_type="CollectionPage",
)

main = '''
    <!-- ==================== PAGE HERO ==================== -->
    <section class="page-hero bg-gradient-primary">
      <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
          <a href="index.html">خانه</a>
          <span class="breadcrumb-sep">/</span>
          <span class="current">دانلودها</span>
        </nav>
        <h1 class="page-hero-title">مرکز منابع رایگان تزنویسه</h1>
        <p class="page-hero-desc">
          مجموعه‌ای از قالب‌ها، فرم‌ها، پرسشنامه‌ها و راهنماهای پژوهشی را به‌صورت رایگان دانلود کنید.
          بدون نیاز به ثبت‌نام، در دسترس همه پژوهشگران.
        </p>
      </div>
    </section>

    <!-- ==================== CATEGORIES ==================== -->
    <section class="section">
      <div class="container">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-folder"/></svg> دسته‌بندی</span>
          <h2 class="section-title">دسته‌های منابع</h2>
          <p class="section-desc" style="margin-inline:auto;">
            منابع را بر اساس دسته مورد نیاز خود انتخاب کنید.
          </p>
        </div>

        <div class="grid grid-5">
          <!-- BACKEND: Categories from 'download_cat' taxonomy.
               Each: name, slug, count, icon (meta). -->
          <a href="#downloads" class="card text-center card-hover fade-in" data-cat="proposal">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-proposal"/></svg></div>
            <h3 class="card-title">فرم پروپوزال</h3>
            <p class="card-desc">قالب‌های آماده پروپوزال</p>
          </a>
          <a href="#downloads" class="card text-center card-hover fade-in" data-cat="questionnaire">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-clipboard"/></svg></div>
            <h3 class="card-title">پرسشنامه</h3>
            <p class="card-desc">پرسشنامه‌های استاندارد</p>
          </a>
          <a href="#downloads" class="card text-center card-hover fade-in" data-cat="checklist">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-check-circle"/></svg></div>
            <h3 class="card-title">چک‌لیست</h3>
            <p class="card-desc">چک‌لیست‌های مراحل پژوهش</p>
          </a>
          <a href="#downloads" class="card text-center card-hover fade-in" data-cat="guide">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-article"/></svg></div>
            <h3 class="card-title">راهنما</h3>
            <p class="card-desc">راهنمای نگارش و تحلیل</p>
          </a>
          <a href="#downloads" class="card text-center card-hover fade-in" data-cat="book">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-book"/></svg></div>
            <h3 class="card-title">کتاب</h3>
            <p class="card-desc">کتاب‌های مرجع پژوهشی</p>
          </a>
        </div>
      </div>
    </section>

    <!-- ==================== DOWNLOAD LIST ==================== -->
    <!-- BACKEND: Downloads CPT. Categories: taxonomy. File URL: meta field.
         Each item: title, description, category, file_url, format, downloads_count. -->
    <section class="section bg-surface-2" id="downloads">
      <div class="container">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-download"/></svg> منابع</span>
          <h2 class="section-title">فایل‌های قابل دانلود</h2>
          <p class="section-desc" style="margin-inline:auto;">
            جدیدترین منابع افزودنی به این بخش اضافه می‌شوند.
          </p>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs fade-in" style="justify-content:center;margin-bottom:2.5rem;flex-wrap:wrap;display:flex;gap:0.5rem;">
          <button class="filter-tab active" data-filter="all">همه</button>
          <button class="filter-tab" data-filter="proposal">فرم پروپوزال</button>
          <button class="filter-tab" data-filter="questionnaire">پرسشنامه</button>
          <button class="filter-tab" data-filter="checklist">چک‌لیست</button>
          <button class="filter-tab" data-filter="guide">راهنما</button>
          <button class="filter-tab" data-filter="book">کتاب</button>
        </div>

        <div class="grid grid-2">
          <!-- BACKEND: Loop through downloads -->
          <div class="card card-hover fade-in" data-cat="proposal">
            <div class="flex items-center gap-3" style="margin-bottom:1rem;">
              <div class="card-icon" style="margin:0;"><svg><use href="#icon-proposal"/></svg></div>
              <div>
                <span class="badge">فرم پروپوزال</span>
                <h3 class="card-title" style="margin:0.25rem 0 0;">قالب پروپوزال کارشناسی ارشد</h3>
              </div>
            </div>
            <p class="card-desc">قالب استاندارد پروپوزال کارشناسی ارشد با ساختار کامل فصل‌ها و منابع.</p>
            <div class="flex justify-between items-center mt-4">
              <span class="text-muted" style="font-size:0.875rem;"><svg width="14" height="14" style="vertical-align:middle;"><use href="#icon-download"/></svg> فرمت DOCX · ۱.۲MB</span>
              <a href="#" class="btn btn-primary btn-sm">
                <svg width="16" height="16"><use href="#icon-download"/></svg>
                دانلود
              </a>
            </div>
          </div>

          <div class="card card-hover fade-in" data-cat="proposal">
            <div class="flex items-center gap-3" style="margin-bottom:1rem;">
              <div class="card-icon" style="margin:0;"><svg><use href="#icon-proposal"/></svg></div>
              <div>
                <span class="badge">فرم پروپوزال</span>
                <h3 class="card-title" style="margin:0.25rem 0 0;">قالب پروپوزال دکتری</h3>
              </div>
            </div>
            <p class="card-desc">قالب پروپوزال مقطع دکتری با ساختار پیشرفته و بخش نوآوری پژوهش.</p>
            <div class="flex justify-between items-center mt-4">
              <span class="text-muted" style="font-size:0.875rem;"><svg width="14" height="14" style="vertical-align:middle;"><use href="#icon-download"/></svg> فرمت DOCX · ۱.۵MB</span>
              <a href="#" class="btn btn-primary btn-sm">
                <svg width="16" height="16"><use href="#icon-download"/></svg>
                دانلود
              </a>
            </div>
          </div>

          <div class="card card-hover fade-in" data-cat="questionnaire">
            <div class="flex items-center gap-3" style="margin-bottom:1rem;">
              <div class="card-icon" style="margin:0;"><svg><use href="#icon-clipboard"/></svg></div>
              <div>
                <span class="badge">پرسشنامه</span>
                <h3 class="card-title" style="margin:0.25rem 0 0;">پرسشنامه استاندارد رضایت شغلی</h3>
              </div>
            </div>
            <p class="card-desc">پرسشنامه استاندارد رضایت شغلی با روایی و پایایی تأییدشده و راهنمای نمره‌گذاری.</p>
            <div class="flex justify-between items-center mt-4">
              <span class="text-muted" style="font-size:0.875rem;"><svg width="14" height="14" style="vertical-align:middle;"><use href="#icon-download"/></svg> فرمت PDF · ۸۰۰KB</span>
              <a href="#" class="btn btn-primary btn-sm">
                <svg width="16" height="16"><use href="#icon-download"/></svg>
                دانلود
              </a>
            </div>
          </div>

          <div class="card card-hover fade-in" data-cat="checklist">
            <div class="flex items-center gap-3" style="margin-bottom:1rem;">
              <div class="card-icon" style="margin:0;"><svg><use href="#icon-check-circle"/></svg></div>
              <div>
                <span class="badge">چک‌لیست</span>
                <h3 class="card-title" style="margin:0.25rem 0 0;">چک‌لیست مراحل پایان‌نامه</h3>
              </div>
            </div>
            <p class="card-desc">چک‌لیست کامل مراحل انجام پایان‌نامه از انتخاب موضوع تا دفاع نهایی.</p>
            <div class="flex justify-between items-center mt-4">
              <span class="text-muted" style="font-size:0.875rem;"><svg width="14" height="14" style="vertical-align:middle;"><use href="#icon-download"/></svg> فرمت PDF · ۶۰۰KB</span>
              <a href="#" class="btn btn-primary btn-sm">
                <svg width="16" height="16"><use href="#icon-download"/></svg>
                دانلود
              </a>
            </div>
          </div>

          <div class="card card-hover fade-in" data-cat="guide">
            <div class="flex items-center gap-3" style="margin-bottom:1rem;">
              <div class="card-icon" style="margin:0;"><svg><use href="#icon-article"/></svg></div>
              <div>
                <span class="badge">راهنما</span>
                <h3 class="card-title" style="margin:0.25rem 0 0;">راهنمای تحلیل آماری با SPSS</h3>
              </div>
            </div>
            <p class="card-desc">راهنمای گام‌به‌گام تحلیل‌های آماری پرکاربرد در SPSS با مثال و تفسیر نتایج.</p>
            <div class="flex justify-between items-center mt-4">
              <span class="text-muted" style="font-size:0.875rem;"><svg width="14" height="14" style="vertical-align:middle;"><use href="#icon-download"/></svg> فرمت PDF · ۲.۸MB</span>
              <a href="#" class="btn btn-primary btn-sm">
                <svg width="16" height="16"><use href="#icon-download"/></svg>
                دانلود
              </a>
            </div>
          </div>

          <div class="card card-hover fade-in" data-cat="book">
            <div class="flex items-center gap-3" style="margin-bottom:1rem;">
              <div class="card-icon" style="margin:0;"><svg><use href="#icon-book"/></svg></div>
              <div>
                <span class="badge">کتاب</span>
                <h3 class="card-title" style="margin:0.25rem 0 0;">کتاب روش تحقیق در علوم رفتاری</h3>
              </div>
            </div>
            <p class="card-desc">کتاب مرجع روش تحقیق برای آشنایی با مبانی پژوهش علمی و روش‌های کمی و کیفی.</p>
            <div class="flex justify-between items-center mt-4">
              <span class="text-muted" style="font-size:0.875rem;"><svg width="14" height="14" style="vertical-align:middle;"><use href="#icon-download"/></svg> فرمت PDF · ۴.۲MB</span>
              <a href="#" class="btn btn-primary btn-sm">
                <svg width="16" height="16"><use href="#icon-download"/></svg>
                دانلود
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== REQUEST RESOURCE CTA ==================== -->
    <section class="section">
      <div class="container">
        <div class="cta-section fade-in">
          <h2 class="cta-title">منبع مورد نیازت را پیدا نکردی؟</h2>
          <p class="cta-desc">
            اگر به منبع خاصی نیاز داری که در اینجا نیست، درخواست خود را برای ما بفرست تا آن را تهیه کنیم.
          </p>
          <div class="hero-actions" style="justify-content:center;">
            <a href="contact.html" class="btn btn-primary btn-lg">
              <svg width="20" height="20"><use href="#icon-mail"/></svg>
              درخواست منبع
            </a>
            <a href="https://wa.me/989302822091" class="btn btn-outline btn-lg">
              <svg width="20" height="20"><use href="#icon-whatsapp"/></svg>
              گفتگو در واتساپ
            </a>
          </div>
        </div>
      </div>
    </section>
'''

page = S.page(head, main,
              desktop_active=None,
              mobile_active="downloads.html",
              bottom_active=None)

with open("/home/user/workspace/teznevise-redesign/downloads.html", "w", encoding="utf-8") as f:
    f.write(page)
print("downloads.html written:", len(page), "chars")
