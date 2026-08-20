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
      <div className="container-tz">
        <span className="eyebrow">{eyebrow}</span>
        <h1>{title}</h1>
        <p>{lead}</p>
      </div>
    </section>
  );
}
