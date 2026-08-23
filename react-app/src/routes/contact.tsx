import { createFileRoute } from "@tanstack/react-router";
import { SITE } from "@/lib/site";
import { InquiryForm } from "@/components/shared/InquiryForm";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/contact")({ component: Contact });

function Contact() {
  return (
    <>
      <PageHero
        eyebrow="تماس با ما"
        title="همین حالا مسیر پروژه‌تان را شروع کنید"
        lead="تماس تلفنی، پیام‌رسان‌ها یا فرم مشاوره — پاسخ در ساعات کاری."
      />
      <section className="section">
        <div className="container-tz">
          <div className="tz-contact-cards">
            <a href={`tel:${SITE.phoneIntl}`} className="tz-contact-card">
              <div className="icon-box icon-teal" />
              <b>تلفن</b>
              <p>{SITE.phoneDisplay}</p>
            </a>
            <a href={SITE.whatsapp} className="tz-contact-card" target="_blank" rel="noopener noreferrer">
              <div className="icon-box icon-indigo" />
              <b>واتساپ</b>
              <p>شروع گفتگو</p>
            </a>
            <a href={`mailto:${SITE.email}`} className="tz-contact-card">
              <div className="icon-box icon-cyan" />
              <b>ایمیل</b>
              <p>{SITE.email}</p>
            </a>
            <div className="tz-contact-card">
              <div className="icon-box icon-amber" />
              <b>ساعات پاسخ‌گویی</b>
              <p>{SITE.hours}</p>
            </div>
          </div>
          <div className="mt-10 max-w-xl mx-auto">
            <InquiryForm />
          </div>
        </div>
      </section>
    </>
  );
}