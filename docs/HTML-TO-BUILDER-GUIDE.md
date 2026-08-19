# صفحه‌ساز تزنویسه — راهنمای تبدیل HTML

English summary follows the Persian section.

## فارسی

### چه چیزی تغییر کرده است؟

۱۶ صفحهٔ استاتیک `teznevise_work` وجود دارد. ۱۴ تای آن‌ها حالا می‌توانند از
**صفحه‌ساز تزنویسه** مدیریت شوند (۱۳ برگه + یک نوشتهٔ نمونه). بلاگ و ۴۰۴
صفحه‌ساز ندارند. هر بخش (هیرو، کارت خدمت، مراحل، نوار اقدام و …) از ویرایشگر
برگه اضافه، جابه‌جا، تکرار یا خاموش می‌شود.

### راه‌اندازی

1. **نمایش ← راه‌اندازی تزنویسه**
2. دکمهٔ «ایجاد / هم‌ترازسازی صفحات پیشنهادی» را بزنید.  
   صفحات ساخته می‌شوند و اگر هنوز بخش صفحه‌ساز نداشته باشند، محتوای HTML در آن‌ها پر می‌شود.
3. برای بازنویسی بخش‌های موجود (کارهای دستی پاک می‌شود) گزینهٔ جایگزینی را علامت بزنید.

### ویرایش بعدی

برگه را باز کنید → جعبهٔ **صفحه‌ساز تزنویسه — بخش‌های سفارشی**.

- بلاگ (`/blog/`) همان فهرست نوشته‌های وردپرس است؛ صفحه‌ساز ندارد.
- صفحهٔ ۴۰۴ قالب کدنویسی‌شده است (شناسهٔ برگه ندارد).
- فرم تماس / ثبت سفارش و ماشین‌حساب آمار توصیفی در محتوای برگه می‌مانند.

### آدرس‌ها

slugها عوض نمی‌شوند: `/about/`، `/contact/`، `/service-thesis/` و بقیه مثل قبل هستند.

---

## English

Static HTML from `teznevise_work/` is now seeded into the Flexible Page Builder
(`_teznevise_builder_sections`). Editors manage sections from the page/post
editor. Permalinks are unchanged.

**Setup:** Appearance → Teznevise Setup → create/align recommended pages.
Builder JSON is written only when a page has none, unless you check replace.

**Not builder-managed:** the blog index (`home.php`) and `404.php`. Those
requests have no (or the wrong) post-meta owner. See the roadmap.

**Kept in templates / post content:** contact NAP + forms, the descriptive
statistics calculator, privacy policy long copy, and the sample post article.

**Conflict note:** if PR #384 also rewrites privacy/team/tools/contact as
hard-coded System A templates, this branch is the builder-section source of
truth for those singular pages.
