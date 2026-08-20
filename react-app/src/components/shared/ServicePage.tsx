import { Link } from "@tanstack/react-router";
import type { PageBlock } from "@/lib/content";
import { mergePageBlock, usePageOverlay } from "@/lib/page-overlay";
import { AppLink } from "@/components/AppLink";
import { CheckGrid } from "./CheckGrid";
import { CtaBand } from "./CtaBand";
import { InquiryForm } from "./InquiryForm";
import { PageHero } from "./PageHero";
import { Universities } from "./Universities";

export function ServicePage({ page, fieldSlug }: { page: PageBlock; fieldSlug?: string }) {
  const overlay = usePageOverlay(fieldSlug ?? page.slug);
  const p = mergePageBlock(page, overlay);
  const ctaHref = p.ctaUrl?.startsWith("/") ? p.ctaUrl : "/inquiry";

  return (
    <>
      <PageHero eyebrow={p.eyebrow} title={p.title} lead={p.lead} />
      <section className="section">
        <div className="container-tz grid items-start gap-10 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <span className="eyebrow">چه می‌گیرید؟</span>
            <h2 className="mt-3 mb-6 text-3xl font-extrabold">ویژگی‌ها و خروجی‌ها</h2>
            <CheckGrid items={p.features} />
            <div className="mt-8 flex flex-wrap gap-3">
              <AppLink to={ctaHref} className="btn-tz btn-primary-tz btn-lg-tz">
                {p.ctaText || "شروع مشاوره رایگان"}
              </AppLink>
              <Link to="/tools" className="btn-tz btn-light-tz btn-lg-tz">
                ابزارهای آنلاین
              </Link>
            </div>
          </div>
          <InquiryForm compact />
        </div>
      </section>
      {p.body?.length ? (
        <section className="section bg-soft">
          <div className="container-tz max-w-3xl prose-fa">
            {p.body.map((paragraph) => (
              <p key={paragraph.slice(0, 24)}>{paragraph}</p>
            ))}
          </div>
        </section>
      ) : null}
      <Universities compact />
      <CtaBand />
    </>
  );
}
