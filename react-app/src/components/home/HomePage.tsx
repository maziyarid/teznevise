import { useState } from "react";
import { Link } from "@tanstack/react-router";
import { AppLink } from "@/components/AppLink";
import { ARTICLES, REASONS, SERVICES, STEPS } from "@/lib/content";
import { TESTIMONIALS } from "@/lib/site-extra";
import { CtaBand } from "@/components/shared/CtaBand";
import { Universities } from "@/components/shared/Universities";
import { FaIcon } from "@/components/ui/FaIcon";
import { faNum } from "@/lib/format";

const FA: Record<string, string> = {
  grad: "fa-graduation-cap",
  file: "fa-file-lines",
  chart: "fa-chart-line",
  calc: "fa-calculator",
  idea: "fa-lightbulb",
  sim: "fa-microchip",
  qual: "fa-comments",
  proj: "fa-folder-open",
  paper: "fa-newspaper",
  shield: "fa-shield-halved",
  compass: "fa-compass",
  bolt: "fa-bolt",
  pen: "fa-pen-nib",
};

export function HomePage() {
  const [seoOpen, setSeoOpen] = useState(false);

  return (
    <>
      <section className="hero-new">
        <div className="container-tz">
          <div className="hero-grid">
            <div className="hero-copy">
              <span className="eyebrow">همراهی علمی از ایده تا دفاع</span>
              <h1>
                از موضوع تا تحویل نهایی،{" "}
                <span className="grad">پژوهش‌تان را حرفه‌ای‌تر</span> پیش ببرید.
              </h1>
              <p>
                تزنویسه برای پایان‌نامه، پروپوزال، پروژه دانشگاهی و تحلیل آماری یک
                مسیر منظم، خلاقانه و قابل اتکا می‌سازد؛ با پشتیبانی تخصصی، محرمانگی
                کامل و پاسخ‌گویی سریع.
              </p>
              <div className="mb-6 flex flex-wrap gap-3">
                <Link to="/inquiry" className="btn-tz btn-primary-tz btn-lg-tz">
                  ثبت سفارش و شروع مشاوره
                </Link>
                <a href="#services" className="btn-tz btn-light-tz btn-lg-tz">
                  مشاهده خدمات
                </a>
              </div>
              <div className="flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted">
                {["مشاوره اولیه رایگان", "متخصص هر رشته", "پشتیبانی تا تحویل"].map(
                  (t) => (
                    <span key={t} className="inline-flex items-center gap-2">
                      <i className="grid size-5 place-items-center rounded-full bg-brand/10 text-brand not-italic">
                        <FaIcon icon="fa-check" className="text-[10px]" />
                      </i>
                      {t}
                    </span>
                  ),
                )}
              </div>
            </div>
            <div className="hero-visual" aria-label="نمایی از خدمات تزنویسه">
              <div className="hero-orb" />
              <a className="hero-order" href="/inquiry">
                ثبت سفارش
              </a>
              <span className="orbit-tag t1">SPSS</span>
              <span className="orbit-tag t2">Matlab</span>
              <span className="orbit-tag t3">پایان‌نامه</span>
              <span className="orbit-tag t4">پروژه دانشگاهی</span>
            </div>
          </div>
        </div>
      </section>

      <section className="section" id="services">
        <div className="container-tz">
          <div className="section-head center">
            <span className="eyebrow">خدمات پژوهشی تزنویسه</span>
            <h2>هر مرحله از پروژه را با یک پل تخصصی جلو ببرید</h2>
            <p>
              نه خدمت اصلی، از پایان‌نامه تا مقاله؛ ساختار صفحه طوری است که از یک خدمت به
              خدمت بعدی روان حرکت کنید.
            </p>
          </div>
          <div className="services-grid">
            {SERVICES.map((s, i) => (
              <article key={s.to} className={`service-card tone-${(i % 9) + 1}`}>
                <div className="icon-box">
                  <FaIcon icon={FA[s.icon] || "fa-circle-dot"} />
                </div>
                <h3>{s.title}</h3>
                <p>{s.text}</p>
                <AppLink className="link-arrow" to={s.to}>
                  جزئیات خدمت
                </AppLink>
              </article>
            ))}
          </div>
        </div>
      </section>

      <Universities />

      <section className="section bg-soft">
        <div className="container-tz">
          <div className="reason-wrap">
            <div className="reason-panel">
              <span
                className="eyebrow"
                style={{
                  background: "rgba(255,255,255,.12)",
                  color: "#fff",
                  borderColor: "rgba(255,255,255,.2)",
                }}
              >
                درباره تزنویسه
              </span>
              <h3 className="mt-4 mb-3 text-3xl font-extrabold">پژوهش خوب فقط تحویل فایل نیست.</h3>
              <p className="text-white/75">
                تزنویسه با تمرکز بر کیفیت علمی، شفافیت مسیر و پاسخ‌گویی، دانشجویان و
                پژوهشگران را از انتخاب موضوع تا دفاع همراهی می‌کند.
              </p>
              <Link to="/about" className="btn-tz btn-light-tz btn-lg-tz mt-6 self-start">
                درباره ما
              </Link>
            </div>
            <div className="reason-list">
              {REASONS.map((r, i) => (
                <div key={r.title} className={`reason-item tone-${(i % 6) + 1}`}>
                  <div className="icon-box">
                    <FaIcon icon={FA[r.icon] || "fa-circle-dot"} />
                  </div>
                  <b>{r.title}</b>
                  <p>{r.text}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container-tz">
          <div className="section-head center">
            <span className="eyebrow">از کجا شروع کنم؟</span>
            <h2>شش قدم تا یک مسیر پژوهشی روشن</h2>
            <p>هر مرحله خروجی مشخص دارد؛ بنابراین همیشه می‌دانید قدم بعدی چیست.</p>
          </div>
          <div className="steps steps-6">
            {STEPS.map((s, i) => (
              <div key={s.title} className={`step tone-${(i % 6) + 1}`}>
                <div className="n">{faNum(i + 1)}</div>
                <h3>{s.title}</h3>
                <p>{s.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section bg-soft">
        <div className="container-tz">
          <div className="section-head">
            <div>
              <span className="eyebrow">تازه‌های مرکز دانش</span>
              <h2>مطالب جدید و کاربردی</h2>
              <p>راهنماهای کوتاه برای نگارش فصل‌ها، روش تحقیق و تحلیل آماری.</p>
            </div>
            <Link className="link-arrow" to="/blog">
              مشاهده همه مقالات
            </Link>
          </div>
          <div className="article-grid">
            {ARTICLES.slice(0, 3).map((a, i) => (
              <article key={a.slug} className={`article-card tone-${(i % 3) + 1}`}>
                <div className={`article-cover ${a.cover}`} />
                <div className="article-body">
                  <div className="article-meta">
                    <span>{a.dateFa}</span>
                    <span>{a.category}</span>
                  </div>
                  <h3>{a.title}</h3>
                  <p>{a.excerpt}</p>
                  <Link className="link-arrow" to="/blog/$slug" params={{ slug: a.slug }}>
                    مطالعه مقاله
                  </Link>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container-tz">
          <div className="section-head">
            <div>
              <span className="eyebrow">صدای متقاضیان</span>
              <h2>آنچه بعد از تحویل می‌شنویم</h2>
              <p>شفافیت زمان‌بندی و قابل‌دفاع بودن خروجی، پرتکرارترین نکته بازخورد است.</p>
            </div>
            <Link className="link-arrow" to="/testimonials">
              همه نظرات
            </Link>
          </div>
          <div className="quote-grid">
            {TESTIMONIALS.slice(0, 3).map((t, i) => (
              <blockquote key={t.name} className={`quote-card tone-${(i % 3) + 1}`}>
                <p>«{t.quote}»</p>
                <footer>
                  <b>{t.name}</b>
                  <span>{t.role}</span>
                </footer>
              </blockquote>
            ))}
          </div>
        </div>
      </section>

      <CtaBand />

      <section className="section section-sm">
        <div className="container-tz">
          <div className="seo-panel">
            <div className="text-xs font-extrabold text-brand">راهنمای انتخاب خدمت</div>
            <h2>راهنمای انتخاب خدمات پژوهشی تزنویسه</h2>
            <p>
              تزنویسه برای دانشجویانی طراحی شده که می‌خواهند مسیر پایان‌نامه، پروپوزال یا
              تحلیل آماری را با نظم بیشتر پیش ببرند. مشاوره اولیه کمک می‌کند قبل از شروع
              نگارش، وضعیت واقعی پروژه مشخص شود.
            </p>
            <p>
              در بخش مشاوره انجام پایان‌نامه، نیاز هر پروژه با توجه به رشته، مقطع و مرحله فعلی
              بررسی می‌شود. ساختار خدمات مرحله‌ای است تا هر بخش مستقل باشد اما ارتباط
              منطقی خود را حفظ کند.
            </p>
            {seoOpen ? (
              <>
                <p>
                  برای تحلیل آماری انتخاب نرم‌افزار به‌تنهایی تعیین‌کننده نیست. SPSS، R،
                  Python یا AMOS زمانی نتیجه قابل دفاع می‌سازند که روش تحلیل متناسب با نوع
                  داده و سوال پژوهش انتخاب شده باشد.
                </p>
                <p>
                  در بخش پروپوزال، تمرکز روی ارتباط میان عنوان، بیان مسئله، پیشینه، اهداف و
                  روش اجرا است. اگر این ارتباط از ابتدا شفاف باشد، احتمال بازنویسی‌های سنگین
                  کمتر می‌شود.
                </p>
              </>
            ) : null}
            <button
              type="button"
              className="seo-more-btn"
              aria-expanded={seoOpen}
              onClick={() => setSeoOpen((v) => !v)}
            >
              {seoOpen ? "نمایش کمتر" : "مشاهده بیشتر"}
            </button>
          </div>
        </div>
      </section>
    </>
  );
}
