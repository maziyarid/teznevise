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
    <section className="page-hero">
      <div className="container-tz page-hero-inner">
        {eyebrow ? <span className="eyebrow">{stripEmoji(eyebrow)}</span> : null}
        <h1>{stripEmoji(title)}</h1>
        {lead ? <p>{stripEmoji(lead)}</p> : null}
      </div>
    </section>
  );
}
