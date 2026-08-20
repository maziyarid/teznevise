import { createFileRoute, Link, notFound } from "@tanstack/react-router";
import { CASE_STUDIES } from "@/lib/imported-pages";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/case-study/$slug")({
  loader: ({ params }) => {
    const item = CASE_STUDIES.find((c) => c.slug === params.slug);
    if (!item) throw notFound();
    return item;
  },
  head: ({ loaderData }) => ({
    meta: [
      { title: loaderData ? `${loaderData.title} | تزنویسه` : "مطالعه موردی" },
      { name: "description", content: loaderData?.excerpt || "" },
    ],
  }),
  component: CasePage,
});

function CasePage() {
  const c = Route.useLoaderData();
  const blocks = [
    { title: "چالش", body: c.challenge },
    { title: "راه‌حل", body: c.solution },
    { title: "نتیجه", body: c.result },
  ];
  return (
    <>
      <PageHero eyebrow={`${c.icon} ${c.field}`} title={c.title} lead={c.excerpt} />
      <section className="section">
        <div className="container-tz">
          <dl className="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {[
              ["متقاضی", c.client],
              ["مقطع", c.degree],
              ["خدمت", c.service],
              ["مدت", c.duration],
              ["منطقه", c.region],
              ["ابزار", c.tools],
            ].map(([k, v]) => (
              <div key={k} className="surface-card">
                <dt className="text-xs font-bold text-muted">{k}</dt>
                <dd className="mt-1 font-bold">{v}</dd>
              </div>
            ))}
          </dl>
          <div className="grid gap-6">
            {blocks.map((b) => (
              <article key={b.title} className="service-card">
                <h2>{b.title}</h2>
                {b.body.split(/\n+/).filter(Boolean).map((p) => (
                  <p key={p.slice(0, 48)}>{p}</p>
                ))}
              </article>
            ))}
          </div>
          {c.quote ? (
            <blockquote className="surface-card mt-8 text-lg leading-9">
              {c.quote}
            </blockquote>
          ) : null}
          <div className="mt-8">
            <Link to="/inquiry" className="btn-tz btn-primary-tz btn-lg-tz">
              پروژه مشابه را مطرح کنید
            </Link>
          </div>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
