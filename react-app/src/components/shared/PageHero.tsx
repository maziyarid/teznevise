import { stripEmoji } from "@/lib/utils";

export function PageHero({
  eyebrow,
  title,
  lead,
}: {
  eyebrow: string;
  title: string;
  lead: string;
}) {
  return (
    <section className="page-hero tz-hero-split">
      <div className="container-tz tz-hero-split__grid">
        <div className="page-hero-inner">
          {eyebrow ? <span className="eyebrow">{stripEmoji(eyebrow)}</span> : null}
          <h1>{stripEmoji(title)}</h1>
          {lead ? <p>{stripEmoji(lead)}</p> : null}
        </div>
        <div className="hero-visual tz-hero-orbit" aria-hidden>
          <div className="hero-orb" />
          <a className="hero-order" href="/inquiry">ثبت سفارش</a>
          <span className="orbit-tag t1">SPSS</span>
          <span className="orbit-tag t2">Matlab</span>
          <span className="orbit-tag t3">پایان‌نامه</span>
          <span className="orbit-tag t4">پروژه دانشگاهی</span>
        </div>
      </div>
    </section>
  );
}