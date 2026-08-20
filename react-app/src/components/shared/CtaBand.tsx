import { Link } from "@tanstack/react-router";

export function CtaBand({
  title = "پروژه پژوهشی‌ات را همین امروز شروع کن",
  text = "موضوع را بفرست؛ کارشناسان تزنویسه مسیر، زمان و برآورد اولیه را با شما بررسی می‌کنند.",
  action = "درخواست مشاوره رایگان",
}: {
  title?: string;
  text?: string;
  action?: string;
}) {
  return (
    <section className="section">
      <div className="container-tz">
        <div className="cta-band">
          <div>
            <h2>{title}</h2>
            <p>{text}</p>
          </div>
          <Link to="/inquiry" className="btn-tz btn-light-tz btn-lg-tz">
            {action}
          </Link>
        </div>
      </div>
    </section>
  );
}
