import { PageHero } from "./PageHero";
import type { LegalPage } from "@/lib/legal";

export function LegalView({ page }: { page: LegalPage }) {
  return (
    <>
      <PageHero eyebrow={page.eyebrow} title={page.title} lead={page.lead} />
      <section className="section">
        <div className="container-tz max-w-3xl space-y-8">
          {page.sections.map((s) => (
            <article key={s.title}>
              <h2 className="text-2xl font-extrabold">{s.title}</h2>
              <p className="text-muted">{s.body}</p>
            </article>
          ))}
        </div>
      </section>
    </>
  );
}
