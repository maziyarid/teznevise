#!/usr/bin/env python3
"""Build about.html"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

head = S.head(
    title="درباره تزنویسه | موسسه پژوهشی بین‌المللی پایان‌نامه و تحلیل آماری",
    description="تزنویسه یک اکوسیستم علمی جهانی با پژوهشگرانی از بیش از ۲۰ کشور است. با ما آشنا شوید — داستان، تیم و تعهد ما به پژوهش علمی و محرمانگی.",
    og_title="درباره تزنویسه — اکوسیستم پژوهشی جهانی",
    og_desc="تزنویسه؛ موسسه پژوهشی با پژوهشگرانی از ۲۰+ کشور. مشاوره تخصصی پایان‌نامه، پروپوزال و تحلیل آماری.",
    canonical_path="about.html",
    keywords="درباره تزنویسه, موسسه پژوهشی, تیم پژوهشگران, تاریخچه تزنویسه, اکوسیستم علمی",
    schema_type="AboutPage",
)

main = '''
    <!-- ==================== PAGE HERO ==================== -->
    <section class="page-hero bg-gradient-primary">
      <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
          <a href="index.html">خانه</a>
          <span class="breadcrumb-sep">/</span>
          <span class="current">درباره ما</span>
        </nav>
        <h1 class="page-hero-title">پژوهش را به سبکی متفاوت تجربه کنید!</h1>
        <p class="page-hero-desc">
          تزنویسه یک اکوسیستم علمی جهانی است؛ خانه‌ای برای پژوهشگرانی از بیش از ۲۰ کشور که
          با گرد آوردن تخصص‌های گوناگون، پایان‌نامه، پروپوزال و تحلیل آماری را با بالاترین
          استانداردهای علمی و حفظ کامل محرمانگی همراهی می‌کنند.
        </p>
      </div>
    </section>

    <!-- ==================== STATS ==================== -->
    <!-- BACKEND: Fetch stats dynamically from database. -->
    <section class="section-sm bg-primary">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="1972" data-suffix="+">۱۹۷۲+</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">پروژه تحویل‌شده</div>
          </div>
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="27" data-suffix="+">۲۷+</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">پژوهشگر متخصص</div>
          </div>
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="20" data-suffix="+">۲۰+</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">کشور عضو</div>
          </div>
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="98" data-suffix="٪">۹۸٪</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">رضایت مراجعان</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== STORY ==================== -->
    <section class="section">
      <div class="container container-narrow text-center">
        <span class="eyebrow"><svg width="16" height="16"><use href="#icon-globe"/></svg> داستان ما</span>
        <h2 class="section-title">از یک ایده تا اکوسیستم جهانی</h2>
        <p class="section-desc" style="margin-inline:auto;">
          تزنویسه در سال ۱۳۹۷ با هدف ساده‌ای آغاز شد: پژوهش علمی را برای دانشجویان و پژوهشگران
          ساده‌تر، شفاف‌تر و در دسترس‌تر کنیم. آنچه امروز یک موسسه بین‌المللی است، از یک تیم کوچک
          و علاقه‌مند به علم آغاز به کار کرد. ما باور داریم پژوهش نباید مرز بشناسد؛ به همین دلیل
          پژوهشگرانی از بیش از ۲۰ کشور را گرد هم آورده‌ایم تا تجربه‌ای متفاوت از مشاوره و انجام
          پروژه‌های علمی ارائه دهیم.
        </p>
        <p class="section-desc" style="margin-inline:auto;">
          تعهد ما به محرمانگی، روش‌مندی علمی و استفاده از منابع به‌روز، پایه‌ای است که اعتماد
          هزاران پژوهشگر را به ما نشان داده است. امروز تزنویسه تنها یک موسسه نیست؛ یک اکوسیستم
          زنده از ابزارها، پژوهشگران و دانش است.
        </p>
      </div>
    </section>

    <!-- ==================== TIMELINE ==================== -->
    <!-- BACKEND: Timeline items from options/ACF. Year + title + description. -->
    <section class="section bg-surface-2">
      <div class="container">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-calendar"/></svg> مسیر ما</span>
          <h2 class="section-title">گذری بر تاریخچه تزنویسه</h2>
          <p class="section-desc" style="margin-inline:auto;">
            نقاط عطفی که تزنویسه را به آنجا که امروز هست رساند.
          </p>
        </div>

        <div class="timeline fade-in" style="max-width:720px;margin-inline:auto;">
          <div class="timeline-item">
            <h3 class="card-title">۱۳۹۷ — شروع راه</h3>
            <p class="card-desc">تزنویسه با تیمی کوچک از پژوهشگران و با هدف مشاوره تخصصی پایان‌نامه تأسیس شد. اولین پروژه‌ها در حوزه علوم پزشکی و مدیریت آغاز شد.</p>
          </div>
          <div class="timeline-item">
            <h3 class="card-title">۱۳۹۹ — راه‌اندازی ابزارهای آنلاین</h3>
            <p class="card-desc">نخستین ابزارهای آماری آنلاین رایگان در دسترس پژوهشگران قرار گرفت تا تحلیل‌های پایه بدون نیاز به نرم‌افزار پیچیده انجام شود.</p>
          </div>
          <div class="timeline-item">
            <h3 class="card-title">۱۴۰۲ — بخش هوش مصنوعی</h3>
            <p class="card-desc">با ورود فناوری هوش مصنوعی، بخش اختصاصی AI برای ایده‌یابی موضوع، نگارش و تحلیل هوشمند راه‌اندازی شد.</p>
          </div>
          <div class="timeline-item">
            <h3 class="card-title">۱۴۰۴ — شبکه جهانی پژوهشگران</h3>
            <p class="card-desc">تزنویسه به یک اکوسیستم بین‌المللی تبدیل شد و پژوهشگرانی از بیش از ۲۰ کشور را در یک شبکه واحد گرد آورد.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== TEAM PREVIEW ==================== -->
    <!-- BACKEND: Fetch featured researchers (top 3-4) from researchers CPT. -->
    <section class="section">
      <div class="container">
        <div class="flex justify-between items-center mb-8 fade-in">
          <div>
            <span class="eyebrow"><svg width="16" height="16"><use href="#icon-team"/></svg> تیم ما</span>
            <h2 class="section-title">پژوهشگران تزنویسه</h2>
          </div>
          <a href="team.html" class="btn btn-outline">
            همه پژوهشگران
            <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
          </a>
        </div>

        <div class="grid grid-4">
          <!-- BACKEND: Loop researchers -->
          <div class="team-card fade-in">
            <div class="team-avatar">م.ر</div>
            <h3 class="team-name">م.ر / PhD-MED-001</h3>
            <p class="team-role">متخصص علوم پزشکی</p>
            <div class="team-detail"><span>درجه:</span><span>دکتری</span></div>
            <div class="team-detail"><span>پروژه‌ها:</span><span>۱۲۰+</span></div>
            <div class="team-detail"><span>رضایت:</span><span>۹۸٪</span></div>
          </div>
          <div class="team-card fade-in">
            <div class="team-avatar">س.ک</div>
            <h3 class="team-name">س.ک / PhD-MGT-014</h3>
            <p class="team-role">متخصص مدیریت</p>
            <div class="team-detail"><span>درجه:</span><span>دکتری</span></div>
            <div class="team-detail"><span>پروژه‌ها:</span><span>۹۵+</span></div>
            <div class="team-detail"><span>رضایت:</span><span>۹۷٪</span></div>
          </div>
          <div class="team-card fade-in">
            <div class="team-avatar">ا.م</div>
            <h3 class="team-name">ا.م / PhD-ECO-007</h3>
            <p class="team-role">متخصص اقتصاد</p>
            <div class="team-detail"><span>درجه:</span><span>دکتری</span></div>
            <div class="team-detail"><span>پروژه‌ها:</span><span>۸۰+</span></div>
            <div class="team-detail"><span>رضایت:</span><span>۹۹٪</span></div>
          </div>
          <div class="team-card fade-in">
            <div class="team-avatar">ر.ح</div>
            <h3 class="team-name">ر.ح / PhD-ENG-022</h3>
            <p class="team-role">متخصص مهندسی</p>
            <div class="team-detail"><span>درجه:</span><span>دکتری</span></div>
            <div class="team-detail"><span>پروژه‌ها:</span><span>۷۰+</span></div>
            <div class="team-detail"><span>رضایت:</span><span>۹۶٪</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CONTACT OPTIONS ==================== -->
    <section class="section bg-surface-2">
      <div class="container">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-phone"/></svg> در دسترس</span>
          <h2 class="section-title">با ما در تماس باشید</h2>
          <p class="section-desc" style="margin-inline:auto;">
            تیم ما آماده پاسخ‌گویی به سوالات شماست. راه ارتباطی دلخواهتان را انتخاب کنید.
          </p>
        </div>

        <div class="grid grid-3">
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-phone"/></svg></div>
            <h3 class="card-title">تماس تلفنی</h3>
            <p class="card-desc"><a href="tel:+989302822091">۰۹۳۰۲۸۲۲۰۹۱</a></p>
            <a href="tel:+989302822091" class="btn btn-outline btn-sm">تماس بگیرید</a>
          </div>
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-mail"/></svg></div>
            <h3 class="card-title">ایمیل</h3>
            <p class="card-desc"><a href="mailto:teznevisan@gmail.com">teznevisan@gmail.com</a></p>
            <a href="mailto:teznevisan@gmail.com" class="btn btn-outline btn-sm">ارسال ایمیل</a>
          </div>
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-whatsapp"/></svg></div>
            <h3 class="card-title">واتساپ</h3>
            <p class="card-desc">گفتگوی آنلاین و سریع</p>
            <a href="https://wa.me/989302822091" class="btn btn-outline btn-sm">گفتگو در واتساپ</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="section">
      <div class="container">
        <div class="cta-section fade-in">
          <h2 class="cta-title">پروژه‌ات را با تیمی جهانی شروع کن</h2>
          <p class="cta-desc">
            به جمع هزاران پژوهشری که پروژه خود را با تزنویسه به سرانجام رسانده‌اند بپیوند.
          </p>
          <div class="hero-actions" style="justify-content:center;">
            <a href="inquiry.html" class="btn btn-primary btn-lg">
              <svg width="20" height="20"><use href="#icon-clipboard"/></svg>
              ثبت سفارش
            </a>
            <a href="contact.html" class="btn btn-outline btn-lg">
              <svg width="20" height="20"><use href="#icon-phone"/></svg>
              تماس با ما
            </a>
          </div>
        </div>
      </div>
    </section>
'''

page = S.page(head, main,
              desktop_active=None,
              mobile_active="about.html",
              bottom_active=None)

with open("/home/user/workspace/teznevise-redesign/about.html", "w", encoding="utf-8") as f:
    f.write(page)
print("about.html written:", len(page), "chars")
