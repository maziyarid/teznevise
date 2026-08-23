import type { ReactNode } from "react";
import { stripEmoji } from "@/lib/utils";

export function PageHero({
  eyebrow,
  title,
  lead,
  aside,
}: {
  eyebrow: string;
  title: string;
  lead: string;
  aside?: ReactNode;
}) {
  return (
    <section className={`page-hero tz-hero-split${aside ? " page-hero--split" : ""}`}>
      <div className="container-tz tz-hero-split__grid">
        <div className="page-hero-inner page-hero-text">
          {eyebrow ? <span className="eyebrow">{stripEmoji(eyebrow)}</span> : null}
          <h1>{stripEmoji(title)}</h1>
          {lead ? <p className="page-hero-lead">{stripEmoji(lead)}</p> : null}
        </div>
        <div className="page-hero-aside" aria-hidden={aside ? undefined : true}>
          {aside ?? (
            <div className="hero-visual tz-hero-orbit">
              <div className="hero-orb" />
              <a className="hero-order" href="/inquiry">ثبت سفارش</a>
              <span className="orbit-tag t1">SPSS</span>
              <span className="orbit-tag t2">Matlab</span>
              <span className="orbit-tag t3">پایان‌نامه</span>
              <span className="orbit-tag t4">پروژه دانشگاهی</span>
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
