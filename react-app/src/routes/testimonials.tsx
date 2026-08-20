import { createFileRoute, Link } from "@tanstack/react-router";
import { TESTIMONIALS } from "@/lib/site-extra";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/testimonials")({ component: TestimonialsPage });

function TestimonialsPage() {
  const base = STATIC_PAGES.testimonials;
  const overlay = usePageOverlay("testimonials");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz quote-grid">
          {TESTIMONIALS.map((t) => (
            <blockquote key={t.name} className="quote-card">
              <p>«{t.quote}»</p>
              <footer>
                <b>{t.name}</b>
                <span>{t.role}</span>
              </footer>
            </blockquote>
          ))}
        </div>
        <div className="container-tz mt-10 text-center">
          <Link to="/case-studies" className="link-arrow">
            نمونه‌هایی از مسیر پروژه‌ها
          </Link>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
