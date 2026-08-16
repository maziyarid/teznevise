#!/usr/bin/env python3
"""Build inquiry.html — multi-step order registration form"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

head = S.head(
    title="ثبت سفارش پروژه پژوهشی | تزنویسه — رایگان و سریع",
    description="ثبت سفارش پایان‌نامه، پروپوزال یا تحلیل آماری به‌صورت رایگان. سفارش شما به متخصص مربوطه ارجاع شده و برآورد دقیق هزینه و زمان اعلام می‌شود. محرمانگی کامل با NDA.",
    og_title="ثبت سفارش پروژه پژوهشی — تزنویسه",
    og_desc="ثبت سفارش رایگان پایان‌نامه، پروپوزال و تحلیل آماری. ارجاع به متخصص، برآورد دقیق و شروع پس از تأیید.",
    canonical_path="inquiry.html",
    keywords="ثبت سفارش, سفارش پایان نامه, سفارش پروپوزال, سفارش تحلیل آماری, فرم سفارش تزنویسه",
    schema_type="WebPage",
)

main = '''
    <!-- ==================== PAGE HERO ==================== -->
    <section class="page-hero bg-gradient-primary">
      <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
          <a href="index.html">خانه</a>
          <span class="breadcrumb-sep">/</span>
          <span class="current">ثبت سفارش</span>
        </nav>
        <h1 class="page-hero-title">ثبت سفارش پروژه پژوهشی</h1>
        <p class="page-hero-desc">
          ثبت سفارش کاملاً رایگان است. سفارش شما به متخصص مربوطه ارجاع داده می‌شود،
          برآورد دقیق هزینه و زمان اعلام می‌گردد و پروژه پس از تأیید شما آغاز می‌شود.
        </p>
      </div>
    </section>

    <!-- ==================== KEY POINTS ==================== -->
    <section class="section-sm bg-surface-2">
      <div class="container">
        <div class="grid grid-4">
          <div class="card text-center fade-in" style="border:none;background:transparent;">
            <div class="card-icon" style="margin-inline:auto;color:var(--color-primary);"><svg><use href="#icon-check-circle"/></svg></div>
            <h3 class="card-title">ثبت رایگان</h3>
            <p class="card-desc">هزینه‌ای برای ثبت سفارش دریافت نمی‌شود.</p>
          </div>
          <div class="card text-center fade-in" style="border:none;background:transparent;">
            <div class="card-icon" style="margin-inline:auto;color:var(--color-primary);"><svg><use href="#icon-researcher"/></svg></div>
            <h3 class="card-title">ارجاع به متخصص</h3>
            <p class="card-desc">سفارش به پژوهشگر تخصص حوزه شما ارجاع داده می‌شود.</p>
          </div>
          <div class="card text-center fade-in" style="border:none;background:transparent;">
            <div class="card-icon" style="margin-inline:auto;color:var(--color-primary);"><svg><use href="#icon-target"/></svg></div>
            <h3 class="card-title">برآورد دقیق</h3>
            <p class="card-desc">هزینه و زمان دقیق پیش از شروع اعلام می‌شود.</p>
          </div>
          <div class="card text-center fade-in" style="border:none;background:transparent;">
            <div class="card-icon" style="margin-inline:auto;color:var(--color-primary);"><svg><use href="#icon-shield"/></svg></div>
            <h3 class="card-title">محرمانگی (NDA)</h3>
            <p class="card-desc">اطلاعات شما تحت قرارداد عدم افشا محافظت می‌شود.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== MULTI-STEP FORM ==================== -->
    <!-- BACKEND: Multi-step order form. Save to orders table. Send to matching specialist.
         Status: pending. Fields: service_type, field, degree, deadline, budget, phone,
         email, description. Email specialist + admin notification. -->
    <section class="section">
      <div class="container" style="max-width:820px;">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-clipboard"/></svg> فرم سفارش</span>
          <h2 class="section-title">جزئیات سفارش خود را وارد کنید</h2>
          <p class="section-desc" style="margin-inline:auto;">
            پروژه پس از تأیید شما و توافق نهایی آغاز می‌شود. هیچ تعهد مالی پیش از توافق وجود ندارد.
          </p>
        </div>

        <!-- Step Indicator -->
        <div class="fade-in" style="display:flex;justify-content:space-between;align-items:center;max-width:680px;margin:0 auto 2.5rem;position:relative;">
          <div style="position:absolute;top:18px;right:8%;left:8%;height:2px;background:var(--color-border);z-index:0;"></div>
          <div style="position:absolute;top:18px;right:8%;height:2px;background:var(--color-primary);z-index:0;width:0;" id="stepProgress"></div>
          <div class="step-node" data-step="1" style="position:relative;z-index:1;text-align:center;flex:1;">
            <div class="step-circle active">۱</div>
            <div class="step-label">نوع خدمت</div>
          </div>
          <div class="step-node" data-step="2" style="position:relative;z-index:1;text-align:center;flex:1;">
            <div class="step-circle">۲</div>
            <div class="step-label">جزئیات</div>
          </div>
          <div class="step-node" data-step="3" style="position:relative;z-index:1;text-align:center;flex:1;">
            <div class="step-circle">۳</div>
            <div class="step-label">زمان و بودجه</div>
          </div>
          <div class="step-node" data-step="4" style="position:relative;z-index:1;text-align:center;flex:1;">
            <div class="step-circle">۴</div>
            <div class="step-label">اطلاعات تماس</div>
          </div>
        </div>

        <form action="#" method="post" id="orderForm" class="card fade-in">
          <!-- Step 1: Service Type -->
          <div class="form-step" data-step="1">
            <h3 class="card-title mb-6"><svg width="22" height="22" style="vertical-align:middle;color:var(--color-primary);"><use href="#icon-thesis"/></svg> نوع خدمت مورد نظر</h3>
            <div class="form-group">
              <label class="form-label" for="serviceType">نوع خدمت <span class="required">*</span></label>
              <select id="serviceType" name="service_type" class="form-select" required>
                <option value="">انتخاب نوع خدمت...</option>
                <option value="thesis">انجام پایان‌نامه</option>
                <option value="proposal">انجام پروپوزال</option>
                <option value="statistics">تحلیل آماری</option>
                <option value="other">سایر</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="field">رشته تحصیلی <span class="required">*</span></label>
              <input type="text" id="field" name="field" class="form-input" placeholder="مثال: مدیریت، علوم پزشکی، مهندسی..." required>
            </div>
            <div class="form-group">
              <label class="form-label" for="degree">مقطع تحصیلی <span class="required">*</span></label>
              <select id="degree" name="degree" class="form-select" required>
                <option value="">انتخاب مقطع...</option>
                <option value="bachelor">کارشناسی</option>
                <option value="master">کارشناسی ارشد</option>
                <option value="phd">دکتری</option>
              </select>
            </div>
            <div class="flex justify-between mt-6">
              <span></span>
              <button type="button" class="btn btn-primary" data-next>
                مرحله بعد
                <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
              </button>
            </div>
          </div>

          <!-- Step 2: Details -->
          <div class="form-step hidden" data-step="2">
            <h3 class="card-title mb-6"><svg width="22" height="22" style="vertical-align:middle;color:var(--color-primary);"><use href="#icon-article"/></svg> جزئیات پروژه</h3>
            <div class="form-group">
              <label class="form-label" for="title">عنوان احتمالی موضوع</label>
              <input type="text" id="title" name="title" class="form-input" placeholder="عنوان موضوع (اختیاری)">
            </div>
            <div class="form-group">
              <label class="form-label" for="description">شرح پروژه و نیاز شما <span class="required">*</span></label>
              <textarea id="description" name="description" class="form-textarea" rows="6" placeholder="شرح کامل پروژه، نیازمندی‌ها و انتظارات خود را بنویسید..." required></textarea>
              <span class="form-hint">هرچه شرح دقیق‌تر باشد، برآورد هزینه و زمان دقیق‌تر خواهد بود.</span>
            </div>
            <div class="flex justify-between mt-6">
              <button type="button" class="btn btn-outline" data-prev>
                <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
                مرحله قبل
              </button>
              <button type="button" class="btn btn-primary" data-next>
                مرحله بعد
                <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
              </button>
            </div>
          </div>

          <!-- Step 3: Timeline & Budget -->
          <div class="form-step hidden" data-step="3">
            <h3 class="card-title mb-6"><svg width="22" height="22" style="vertical-align:middle;color:var(--color-primary);"><use href="#icon-calendar"/></svg> زمان و بودجه</h3>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="deadline">مهلت تحویل <span class="required">*</span></label>
                <input type="date" id="deadline" name="deadline" class="form-input" required>
                <span class="form-hint">تاریخ تقریبی مورد نظر شما</span>
              </div>
              <div class="form-group">
                <label class="form-label" for="budget">بازه بودجه</label>
                <select id="budget" name="budget" class="form-select">
                  <option value="">انتخاب بازه...</option>
                  <option value="lt3">کمتر از ۳ میلیون تومان</option>
                  <option value="3-7">۳ تا ۷ میلیون تومان</option>
                  <option value="7-15">۷ تا ۱۵ میلیون تومان</option>
                  <option value="15-30">۱۵ تا ۳۰ میلیون تومان</option>
                  <option value="gt30">بیش از ۳۰ میلیون تومان</option>
                  <option value="unknown">هنوز نامشخص</option>
                </select>
              </div>
            </div>
            <div class="flex justify-between mt-6">
              <button type="button" class="btn btn-outline" data-prev>
                <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
                مرحله قبل
              </button>
              <button type="button" class="btn btn-primary" data-next>
                مرحله بعد
                <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
              </button>
            </div>
          </div>

          <!-- Step 4: Contact Info -->
          <div class="form-step hidden" data-step="4">
            <h3 class="card-title mb-6"><svg width="22" height="22" style="vertical-align:middle;color:var(--color-primary);"><use href="#icon-phone"/></svg> اطلاعات تماس</h3>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="phone">شماره تماس <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="۰۹۱۲۳۴۵۶۷۸۹" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="email">ایمیل <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="email@example.com" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="contact_method">روش ترجیحی تماس</label>
              <select id="contact_method" name="contact_method" class="form-select">
                <option value="whatsapp">واتساپ</option>
                <option value="phone">تماس تلفنی</option>
                <option value="telegram">تلگرام</option>
                <option value="email">ایمیل</option>
              </select>
            </div>
            <div class="card bg-surface-2" style="margin-bottom:1.5rem;">
              <p class="card-desc" style="display:flex;gap:0.5rem;align-items:flex-start;">
                <svg width="20" height="20" style="color:var(--color-primary);flex-shrink:0;margin-top:2px;"><use href="#icon-shield"/></svg>
                اطلاعات شما تحت قرارداد عدم افشا (NDA) محافظت می‌شود و بدون اجازه شما به اشتراک گذاشته نمی‌شود.
              </p>
            </div>
            <div class="flex justify-between mt-6">
              <button type="button" class="btn btn-outline" data-prev>
                <svg width="18" height="18"><use href="#icon-arrow-left"/></svg>
                مرحله قبل
              </button>
              <button type="submit" class="btn btn-primary btn-lg">
                <svg width="20" height="20"><use href="#icon-check-circle"/></svg>
                ثبت نهایی سفارش
              </button>
            </div>
          </div>
        </form>

        <p class="text-center text-muted mt-6">
          نیاز به راهنمایی دارید؟
          <a href="https://wa.me/989302822091" class="text-primary">در واتساپ با ما گفتگو کنید</a>
        </p>
      </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="section bg-surface-2">
      <div class="container">
        <div class="cta-section fade-in">
          <h2 class="cta-title">سوالی قبل از ثبت سفارش دارید؟</h2>
          <p class="cta-desc">
            کارشناسان ما آماده پاسخ‌گویی به تمام سوالات شما هستند.
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
              mobile_active="inquiry.html",
              bottom_active="inquiry.html")

with open("/home/user/workspace/teznevise-redesign/inquiry.html", "w", encoding="utf-8") as f:
    f.write(page)
print("inquiry.html written:", len(page), "chars")
