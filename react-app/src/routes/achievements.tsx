import { createFileRoute } from "@tanstack/react-router";
import { ACHIEVEMENT_STATS, TIMELINE } from "@/lib/site-extra";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/achievements")({ component: AchievementsPage });

function AchievementsPage() {
  const base = STATIC_PAGES.achievements;
  const overlay = usePageOverlay("achievements");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz stats-grid">
          {ACHIEVEMENT_STATS.map((s) => (
            <article key={s.label} className="stat-card">
              <b>{s.value}</b>
              <span>{s.label}</span>
            </article>
          ))}
        </div>
      </section>
      <section className="section bg-soft">
        <div className="container-tz max-w-3xl">
          <span className="eyebrow">خط زمانی</span>
          <h2 className="mt-3 mb-8 text-3xl font-extrabold">چطور تا اینجا رسیدیم</h2>
          <ol className="timeline">
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
        </div>
      </section>
      <CtaBand />
    </>
  );
}
