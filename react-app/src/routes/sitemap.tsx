import { createFileRoute, Link } from "@tanstack/react-router";
import { FOOTER_NAV, FOOTER_QUICK, FOOTER_SERVICES, PRIMARY_NAV } from "@/lib/site";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/sitemap")({ component: Sitemap });

function Sitemap() {
  const groups = [
    { title: "اصلی", items: PRIMARY_NAV.map((n) => ({ label: n.label, to: n.to })) },
    { title: "خدمات", items: [...FOOTER_SERVICES] },
    { title: "سازمان", items: [...FOOTER_NAV, ...FOOTER_QUICK] },
    {
      title: "قوانین",
      items: [
        { label: "حریم خصوصی", to: "/privacy" },
        { label: "شرایط استفاده", to: "/terms" },
        { label: "سیاست کوکی", to: "/cookies" },
        { label: "بازگشت وجه", to: "/refund" },
        { label: "آیین‌نامه پژوهشی", to: "/rules" },
        { label: "استفاده منصفانه", to: "/fair-use-policy" },
        { label: "تضمین اصالت", to: "/originality-guarantee" },
        { label: "سیاست بازنگری", to: "/revision-policy" },
        { label: "تعهدات موسسه", to: "/service-commitments" },
        { label: "مسئولیت اجتماعی", to: "/corporate-social-responsibility" },
      ],
    },
    { title: "دانلودها", items: [{ label: "همه دانلودها", to: "/downloads" }] },
  ];
  return (
    <>
      <PageHero eyebrow="نقشه سایت" title="همه صفحات تزنویسه" lead="دسترسی سریع به بخش‌های سایت." />
      <section className="section">
        <div className="container-tz grid gap-8 md:grid-cols-3">
          {groups.map((g) => (
            <div key={g.title}>
              <h2 className="mb-4 text-xl font-extrabold">{g.title}</h2>
              {g.items.map((i) => (
                <Link key={i.to} to={i.to} className="mb-2 block text-muted hover:text-brand">
                  {i.label}
                </Link>
              ))}
            </div>
          ))}
        </div>
      </section>
    </>
  );
}
