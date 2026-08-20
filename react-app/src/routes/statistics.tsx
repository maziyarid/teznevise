import { createFileRoute, Link } from "@tanstack/react-router";
import { SERVICE_PAGES } from "@/lib/content";
import { mergePageBlock, usePageOverlay } from "@/lib/page-overlay";
import { CheckGrid } from "@/components/shared/CheckGrid";
import { CtaBand } from "@/components/shared/CtaBand";
import { InquiryForm } from "@/components/shared/InquiryForm";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/statistics")({ component: Statistics });

const SOFT = [
  { title: "آمار عمومی", items: ["SPSS", "R", "Python", "Stata", "Minitab"] },
  { title: "مدل‌سازی ساختاری", items: ["AMOS", "SmartPLS", "LISREL", "Mplus"] },
  { title: "اقتصادسنجی", items: ["EViews", "RATS", "GAUSS"] },
];

function Statistics() {
  const overlay = usePageOverlay("statistics");
  const page = mergePageBlock(SERVICE_PAGES.statistics, overlay);
  return (
    <>
      <PageHero eyebrow={page.eyebrow} title={page.title} lead={page.lead} />
      <section className="section">
        <div className="container-tz">
          <span className="eyebrow">پوشش تحلیل</span>
          <h2 className="mt-3 mb-6 text-3xl font-extrabold">خروجی‌هایی که دریافت می‌کنید</h2>
          <CheckGrid items={page.features} />
        </div>
      </section>
      <section className="section bg-soft">
        <div className="container-tz grid items-start gap-10 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <span className="eyebrow">نرم‌افزارها</span>
            <h2 className="mt-3 mb-6 text-3xl font-extrabold">نرم‌افزار متناسب با روش شما</h2>
            <div className="grid gap-4 sm:grid-cols-3">
              {SOFT.map((g) => (
                <article key={g.title} className="service-card">
                  <h3>{g.title}</h3>
                  <p>{g.items.join("، ")}</p>
                </article>
              ))}
            </div>
            <Link to="/tools" className="link-arrow mt-6 inline-flex">
              ابزارهای رایگان آماری
            </Link>
          </div>
          <InquiryForm compact />
        </div>
      </section>
      <CtaBand />
    </>
  );
}
