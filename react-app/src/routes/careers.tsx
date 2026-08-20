import { createFileRoute, Link } from "@tanstack/react-router";
import { JOBS } from "@/lib/site-extra";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/careers")({ component: CareersPage });

function CareersPage() {
  const base = STATIC_PAGES.careers;
  const overlay = usePageOverlay("careers");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz grid gap-5">
          {JOBS.map((j) => (
            <article key={j.title} className="job-card">
              <div>
                <span className="eyebrow">{j.type}</span>
                <h3 className="mt-2">{j.title}</h3>
                <p>{j.text}</p>
              </div>
              <Link to="/contact" className="btn-tz btn-primary-tz">
                ارسال رزومه
              </Link>
            </article>
          ))}
        </div>
      </section>
      <CtaBand title="نمونه کار دارید؟" text="رزومه و حوزه تخصصی را از فرم تماس بفرستید تا بررسی اولیه انجام شود." />
    </>
  );
}
