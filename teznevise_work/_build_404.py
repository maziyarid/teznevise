#!/usr/bin/env python3
"""Build 404.html — error page"""
import sys
sys.path.insert(0, "/home/user/workspace/teznevise-redesign")
import _build_shared as S

head = S.head(
    title="صفحه یافت نشد | تزنویسه",
    description="صفحه مورد نظر یافت نشد. به صفحه اصلی تزنویسه بازگردید یا در سایت جستجو کنید. خدمات مشاوره پایان‌نامه، پروپوزال و تحلیل آماری.",
    og_title="صفحه یافت نشد — تزنویسه",
    og_desc="صفحه مورد نظر شما یافت نشد. به صفحه اصلی بازگردید یا جستجو کنید.",
    canonical_path="404.html",
    schema_type="WebPage",
)

main = '''
    <!-- ==================== 404 ERROR ==================== -->
    <section class="section" style="min-height:60vh;display:flex;align-items:center;">
      <div class="container">
        <div class="text-center fade-in" style="max-width:640px;margin:0 auto;">
          <!-- Large 404 number -->
          <div style="font-size:clamp(6rem,20vw,12rem);font-weight:800;line-height:1;background:var(--color-primary-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:var(--color-primary);margin-bottom:1rem;letter-spacing:-0.04em;">
            ۴۰۴
          </div>

          <span class="badge mb-4" style="display:inline-flex;align-items:center;gap:0.4rem;">
            <svg width="16" height="16"><use href="#icon-search"/></svg>
            خطا
          </span>
          <h1 style="font-size:var(--text-3xl);font-weight:700;margin-bottom:1rem;color:var(--color-text);">
            صفحه مورد نظر یافت نشد
          </h1>
          <p class="section-desc" style="margin-inline:auto;">
            صفحه‌ای که به دنبال آن هستید وجود ندارد، جابه‌جا شده است یا آدرس آن اشتباه وارد شده است.
          </p>

          <!-- Search box -->
          <form action="#" method="get" style="max-width:480px;margin:2rem auto;display:flex;gap:0.5rem;align-items:center;background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius);padding:0.5rem;">
            <svg width="22" height="22" style="color:var(--color-text-muted);margin-inline-start:0.5rem;flex-shrink:0;"><use href="#icon-search"/></svg>
            <input type="search" class="form-input" placeholder="جستجو در تزنویسه..." aria-label="جستجو" style="border:none;background:transparent;box-shadow:none;padding:0.5rem;">
            <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0;">جستجو</button>
          </form>

          <!-- Quick links -->
          <div style="margin-top:2.5rem;">
            <p class="text-muted mb-4" style="font-size:0.9rem;">میانبرهای سریع:</p>
            <div class="flex justify-center" style="flex-wrap:wrap;gap:0.75rem;">
              <a href="index.html" class="btn btn-outline">
                <svg width="18" height="18"><use href="#icon-home"/></svg>
                خانه
              </a>
              <a href="inquiry.html" class="btn btn-outline">
                <svg width="18" height="18"><use href="#icon-clipboard"/></svg>
                ثبت سفارش
              </a>
              <a href="tools.html" class="btn btn-outline">
                <svg width="18" height="18"><use href="#icon-tools"/></svg>
                ابزارها
              </a>
              <a href="blog.html" class="btn btn-outline">
                <svg width="18" height="18"><use href="#icon-blog"/></svg>
                بلاگ
              </a>
              <a href="contact.html" class="btn btn-outline">
                <svg width="18" height="18"><use href="#icon-phone"/></svg>
                تماس با ما
              </a>
            </div>
          </div>

          <p class="text-muted mt-6" style="font-size:0.875rem;">
            نیاز به کمک دارید؟
            <a href="https://wa.me/989302822091" class="text-primary">در واتساپ با ما گفتگو کنید</a>
          </p>
        </div>
      </div>
    </section>
'''

page = S.page(head, main,
              desktop_active=None,
              mobile_active=None,
              bottom_active=None)

with open("/home/user/workspace/teznevise-redesign/404.html", "w", encoding="utf-8") as f:
    f.write(page)
print("404.html written:", len(page), "chars")
