import { createFileRoute, Link } from "@tanstack/react-router";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/about")({ component: About });

function About() {
  const page = STATIC_PAGES.about;
  const overlay = usePageOverlay("about");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || page.eyebrow}
        title={overlay?.title || page.title}
        lead={overlay?.lead || page.lead}
      />
      <section className="section">
        <div className="container-tz grid gap-6 md:grid-cols-3">
          {page.sections.map((s) => (
            <article key={s.title} className="service-card">
              <h3>{s.title}</h3>
              <p>{s.body}</p>
            </article>
          ))}
        </div>
        <div className="container-tz mt-10 flex flex-wrap justify-center gap-3">
          <Link to="/team" className="btn-tz btn-primary-tz btn-lg-tz">
            مشاهده تیم پژوهشگران
          </Link>
          <Link to="/our-story" className="btn-tz btn-light-tz btn-lg-tz">
            داستان تزنویسه
          </Link>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
