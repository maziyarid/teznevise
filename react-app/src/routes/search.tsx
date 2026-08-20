import { useMemo, useState } from "react";
import { createFileRoute } from "@tanstack/react-router";
import { ARTICLES, SERVICES, TOOLS } from "@/lib/content";
import { PRIMARY_NAV } from "@/lib/site";
import { AppLink } from "@/components/AppLink";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/search")({
  validateSearch: (s: Record<string, unknown>) => ({ q: typeof s.q === "string" ? s.q : "" }),
  component: SearchPage,
});

function SearchPage() {
  const { q: initial } = Route.useSearch();
  const [q, setQ] = useState(initial);
  const results = useMemo(() => {
    const s = q.trim();
    const pages = [
      ...SERVICES.map((x) => ({ title: x.title, to: x.to, kind: "خدمت" })),
      ...TOOLS.map((x) => ({ title: x.title, to: `/tools/${x.slug}`, kind: "ابزار" })),
      ...ARTICLES.map((x) => ({ title: x.title, to: `/blog/${x.slug}`, kind: "مقاله" })),
      ...PRIMARY_NAV.map((x) => ({ title: x.label, to: x.to, kind: "صفحه" })),
    ];
    if (!s) return pages.slice(0, 8);
    return pages.filter((p) => p.title.includes(s));
  }, [q]);

  return (
    <>
      <PageHero eyebrow="جستجو" title="جستجو در تزنویسه" lead="خدمات، ابزارها و مقالات را پیدا کنید." />
      <section className="section">
        <div className="container-tz max-w-2xl">
          <div className="field">
            <label htmlFor="q">عبارت جستجو</label>
            <input id="q" value={q} onChange={(e) => setQ(e.target.value)} placeholder="مثلاً SPSS یا فصل اول" />
          </div>
          <div className="mt-6 space-y-2">
            {results.map((r) => (
              <AppLink key={r.to + r.title} to={r.to} className="service-card block">
                <span className="text-xs font-bold text-brand">{r.kind}</span>
                <h3 className="mt-1">{r.title}</h3>
              </AppLink>
            ))}
            {results.length === 0 ? <p className="text-muted">نتیجه‌ای پیدا نشد.</p> : null}
          </div>
        </div>
      </section>
    </>
  );
}
