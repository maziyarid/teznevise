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
      <section className="section tz-story-visual">
        <div className="container-tz">
          <div className="tz-story-stats">
            <article><strong>+۸۵۰۰</strong><span>دانشجو همراهی‌شده</span></article>
            <article><strong>+۱۲۰</strong><span>رشته دانشگاهی</span></article>
            <article><strong>+۲۰</strong><span>مشاور متخصص</span></article>
            <article><strong>۹۸٪</strong><span>رضایت مسیر مشاوره</span></article>
          </div>
          <div className="prose-fa max-w-3xl mx-auto">
            <p>
              تزنویسه از مشاوره موضوع شروع شد؛ جایی که دانشجو می‌دانست چه می‌خواهد بنویسد اما مسیر از مسئله تا روش روشن نبود.
              کم‌کم تحلیل آماری، نگارش فصل‌ها و آمادگی دفاع به همان مسیر اضافه شد تا پروژه تکه‌تکه بین چند نفر رها نشود.
            </p>
          </div>
          <ol className="tz-story-rail">
            {TIMELINE.map((t) => (
              <li key={t.year}>
                <span className="tz-story-year">{t.year}</span>
                <h3>{t.title}</h3>
                <p>{t.text}</p>
              </li>
            ))}
          </ol>
          <blockquote className="tz-story-quote">
            <p>ما تضمین نتیجه علمی نمی‌دهیم؛ مسیر را شفاف می‌کنیم، روش را درست می‌چینیم و دانشجو را تا دفاع همراهی می‌کنیم.</p>
          </blockquote>
          <Link to="/about" className="link-arrow mt-10 inline-flex">
            درباره مأموریت تزنویسه
          </Link>
        </div>
      </section>
      <CtaBand />
    </>
  );
}