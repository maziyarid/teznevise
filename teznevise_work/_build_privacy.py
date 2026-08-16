#!/usr/bin/env python3
"""Build privacy.html — privacy policy page with TOC sidebar"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

head = S.head(
    title="حریم خصوصی و امنیت داده‌ها | تزنویسه",
    description="سیاست حریم خصوصی تزنویسه: نحوه جمع‌آوری، استفاده، اشتراک‌گذاری و محافظت از داده‌های شما. حقوق کاربر، کوکی‌ها و اطلاعات تماس برای پرسش‌های حریم خصوصی.",
    og_title="حریم خصوصی و امنیت داده‌ها — تزنویسه",
    og_desc="سیاست کامل حریم خصوصی موسسه پژوهشی تزنویسه؛ جمع‌آوری، استفاده و محافظت از داده‌ها.",
    canonical_path="privacy.html",
    keywords="حریم خصوصی, امنیت داده, سیاست کوکی, حقوق کاربر, محرمانگی اطلاعات تزنویسه",
    schema_type="WebPage",
)

main = '''
    <!-- ==================== PAGE HERO ==================== -->
    <section class="page-hero bg-gradient-primary">
      <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
          <a href="index.html">خانه</a>
          <span class="breadcrumb-sep">/</span>
          <span class="current">حریم خصوصی</span>
        </nav>
        <h1 class="page-hero-title">حریم خصوصی و امنیت داده‌ها</h1>
        <p class="page-hero-desc">
          ما به محرمانگی اطلاعات شما متعهدیم. این صفحه توضیح می‌دهد که چگونه داده‌های شما را
          جمع‌آوری، استفاده و محافظت می‌کنیم.
        </p>
      </div>
    </section>

    <!-- ==================== POLICY CONTENT ==================== -->
    <!-- BACKEND: Static page. Update content via WP editor.
         TOC links anchor to section IDs. Keep section IDs in sync with TOC. -->
    <section class="section">
      <div class="container">
        <div class="grid" style="grid-template-columns:260px 1fr;gap:2.5rem;align-items:start;">

          <!-- TOC Sidebar -->
          <aside class="fade-in" style="position:sticky;top:90px;">
            <div class="toc">
              <div class="toc-title"><svg width="18" height="18"><use href="#icon-article"/></svg> فهرست مطالب</div>
              <div class="toc-list">
                <a href="#collection">۱. جمع‌آوری داده‌ها</a>
                <a href="#usage">۲. استفاده از داده‌ها</a>
                <a href="#sharing">۳. اشتراک‌گذاری داده‌ها</a>
                <a href="#security">۴. امنیت داده‌ها</a>
                <a href="#rights">۵. حقوق کاربر</a>
                <a href="#cookies">۶. کوکی‌ها</a>
                <a href="#contact">۷. تماس با ما</a>
              </div>
            </div>
            <p class="text-muted mt-4" style="font-size:0.875rem;">
              آخرین به‌روزرسانی: ۱۵ مرداد ۱۴۰۵
            </p>
          </aside>

          <!-- Article Content -->
          <div class="article-content fade-in">
            <div class="card bg-surface-2 mb-6" style="border-inline-start:4px solid var(--color-primary);">
              <p class="card-desc" style="display:flex;gap:0.5rem;align-items:flex-start;margin:0;">
                <svg width="20" height="20" style="color:var(--color-primary);flex-shrink:0;margin-top:2px;"><use href="#icon-shield"/></svg>
                موسسه پژوهشی تزنویسه متعهد است که اطلاعات شخصی شما را مطابق با این سیاست و قوانین جمهوری اسلامی ایران محافظت کند.
              </p>
            </div>

            <h2 id="collection">۱. جمع‌آوری داده‌ها</h2>
            <p>ما اطلاعات شخصی شما را تنها در موارد زیر جمع‌آوری می‌کنیم:</p>
            <ul>
              <li>اطلاعاتی که هنگام تکمیل فرم ثبت سفارش یا فرم تماس ارائه می‌دهید (نام، شماره تماس، ایمیل، رشته و مقطع تحصیلی).</li>
              <li>اطلاعات مربوط به پروژه پژوهشی شما که برای انجام خدمت لازم است.</li>
              <li>اطلاعات فنی مانند آدرس IP و نوع مرورگر، که به‌صورت خودکار جمع‌آوری می‌شوند.</li>
            </ul>
            <p>ما هرگز اطلاعات حساس مانند اطلاعات بانکی را در سایت ذخیره نمی‌کنیم. پرداخت‌ها از طریق درگاه‌های امن بانکی انجام می‌شود.</p>

            <h2 id="usage">۲. استفاده از داده‌ها</h2>
            <p>داده‌های جمع‌آوری‌شده برای اهداف زیر به کار گرفته می‌شوند:</p>
            <ul>
              <li>ارائه و انجام خدمات پژوهشی درخواستی شما.</li>
              <li>برقراری ارتباط با شما درباره پروژه، برآورد هزینه و زمان.</li>
              <li>بهبود کیفیت خدمات و تجربه کاربری وب‌سایت.</li>
              <li>ارسال اطلاعیه‌ها و به‌روزرسانی‌های مربوط به پروژه شما (در صورت موافقت).</li>
            </ul>
            <p>ما از اطلاعات شما برای purposes تبلیغاتی بدون رضایت صریح شما استفاده نمی‌کنیم.</p>

            <h2 id="sharing">۳. اشتراک‌گذاری داده‌ها</h2>
            <p>ما اطلاعات شخصی شما را به اشخاص ثالث نمی‌فروشیم. اشتراک‌گذاری داده‌ها تنها در موارد زیر انجام می‌شود:</p>
            <ul>
              <li>اشتراک‌گذاری اطلاعات پروژه با پژوهشگر متخصص مربوطه، صرفاً برای انجام خدمت.</li>
              <li>در صورت الزام قانونی یا درخواست مراجع قضایی ذی‌صلاح.</li>
              <li>با رضایت صریح و کتبی شما.</li>
            </ul>
            <p>تمام پژوهشگران و همکاران ما تحت قرارداد عدم افشا (NDA) ملزم به حفظ محرمانگی اطلاعات شما هستند.</p>

            <h2 id="security">۴. امنیت داده‌ها</h2>
            <p>ما اقدامات فنی و سازمانی مناسب را برای محافظت از داده‌های شما در برابر دسترسی غیرمجاز، افشا یا تغییر اتخاذ کرده‌ایم:</p>
            <ul>
              <li>استفاده از پروتکل رمزنگاری SSL برای انتقال امن داده‌ها.</li>
              <li>ذخیره‌سازی رمزنگاری‌شده اطلاعات حساس در پایگاه داده.</li>
              <li>محدود کردن دسترسی به اطلاعات شخصی به کارکنان مجاز.</li>
              <li>پشتیبان‌گیری منظم و نظارت بر امنیت سیستم‌ها.</li>
            </ul>
            <blockquote>به‌رغم تلاش‌های ما، هیچ روش انتقال یا ذخیره‌سازی الکترونیکی کاملاً امن نیست. ما نمی‌توانیم تضمین مطلق امنیت ارائه دهیم، اما متعهد به بهترین تلاش خود هستیم.</blockquote>

            <h2 id="rights">۵. حقوق کاربر</h2>
            <p>شما به‌عنوان کاربر، حقوق زیر را دارید:</p>
            <ul>
              <li>دسترسی به اطلاعات شخصی خود که در اختیار ما قرار داریم.</li>
              <li>درخواست اصلاح یا حذف اطلاعات شخصی خود.</li>
              <li>اعتراض به پردازش داده‌های شما برای اهداف خاص.</li>
              <li>درخواست دریافت کپی از داده‌های خود به‌صورت قابل انتقال.</li>
            </ul>
            <p>برای اعمال هر یک از این حقوق، با ما از طریق <a href="mailto:teznevisan@gmail.com">teznevisan@gmail.com</a> تماس بگیرید. درخواست شما در کوتاه‌ترین زمان ممکن بررسی و پاسخ داده می‌شود.</p>

            <h2 id="cookies">۶. کوکی‌ها</h2>
            <p>وب‌سایت تزنویسه از کوکی‌ها (Cookies) برای بهبود تجربه کاربری استفاده می‌کند. کوکی‌ها فایل‌های متنی کوچکی هستند که در مرورگر شما ذخیره می‌شوند. انواع کوکی‌های مورد استفاده:</p>
            <ul>
              <li><strong>کوکی‌های ضروری:</strong> برای عملکرد صحیح وب‌سایت ضروری هستند و قابل غیرفعال‌سازی نیستند.</li>
              <li><strong>کوکی‌های ترجیحات:</strong> انتخاب‌های شما مانند تم وب‌سایت را به یاد می‌سپارند.</li>
              <li><strong>کوکی‌های تحلیلی:</strong> به ما کمک می‌کنند بفهمیم بازدیدکنندگان چگونه از سایت استفاده می‌کنند.</li>
            </ul>
            <p>شما می‌توانید کوکی‌ها را از تنظیمات مرورگر خود مدیریت یا غیرفعال کنید. توجه داشته باشید که غیرفعال‌سازی برخی کوکی‌ها ممکن است بر تجربه شما تأثیر بگذارد.</p>

            <h2 id="contact">۷. تماس با ما</h2>
            <p>اگر درباره این سیاست حریم خصوصی یا نحوه برخورد ما با داده‌های شما سوالی دارید، خوشحال می‌شویم پاسخگوی شما باشیم:</p>
            <ul>
              <li>ایمیل: <a href="mailto:teznevisan@gmail.com">teznevisan@gmail.com</a></li>
              <li>تلفن: <a href="tel:+989302822091">۰۹۳۰۲۸۲۲۰۹۱</a></li>
              <li>واتساپ: <a href="https://wa.me/989302822091">گفتگوی آنلاین</a></li>
            </ul>
            <p>ما متعهد هستیم درخواست‌های مربوط به حریم خصوصی را با دقت و شفافیت بررسی کنیم.</p>

            <div class="card bg-surface-2 mt-6">
              <p class="card-desc" style="margin:0;">
                این سیاست ممکن است به‌روزرسانی شود. تغییرات در همین صفحه منتشر خواهد شد. توصیه می‌کنیم این صفحه را به‌طور دوره‌ای بررسی کنید.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="section bg-surface-2">
      <div class="container">
        <div class="cta-section fade-in">
          <h2 class="cta-title">سوالی درباره حریم خصوصی دارید؟</h2>
          <p class="cta-desc">
            تیم ما آماده پاسخ‌گویی به تمام پرسش‌های شما درباره محافظت از داده‌هایتان است.
          </p>
          <div class="hero-actions" style="justify-content:center;">
            <a href="contact.html" class="btn btn-primary btn-lg">
              <svg width="20" height="20"><use href="#icon-phone"/></svg>
              تماس با ما
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
              mobile_active=None,
              bottom_active=None)

with open("/home/user/workspace/teznevise-redesign/privacy.html", "w", encoding="utf-8") as f:
    f.write(page)
print("privacy.html written:", len(page), "chars")
