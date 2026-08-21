import { Link } from "@tanstack/react-router";
import type { PageBlock } from "@/lib/content";
import { SERVICES, STEPS } from "@/lib/content";
import { mergePageBlock, usePageOverlay } from "@/lib/page-overlay";
import { splitFaqs } from "@/lib/page-copy";
import { AppLink } from "@/components/AppLink";
import { FaIcon } from "@/components/ui/FaIcon";
import { faNum } from "@/lib/format";
import { stripEmoji } from "@/lib/utils";
import { CheckGrid } from "./CheckGrid";
import { FaqGrid } from "./FaqGrid";
import { InquiryForm } from "./InquiryForm";
import { MoreContent } from "./MoreContent";
import { PageHero } from "./PageHero";
import { Universities } from "./Universities";

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
};

export function ServicePage({ page, fieldSlug }: { page: PageBlock; fieldSlug?: string }) {
  const overlay = usePageOverlay(fieldSlug ?? page.slug);
  const p = mergePageBlock(page, overlay);
  const split = splitFaqs(p.features);
  const features = split.features;
  const faqs = [...(p.faqs ?? []), ...split.faqs].filter(
    (f, i, arr) => arr.findIndex((x) => x.q === f.q) === i,
  );
  const ctaHref = p.ctaUrl?.startsWith("/") ? p.ctaUrl : "/inquiry";
  const related = SERVICES.filter((s) => s.to !== `/${page.slug}`).slice(0, 9);

  return (
    <>
      <PageHero eyebrow={p.eyebrow} title={p.title} lead={p.lead} />

      {features.length ? (
        <section className="section">
          <div className="container-tz">
            <div className="section-head center">
              <span className="eyebrow">چه می‌گیرید؟</span>
              <h2>ویژگی‌ها و خروجی‌ها</h2>
              <p>هر مورد یک خروجی مشخص است؛ نه شعار کلی.</p>
            </div>
            <CheckGrid items={features} />
            <div className="mt-8 flex flex-wrap justify-center gap-3">
              <AppLink to={ctaHref} className="btn-tz btn-primary-tz btn-lg-tz">
                {p.ctaText || "شروع مشاوره رایگان"}
              </AppLink>
              <Link to="/tools" className="btn-tz btn-light-tz btn-lg-tz">
                ابزارهای آنلاین
              </Link>
            </div>
          </div>
        </section>
      ) : null}

      <section className="section bg-soft">
        <div className="container-tz">
          <div className="section-head center">
            <span className="eyebrow">از کجا شروع کنم؟</span>
            <h2>شش قدم تا یک مسیر پژوهشی روشن</h2>
            <p>همین مسیر را برای این خدمت هم دنبال می‌کنیم.</p>
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

      {p.body?.length ? (
        <MoreContent paragraphs={p.body} />
      ) : null}

      <FaqGrid items={faqs} />

      <section className="section">
        <div className="container-tz grid items-start gap-10 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <span className="eyebrow">ثبت درخواست</span>
            <h2 className="mt-3 mb-4 text-3xl font-extrabold">موضوع را بفرستید تا مسیر مشخص شود</h2>
            <p className="text-muted mb-6">
              مشاوره اولیه رایگان است. رشته، مقطع و فایل‌های فعلی را بگویید تا پژوهشگر مناسب معرفی شود.
            </p>
            <div className="cta-band cta-band-inline">
              <div>
                <h2>برای شروع این خدمت آماده‌اید؟</h2>
                <p>زمان، هزینه و خروجی را شفاف می‌گوییم؛ بدون تعهد اولیه.</p>
              </div>
              <AppLink to={ctaHref} className="btn-tz btn-light-tz btn-lg-tz">
                {p.ctaText || "ثبت درخواست مشاوره"}
              </AppLink>
            </div>
          </div>
          <InquiryForm compact />
        </div>
      </section>

      <section className="section bg-soft" id="services">
        <div className="container-tz">
          <div className="section-head center">
            <span className="eyebrow">سایر خدمات</span>
            <h2>همین مسیر را برای خدمت بعدی ادامه دهید</h2>
          </div>
          <div className="services-grid">
            {related.map((s, i) => (
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

      <Universities compact />
    </>
  );
}
