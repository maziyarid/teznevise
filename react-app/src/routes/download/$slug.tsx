import { createFileRoute, Link, notFound } from "@tanstack/react-router";
import { DOWNLOADS } from "@/lib/imported-pages";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/download/$slug")({
  loader: ({ params }) => {
    const item = DOWNLOADS.find((d) => d.slug === params.slug);
    if (!item) throw notFound();
    return item;
  },
  head: ({ loaderData }) => ({
    meta: [
      { title: loaderData ? `${loaderData.title} | تزنویسه` : "دانلود" },
      { name: "description", content: loaderData?.excerpt || "" },
    ],
  }),
  component: DownloadPage,
});

function DownloadPage() {
  const item = Route.useLoaderData();
  return (
    <>
      <PageHero eyebrow={item.source} title={item.title} lead={item.excerpt} />
      <section className="section">
        <div className="container-tz grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
          <div className="prose-fa max-w-3xl space-y-6">
            {item.sections.map((s) => (
              <article key={s.title}>
                <h2 className="text-2xl font-extrabold">{s.title}</h2>
                {s.body.split("\n").map((p) => (
                  <p key={p.slice(0, 40)} className="text-muted">{p}</p>
                ))}
              </article>
            ))}
          </div>
          <aside className="surface-card">
            <p className="eyebrow">فایل‌ها</p>
            <ul className="mt-4 space-y-3">
              {item.files.map((f) => (
                <li key={f.url}>
                  <a href={f.url} className="btn-tz btn-primary-tz w-full" download>
                    {f.text}
                  </a>
                  <p className="mt-1 text-xs text-muted">
                    {f.ext.toUpperCase()} · {f.size}
                  </p>
                </li>
              ))}
            </ul>
            <p className="mt-4 text-sm text-muted">
              مجوز: {item.license} · زبان: {item.lang} · نسخه {item.version}
            </p>
            <Link to="/inquiry" className="btn-tz btn-light-tz mt-4">
              نیاز به کمک در تکمیل فرم دارید؟
            </Link>
          </aside>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
