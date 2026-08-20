import { useMemo, useState } from "react";
import { createFileRoute, Link } from "@tanstack/react-router";
import { ARTICLES, articleMinutes } from "@/lib/content";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/blog/")({ component: Blog });

function Blog() {
  const [cat, setCat] = useState("همه");
  const cats = useMemo(() => ["همه", ...Array.from(new Set(ARTICLES.map((a) => a.category)))], []);
  const list = cat === "همه" ? ARTICLES : ARTICLES.filter((a) => a.category === cat);
  const featured = ARTICLES[0];

  return (
    <>
      <PageHero
        eyebrow="مرکز دانش"
        title="راهنماها و آموزش‌های پژوهشی"
        lead="نگارش فصل‌ها، روش تحقیق و تحلیل آماری به زبان کاربردی."
      />
      <section className="section pt-8">
        <div className="container-tz">
          <article className="featured-post">
            <div className={`article-cover ${featured.cover}`} />
            <div className="article-body">
              <div className="article-meta">
                <span>{featured.dateFa}</span>
                <span>{featured.category}</span>
                <span>{articleMinutes(featured)} دقیقه</span>
              </div>
              <span className="cat-chip">{featured.category}</span>
              <h2>{featured.title}</h2>
              <p>{featured.excerpt}</p>
              <Link className="btn-tz btn-primary-tz" to="/blog/$slug" params={{ slug: featured.slug }}>
                ادامه مطلب
              </Link>
            </div>
          </article>
          <div className="mt-8 mb-6 flex flex-wrap gap-2">
            {cats.map((c) => (
              <button
                key={c}
                type="button"
                className={`btn-tz ${cat === c ? "btn-primary-tz" : "btn-light-tz"}`}
                onClick={() => setCat(c)}
              >
                {c}
              </button>
            ))}
          </div>
          <div className="article-grid">
            {list.map((a) => (
              <Link key={a.slug} to="/blog/$slug" params={{ slug: a.slug }} className="article-card">
                <div className={`article-cover ${a.cover}`} />
                <div className="article-body">
                  <div className="article-meta">
                    <span>{a.dateFa}</span>
                    <span>{a.category}</span>
                  </div>
                  <span className="cat-chip">{a.category}</span>
                  <h3>{a.title}</h3>
                  <p>{a.excerpt}</p>
                  <span className="btn-tz btn-light-tz">ادامه مطلب</span>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
