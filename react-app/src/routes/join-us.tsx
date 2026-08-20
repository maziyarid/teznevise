import { createFileRoute, Link } from "@tanstack/react-router";
import { JOBS } from "@/lib/site-extra";
import { STATIC_PAGES } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { InquiryForm } from "@/components/shared/InquiryForm";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/join-us")({ component: JoinUs });

function JoinUs() {
  const base = STATIC_PAGES["join-us"];
  const overlay = usePageOverlay("join-us");
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || base.eyebrow}
        title={overlay?.title || base.title}
        lead={overlay?.lead || base.lead}
      />
      <section className="section">
        <div className="container-tz grid items-start gap-10 lg:grid-cols-[1.05fr_0.95fr]">
          <div>
            <span className="eyebrow">مسیر همکاری</span>
            <h2 className="mt-3 mb-4 text-3xl font-extrabold">شبکه پژوهشگران در حال گسترش است</h2>
            <ol className="steps join-steps">
              <li className="step">
                <div className="n">۰۱</div>
                <h3>ارسال رزومه</h3>
                <p>حوزه تخصصی، مقطع و نمونه کار را از فرم روبه‌رو بفرستید.</p>
              </li>
              <li className="step">
                <div className="n">۰۲</div>
                <h3>بررسی اولیه</h3>
                <p>نزدیکی موضوع، کیفیت نگارش و ظرفیت همکاری سنجیده می‌شود.</p>
              </li>
              <li className="step">
                <div className="n">۰۳</div>
                <h3>پروژه آزمایشی</h3>
                <p>یک بخش کوتاه برای هماهنگی لحن و استاندارد تزنویسه.</p>
              </li>
            </ol>
            <ul className="mt-8 space-y-3">
              {JOBS.map((j) => (
                <li key={j.title} className="service-card">
                  <h3>{j.title}</h3>
                  <p>{j.text}</p>
                </li>
              ))}
            </ul>
            <Link to="/careers" className="link-arrow mt-6 inline-flex">
              مشاهده موقعیت‌ها
            </Link>
          </div>
          <InquiryForm />
        </div>
      </section>
    </>
  );
}
