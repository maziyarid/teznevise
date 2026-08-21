import { PageHero } from "./PageHero";
import type { LegalPage } from "@/lib/legal";
import { usePageOverlay } from "@/lib/page-overlay";
import { stripEmoji } from "@/lib/utils";
import { faNum } from "@/lib/format";

export function LegalView({ page, fieldSlug }: { page: LegalPage; fieldSlug?: string }) {
  const overlay = usePageOverlay(fieldSlug ?? page.slug);
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
        <div className="container-tz faq-grid" style={{ gridTemplateColumns: "repeat(2, minmax(0,1fr))" }}>
          {page.sections.map((s, i) => (
            <article key={s.title} className={`faq-card tone-${(i % 9) + 1}`}>
              <span className="faq-num">{faNum(i + 1)}</span>
              <h3>{stripEmoji(s.title)}</h3>
              <p>{stripEmoji(s.body)}</p>
            </article>
          ))}
          {extra?.length
            ? extra.map((p, i) => (
                <p key={p.slice(0, 24)} className="text-muted">
                  {stripEmoji(p)}
                </p>
              ))
            : null}
        </div>
      </section>
    </>
  );
}