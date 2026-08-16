#!/usr/bin/env python3
"""Build team.html — researcher team page"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

head = S.head(
    title="تیم پژوهشگران تزنویسه | متخصصان پایان‌نامه و تحلیل آماری",
    description="با ۲۷+ پژوهشگر متخصص تزنویسه از حوزه‌های علوم پزشکی، مدیریت، اقتصاد، علوم اجتماعی و مهندسی آشنا شوید. ۱۹۷۲+ پروژه با ۹۸٪ رضایت مراجعان.",
    og_title="تیم پژوهشگران تزنویسه",
    og_desc="۲۷+ پژوهشگر متخصص در ۴+ کشور، ۱۹۷۲+ پروژه انجام‌شده با ۹۸٪ رضایت مراجعان.",
    canonical_path="team.html",
    keywords="تیم تزنویسه, پژوهشگران, متخصص پایان نامه, متخصص تحلیل آماری, پژوهشگر علوم پزشکی",
    schema_type="CollectionPage",
)

# 8 researcher cards
researchers = [
    ("م.ر", "م.ر / PhD-MED-001", "متخصص علوم پزشکی", "دکتری", "SPSS, R", "۱۲۰+", "۴۵+", "۹۸٪", "med"),
    ("س.ک", "س.ک / PhD-MGT-014", "متخصص مدیریت", "دکتری", "SPSS, PLS", "۹۵+", "۳۸+", "۹۷٪", "mgt"),
    ("ا.م", "ا.م / PhD-ECO-007", "متخصص اقتصاد", "دکتری", "EViews, Stata", "۸۰+", "۳۲+", "۹۹٪", "eco"),
    ("ر.ح", "ر.ح / PhD-ENG-022", "متخصص مهندسی", "دکتری", "MATLAB, Python", "۷۰+", "۲۸+", "۹۶٪", "eng"),
    ("ف.ع", "ف.ع / PhD-SOC-009", "متخصص علوم اجتماعی", "دکتری", "SPSS, MAXQDA", "۸۵+", "۴۱+", "۹۸٪", "soc"),
    ("ن.ت", "ن.ت / PhD-MED-018", "متخصص علوم پزشکی", "دکتری", "R, SAS", "۶۵+", "۲۲+", "۹۷٪", "med"),
    ("ح.ب", "ح.ب / PhD-MGT-031", "متخصص مدیریت", "دکتری", "SPSS, AMOS", "۹۰+", "۳۵+", "۹۸٪", "mgt"),
    ("ز.گ", "ز.گ / PhD-ECO-025", "متخصص اقتصاد", "دکتری", "Stata, Python", "۷۵+", "۲۹+", "۹۹٪", "eco"),
]

cards = ""
for initials, code, role, degree, tools, projects, articles, satisfaction, cat in researchers:
    cards += f'''
          <div class="team-card fade-in" data-cat="{cat}">
            <div class="team-avatar">{initials}</div>
            <h3 class="team-name">{code}</h3>
            <p class="team-role">{role}</p>
            <div class="team-detail"><span>درجه:</span><span>{degree}</span></div>
            <div class="team-detail"><span>ابزارها:</span><span>{tools}</span></div>
            <div class="team-detail"><span>پروژه‌ها:</span><span>{projects}</span></div>
            <div class="team-detail"><span>مقالات:</span><span>{articles}</span></div>
            <div class="team-detail"><span>رضایت:</span><span>{satisfaction}</span></div>
          </div>'''

main = '''
    <!-- ==================== PAGE HERO ==================== -->
    <section class="page-hero bg-gradient-primary">
      <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
          <a href="index.html">خانه</a>
          <span class="breadcrumb-sep">/</span>
          <span class="current">تیم پژوهشگران</span>
        </nav>
        <h1 class="page-hero-title">تیم پژوهشگران تزنویسه</h1>
        <p class="page-hero-desc">
          پژوهشگرانی متخصص از حوزه‌های گوناگون، گرد هم آمده‌اند تا پروژه پژوهشی شما را با
          بالاترین استانداردهای علمی همراهی کنند.
        </p>
      </div>
    </section>

    <!-- ==================== STATS ==================== -->
    <!-- BACKEND: Fetch team stats dynamically from database. -->
    <section class="section-sm bg-primary">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="27" data-suffix="+">۲۷+</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">پژوهشگر متخصص</div>
          </div>
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="4" data-suffix="+">۴+</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">کشور حضور</div>
          </div>
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="1972" data-suffix="+">۱۹۷۲+</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">پروژه انجام‌شده</div>
          </div>
          <div class="stat-card" style="background:transparent;border:none;color:#fff;">
            <div class="stat-value" style="color:#fff" data-counter="98" data-suffix="٪">۹۸٪</div>
            <div class="stat-label" style="color:rgba(255,255,255,0.8)">رضایت مراجعان</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== TEAM GRID ==================== -->
    <!-- BACKEND: Researchers from custom post type or users with researcher role.
         Fields: code, role, degree, tools, projects count, articles count, satisfaction,
         discipline (taxonomy: med, mgt, eco, soc, eng). Filter via JS or query param. -->
    <section class="section">
      <div class="container">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-team"/></svg> پژوهشگران</span>
          <h2 class="section-title">با متخصصان ما آشنا شوید</h2>
          <p class="section-desc" style="margin-inline:auto;">
            بر اساس حوزه تخصص مورد نظر خود فیلتر کنید.
          </p>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs fade-in" style="justify-content:center;margin-bottom:2.5rem;flex-wrap:wrap;display:flex;gap:0.5rem;">
          <button class="filter-tab active" data-filter="all">همه</button>
          <button class="filter-tab" data-filter="med">علوم پزشکی</button>
          <button class="filter-tab" data-filter="mgt">مدیریت</button>
          <button class="filter-tab" data-filter="eco">اقتصاد</button>
          <button class="filter-tab" data-filter="soc">علوم اجتماعی</button>
          <button class="filter-tab" data-filter="eng">مهندسی</button>
        </div>

        <div class="grid grid-4">
          ''' + cards.strip() + '''
        </div>
      </div>
    </section>

    <!-- ==================== JOIN TEAM CTA ==================== -->
    <section class="section bg-surface-2">
      <div class="container">
        <div class="cta-section fade-in">
          <h2 class="cta-title">پژوهشگر هستی؟ به تیم ما بپیوند</h2>
          <p class="cta-desc">
            اگر در حوزه‌ای تخصص داری و علاقه‌مند به همراهی پژوهشگران هستی، خوشحال می‌شویم با تو آشنا شویم.
          </p>
          <div class="hero-actions" style="justify-content:center;">
            <a href="contact.html" class="btn btn-primary btn-lg">
              <svg width="20" height="20"><use href="#icon-user"/></svg>
              درخواست همکاری
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
              mobile_active="team.html",
              bottom_active=None)

with open("/home/user/workspace/teznevise-redesign/team.html", "w", encoding="utf-8") as f:
    f.write(page)
print("team.html written:", len(page), "chars")
