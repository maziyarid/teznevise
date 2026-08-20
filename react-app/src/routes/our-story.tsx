import { createFileRoute, Link } from "@tanstack/react-router";
import { TIMELINE } from "@/lib/site-extra";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/our-story")({ component: OurStory });

function OurStory() {
  const base = STATIC_PAGES["our-story"];
  const overlay = usePageOverlay("our-story");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz max-w-3xl">
          <div className="prose-fa">
            <p>
              تزنویسه از مشاوره موضوع شروع شد؛ جایی که دانشجو می‌دانست چه می‌خواهد بنویسد اما مسیر از مسئله تا روش روشن نبود.
              کم‌کم تحلیل آماری، نگارش فصل‌ها و آمادگی دفاع به همان مسیر اضافه شد تا پروژه تکه‌تکه بین چند نفر رها نشود.
            </p>
          </div>
          <ol className="timeline mt-10">
            {TIMELINE.map((t) => (
              <li key={t.year}>
                <span className="timeline-year">{t.year}</span>
                <div>
                  <h3>{t.title}</h3>
                  <p>{t.text}</p>
                </div>
              </li>
            ))}
          </ol>
          <Link to="/about" className="link-arrow mt-10 inline-flex">
            درباره مأموریت تزنویسه
          </Link>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
