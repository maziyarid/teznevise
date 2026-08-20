import { Link } from "@tanstack/react-router";

export function NotFound() {
  return (
    <section className="page-hero">
      <div className="container-tz py-16 text-center">
        <span className="eyebrow">۴۰۴</span>
        <h1>صفحه پیدا نشد</h1>
        <p>این مسیر در تزنویسه وجود ندارد یا جابه‌جا شده است.</p>
        <Link to="/" className="btn-tz btn-primary-tz btn-lg-tz mt-6 inline-flex">
          بازگشت به خانه
        </Link>
      </div>
    </section>
  );
}
