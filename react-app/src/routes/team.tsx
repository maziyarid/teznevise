import { createFileRoute, Link } from "@tanstack/react-router";
import { TEAM } from "@/lib/site-extra";
import { usePageOverlay } from "@/lib/page-overlay";
import { STATIC_PAGES } from "@/lib/content";
import { CtaBand } from "@/components/shared/CtaBand";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/team")({ component: TeamPage });

function TeamPage() {
  const base = STATIC_PAGES.team;
  const overlay = usePageOverlay("team");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz">
          <div className="section-head">
            <div>
              <span className="eyebrow">پژوهشگر متناسب رشته شما</span>
              <h2>تیمی که مسیر را با شما جلو می‌برد</h2>
              <p>پس از بررسی موضوع، مقطع و روش، نزدیک‌ترین تخصص به پروژه شما انتخاب می‌شود.</p>
            </div>
          </div>
          <div className="people-grid">
            {TEAM.map((m) => (
              <article key={m.name} className="person-card">
                <div className="person-avatar" aria-hidden>
                  {m.name.split(" ").slice(-1)[0]?.slice(0, 1)}
                </div>
                <h3>{m.name}</h3>
                <p className="person-role">{m.role}</p>
                <p>{m.bio}</p>
                <ul className="tag-row">
                  {m.fields.map((f) => (
                    <li key={f}>{f}</li>
                  ))}
                </ul>
              </article>
            ))}
          </div>
        </div>
      </section>
      <section className="section bg-soft">
        <div className="container-tz grid gap-6 md:grid-cols-3">
          {[
            { t: "رشته و مقطع", d: "اولویت با تجربه نزدیک به حوزه شماست، نه پژوهشگر عمومی." },
            { t: "روش تحقیق", d: "کمی، کیفی یا ترکیبی از ابتدا مشخص می‌شود تا مسیر عوض نشود." },
            { t: "مرحله فعلی", d: "موضوع، پروپوزال، فصل یا تحلیل — نقطه ورود مسیر را تعیین می‌کند." },
          ].map((s) => (
            <article key={s.t} className="service-card">
              <h3>{s.t}</h3>
              <p>{s.d}</p>
            </article>
          ))}
        </div>
        <div className="container-tz mt-10 flex flex-wrap gap-3">
          <Link to="/inquiry" className="btn-tz btn-primary-tz btn-lg-tz">
            درخواست پژوهشگر متخصص
          </Link>
          <Link to="/join-us" className="btn-tz btn-light-tz btn-lg-tz">
            همکاری با تیم
          </Link>
        </div>
      </section>
      <CtaBand />
    </>
  );
}
