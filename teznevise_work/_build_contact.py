#!/usr/bin/env python3
"""Build contact.html"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

schema_extra = ''',
    "telephone": "+989302822091",
    "email": "teznevisan@gmail.com",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "انقلاب، خیابان ۱۲ فروردین، پلاک ۸",
      "addressLocality": "تهران",
      "addressCountry": "IR"
    }'''

head = S.head(
    title="تماس با تزنویسه | مشاوره رایگان پایان‌نامه و تحلیل آماری",
    description="برای تماس با تزنویسه از طریق تلفن، ایمیل، واتساپ یا فرم تماس اقدام کنید. آدرس دفتر تهران، ساعات کاری و پاسخ به پرسش‌های متداول تماس.",
    og_title="تماس با تزنویسه — ما همیشه در دسترس شما هستیم",
    og_desc="تماس تلفنی، ایمیل، واتساپ و فرم تماس آنلاین تزنویسه. آدرس و ساعات کاری موسسه پژوهشی.",
    canonical_path="contact.html",
    keywords="تماس با تزنویسه, آدرس تزنویسه, مشاوره رایگان, تلفن تزنویسه, فرم تماس",
    schema_type="ContactPage",
    schema_extra=schema_extra,
)

main = '''
    <!-- ==================== PAGE HERO ==================== -->
    <section class="page-hero bg-gradient-primary">
      <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر">
          <a href="index.html">خانه</a>
          <span class="breadcrumb-sep">/</span>
          <span class="current">تماس با ما</span>
        </nav>
        <h1 class="page-hero-title">تماس با تزنویسه — ما همیشه در دسترس شما هستیم</h1>
        <p class="page-hero-desc">
          چه سوالی درباره پروژه پژوهشی خود دارید، چه نیاز به مشاوره دارید؛ تیم ما آماده پاسخ‌گویی است.
          راه ارتباطی دلخواهتان را انتخاب کنید یا فرم زیر را پر کنید.
        </p>
      </div>
    </section>

    <!-- ==================== CONTACT INFO CARDS ==================== -->
    <section class="section">
      <div class="container">
        <div class="grid grid-4">
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-location"/></svg></div>
            <h3 class="card-title">آدرس</h3>
            <p class="card-desc">انقلاب، خیابان ۱۲ فروردین، بعد از روانمهر، روبروی افق کوروش، نبش کوچه بهشت آیین، پلاک ۸</p>
          </div>
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-phone"/></svg></div>
            <h3 class="card-title">تلفن</h3>
            <p class="card-desc">
              <a href="tel:+989302822091">۰۹۳۰۲۸۲۲۰۹۱</a><br>
              <a href="https://t.me/Teznevise">@Teznevise</a>
            </p>
          </div>
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-mail"/></svg></div>
            <h3 class="card-title">ایمیل</h3>
            <p class="card-desc"><a href="mailto:teznevisan@gmail.com">teznevisan@gmail.com</a></p>
          </div>
          <div class="card text-center card-hover fade-in">
            <div class="card-icon" style="margin-inline:auto;"><svg><use href="#icon-clock"/></svg></div>
            <h3 class="card-title">ساعات کاری</h3>
            <p class="card-desc">شنبه تا پنجشنبه<br>۹ صبح تا ۹ شب</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CONTACT FORM + MAP ==================== -->
    <section class="section bg-surface-2">
      <div class="container">
        <div class="grid grid-2" style="align-items:start;">
          <!-- Form -->
          <!-- BACKEND: Contact form submission. POST to /wp-json/teznevise/v1/contact.
               Save to messages table, email notification to teznevisan@gmail.com,
               auto-reply to user. Sanitize & validate all fields server-side. -->
          <div class="card fade-in">
            <div class="mb-6">
              <span class="eyebrow"><svg width="16" height="16"><use href="#icon-mail"/></svg> فرم تماس</span>
              <h2 class="section-title">پیام خود را بفرستید</h2>
              <p class="section-desc">پاسخ شما در کوتاه‌ترین زمان ممکن داده می‌شود.</p>
            </div>
            <form action="#" method="post" id="contactForm">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="cName">نام و نام خانوادگی <span class="required">*</span></label>
                  <input type="text" id="cName" name="name" class="form-input" placeholder="نام کامل شما" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cPhone">شماره تماس <span class="required">*</span></label>
                  <input type="tel" id="cPhone" name="phone" class="form-input" placeholder="۰۹۱۲۳۴۵۶۷۸۹" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="cEmail">ایمیل <span class="required">*</span></label>
                  <input type="email" id="cEmail" name="email" class="form-input" placeholder="email@example.com" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cSubject">موضوع</label>
                  <select id="cSubject" name="subject" class="form-select">
                    <option value="">انتخاب موضوع...</option>
                    <option value="thesis">مشاوره پایان‌نامه</option>
                    <option value="proposal">مشاوره پروپوزال</option>
                    <option value="statistics">تحلیل آماری</option>
                    <option value="tools">ابزارهای آنلاین</option>
                    <option value="other">سایر</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="cMessage">پیام شما <span class="required">*</span></label>
                <textarea id="cMessage" name="message" class="form-textarea" rows="6" placeholder="پیام خود را بنویسید..." required></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-block btn-lg">
                <svg width="20" height="20"><use href="#icon-mail"/></svg>
                ارسال پیام
              </button>
              <p class="form-hint">با ارسال این فرم، با <a href="privacy.html">سیاست حریم خصوصی</a> ما موافقت می‌کنید.</p>
            </form>
          </div>

          <!-- Map + Social -->
          <div class="fade-in">
            <div class="card mb-6">
              <h3 class="card-title mb-4">موقعیت ما روی نقشه</h3>
              <!-- BACKEND: Embed Google Maps iframe for office location. -->
              <div style="aspect-ratio:16/10;border-radius:var(--radius);overflow:hidden;background:var(--color-surface-offset);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:0.5rem;color:var(--color-text-muted);">
                <svg width="48" height="48" style="color:var(--color-primary);"><use href="#icon-location"/></svg>
                <span>نقشه تعاملی دفتر تزنویسه</span>
                <span style="font-size:0.875rem;">تهران، انقلاب، خیابان ۱۲ فروردین، پلاک ۸</span>
              </div>
            </div>

            <div class="card">
              <h3 class="card-title mb-4">شبکه‌های اجتماعی</h3>
              <div class="footer-social" style="justify-content:flex-start;">
                <a href="https://wa.me/989302822091" aria-label="واتساپ"><svg><use href="#icon-whatsapp"/></svg></a>
                <a href="https://t.me/Teznevise" aria-label="تلگرام"><svg><use href="#icon-telegram"/></svg></a>
                <a href="#" aria-label="اینستاگرام"><svg><use href="#icon-instagram"/></svg></a>
                <a href="mailto:teznevisan@gmail.com" aria-label="ایمیل"><svg><use href="#icon-mail"/></svg></a>
              </div>
              <p class="card-desc mt-4">در شبکه‌های اجتماعی نیز ما را دنبال کنید تا از جدیدترین مطالب و ابزارها باخبر شوید.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== FAQ ==================== -->
    <section class="section">
      <div class="container" style="max-width:800px;">
        <div class="text-center mb-8 fade-in">
          <span class="eyebrow"><svg width="16" height="16"><use href="#icon-comments"/></svg> پرسش‌های متداول تماس</span>
          <h2 class="section-title">سوالات پرتکرار درباره تماس</h2>
        </div>

        <div class="faq-group fade-in">
          <div class="faq-item open">
            <button class="faq-question">
              چه زمانی پاسخ پیام خود را دریافت می‌کنم؟
              <span class="faq-icon"><svg><use href="#icon-chevron-down"/></svg></span>
            </button>
            <div class="faq-answer">
              <p>تیم ما در ساعات کاری (۹ تا ۲۱) معمولاً کمتر از ۲ ساعت به پیام‌های شما پاسخ می‌دهد. پیام‌های ارسالی خارج از ساعات کاری در اولین فرصت روز کاری بعدی پاسخ داده می‌شوند.</p>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-question">
              آیا مشاوره اولیه رایگان است؟
              <span class="faq-icon"><svg><use href="#icon-chevron-down"/></svg></span>
            </button>
            <div class="faq-answer">
              <p>بله، مشاوره اولیه کاملاً رایگان است. پس از بررسی موضوع و نیاز شما، هزینه و زمان دقیق پروژه اعلام می‌شود.</p>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-question">
              آیا امکان مشاوره حضوری وجود دارد؟
              <span class="faq-icon"><svg><use href="#icon-chevron-down"/></svg></span>
            </button>
            <div class="faq-answer">
              <p>بله، با هماهنگی قبلی می‌توانید به دفتر ما در تهران مراجعه کنید. اما اکثر خدمات به‌صورت آنلاین ارائه می‌شود.</p>
            </div>
          </div>
          <div class="faq-item">
            <button class="faq-question">
              برای شروع پروژه چه باید کنم؟
              <span class="faq-icon"><svg><use href="#icon-chevron-down"/></svg></span>
            </button>
            <div class="faq-answer">
              <p>ساده‌ترین راه، تکمیل <a href="inquiry.html">فرم ثبت سفارش</a> است. پس از آن کارشناسان ما با شما تماس می‌گیرند و برآورد هزینه و زمان را اعلام می‌کنند.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="section">
      <div class="container">
        <div class="cta-section fade-in">
          <h2 class="cta-title">آماده شروع پروژه‌ات هستی؟</h2>
          <p class="cta-desc">
            فرم ثبت سفارش را پر کن تا در کوتاه‌ترین زمان با تو تماس بگیریم.
          </p>
          <div class="hero-actions" style="justify-content:center;">
            <a href="inquiry.html" class="btn btn-primary btn-lg">
              <svg width="20" height="20"><use href="#icon-clipboard"/></svg>
              ثبت سفارش
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
              mobile_active="contact.html",
              bottom_active="contact.html")

with open("/home/user/workspace/teznevise-redesign/contact.html", "w", encoding="utf-8") as f:
    f.write(page)
print("contact.html written:", len(page), "chars")
