import { useEffect, useState } from "react";
import { createFileRoute, Link, notFound } from "@tanstack/react-router";
import { Clock, UserRound } from "lucide-react";
import { ARTICLES, articleMinutes } from "@/lib/content";
import { CtaBand } from "@/components/shared/CtaBand";
import { PostComments } from "@/components/blog/PostComments";
import { ShareBar } from "@/components/blog/ShareBar";

export const Route = createFileRoute("/blog/$slug")({
  loader: ({ params }) => {
    const article = ARTICLES.find((a) => a.slug === params.slug);
    if (!article) throw notFound();
    return article;
  },
  head: ({ loaderData }) => ({
    meta: [
      { title: loaderData ? `${loaderData.title} | تزنویسه` : "مقاله | تزنویسه" },
      { name: "description", content: loaderData?.excerpt ?? "" },
    ],
  }),
  component: Post,
});

function ReadProgress() {
  const [p, setP] = useState(0);
  useEffect(() => {
    const onScroll = () => {
      const max = document.documentElement.scrollHeight - window.innerHeight;
      setP(max > 0 ? Math.min(100, (window.scrollY / max) * 100) : 0);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);
  return <div className="read-progress" style={{ width: `${p}%` }} />;
}

function Post() {
  const a = Route.useLoaderData();
  const minutes = articleMinutes(a);
  const related = ARTICLES.filter((x) => x.slug !== a.slug && x.category === a.category).slice(0, 3);
  const fallback = related.length ? related : ARTICLES.filter((x) => x.slug !== a.slug).slice(0, 3);

  return (
    <>
      <ReadProgress />
      <section className="page-hero">
        <div className="container-tz max-w-3xl">
          <nav className="crumb">
            <Link to="/">خانه</Link>
            <span>/</span>
            <Link to="/blog">بلاگ</Link>
            <span>/</span>
            <span>{a.category}</span>
          </nav>
          <div className="article-meta mt-4">
            <span>{a.dateFa}</span>
            <span>{a.category}</span>
            <span className="inline-flex items-center gap-1">
              <Clock className="size-3.5" aria-hidden />
              {minutes} دقیقه مطالعه
            </span>
          </div>
          <h1>{a.title}</h1>
          <p>{a.excerpt}</p>
          <ShareBar slug={a.slug} title={a.title} />
          <div className="mt-5 flex items-center gap-3 text-sm font-bold text-muted">
            <span className="grid size-10 place-items-center rounded-full bg-brand/10 text-brand">
              <UserRound className="size-5" aria-hidden />
            </span>
            <span>
              {a.author}
              <small className="mt-0.5 block font-medium">{a.authorRole}</small>
            </span>
          </div>
        </div>
      </section>
      <div className={`post-cover ${a.cover}`} aria-hidden />
      <section className="section pt-8">
        <div className="container-tz grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_260px]">
          <article className="prose-fa max-w-3xl">
            {a.sections.map((s, i) => (
              <section key={s.heading} id={`s-${i}`}>
                <h2>{s.heading}</h2>
                {s.paragraphs.map((p) => (
                  <p key={p.slice(0, 40)}>{p}</p>
                ))}
              </section>
            ))}
          </article>
          <aside className="toc-card">
            <details className="toc-mobile" open>
              <summary className="eyebrow">در این مقاله</summary>
              <nav className="toc" aria-label="فهرست مقاله">
                {a.sections.map((s, i) => (
                  <a key={s.heading} href={`#s-${i}`}>
                    {s.heading}
                  </a>
                ))}
              </nav>
            </details>
          </aside>
        </div>
      </section>
      <section className="section pt-0">
        <div className="container-tz max-w-3xl">
          <PostComments slug={a.slug} />
        </div>
      </section>
      <section className="section bg-soft">
        <div className="container-tz">
          <div className="section-head">
            <div>
              <span className="eyebrow">ادامه مطالعه</span>
              <h2>مقالات مرتبط</h2>
            </div>
            <Link to="/blog" className="link-arrow">
              همه مقالات
            </Link>
          </div>
          <div className="article-grid">
            {fallback.map((r) => (
              <Link key={r.slug} to="/blog/$slug" params={{ slug: r.slug }} className="article-card">
                <div className={`article-cover ${r.cover}`} />
                <div className="article-body">
                  <div className="article-meta">
                    <span>{r.dateFa}</span>
                    <span>{r.category}</span>
                  </div>
                  <h3>{r.title}</h3>
                  <p>{r.excerpt}</p>
                  <span className="link-arrow">مطالعه مقاله</span>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
      <CtaBand
        title="برای همین بخش از پایان‌نامه کمک می‌خواهید؟"
        text="موضوع و مرحله فعلی را بفرستید تا مسیر نگارش یا تحلیل مشخص شود."
      />
    </>
  );
}
