import { createFileRoute, Link } from "@tanstack/react-router";
import { CASES } from "@/lib/site-extra";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/case-studies")({ component: CasesPage });

function CasesPage() {
  const base = STATIC_PAGES["case-studies"];
  const overlay = usePageOverlay("case-studies");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz grid gap-6">
          {CASES.map((c, i) => (
            <article key={c.slug} className="case-card" id={c.slug}>
              <span className="case-index">۰{i + 1}</span>
              <div>
                <p className="eyebrow">{c.field}</p>
                <h2>{c.title}</h2>
                <div className="case-grid">
                  <div>
                    <h3>چالش</h3>
                    <p>{c.challenge}</p>
                  </div>
                  <div>
                    <h3>روش</h3>
                    <p>{c.method}</p>
                  </div>
                  <div>
                    <h3>نتیجه</h3>
                    <p>{c.result}</p>
                  </div>
                </div>
              </div>
            </article>
          ))}
        </div>
        <div className="container-tz mt-10">
          <Link to="/inquiry" className="btn-tz btn-primary-tz btn-lg-tz">
            پروژه مشابه را مطرح کنید
          </Link>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
