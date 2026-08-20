import { createFileRoute, Link } from "@tanstack/react-router";
import { DOWNLOADS } from "@/lib/imported-pages";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/downloads")({
  head: () => ({
    meta: [{ title: "دانلودها | تزنویسه" }, { name: "description", content: STATIC_PAGES.downloads.lead }],
  }),
  component: DownloadsPage,
});

function DownloadsPage() {
  const base = STATIC_PAGES.downloads;
  const overlay = usePageOverlay("downloads");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz grid gap-6">
          {DOWNLOADS.map((d) => (
            <article key={d.slug} className="service-card">
              <p className="eyebrow">{d.source}</p>
              <h2 className="mt-2">{d.title}</h2>
              <p>{d.excerpt}</p>
              <p className="text-xs text-muted">
                {d.license} · {d.lang} · نسخه {d.version}
              </p>
              <Link to="/download/$slug" params={{ slug: d.slug }} className="link-arrow mt-3 inline-flex">
                مشاهده و دانلود
              </Link>
            </article>
          ))}
        </div>
      </section>
      <CtaBand />
    </>
  );
}
