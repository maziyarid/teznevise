import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "./CtaBand";
import { PageHero } from "./PageHero";

export function StaticPageView({ slug }: { slug: keyof typeof STATIC_PAGES }) {
  const page = STATIC_PAGES[slug];
  const overlay = usePageOverlay(slug);
  const title = overlay?.title?.trim() || page.title;
  const lead = overlay?.lead?.trim() || page.lead;
  const eyebrow = overlay?.eyebrow?.trim() || page.eyebrow;
  const extra = overlay?.body
    ?.split(/\n{2,}/)
    .map((s) => s.trim())
    .filter(Boolean);

  return (
    <>
      <PageHero eyebrow={eyebrow} title={title} lead={lead} />
      <section className="section">
        <div className="container-tz grid gap-6 md:grid-cols-2">
          {page.sections.map((s) => (
            <article key={s.title} className="service-card">
              <h3>{s.title}</h3>
              <p>{s.body}</p>
            </article>
          ))}
        </div>
        {extra?.length ? (
          <div className="container-tz prose-fa mt-10 max-w-3xl">
            {extra.map((p) => (
              <p key={p.slice(0, 24)}>{p}</p>
            ))}
          </div>
        ) : null}
      </section>
      <CtaBand />
    </>
  );
}
