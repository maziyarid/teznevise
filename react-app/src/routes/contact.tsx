import { createFileRoute } from "@tanstack/react-router";
import { Mail, MapPin, Phone } from "lucide-react";
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
        <div className="container-tz grid items-start gap-8 lg:grid-cols-2">
          <div className="space-y-4">
            <a href={`tel:${SITE.phoneIntl}`} className="service-card block">
              <div className="icon-box">
                <Phone className="size-5" />
              </div>
              <h3>تلفن</h3>
              <p>{SITE.phoneDisplay}</p>
            </a>
            <a href={`mailto:${SITE.email}`} className="service-card block">
              <div className="icon-box">
                <Mail className="size-5" />
              </div>
              <h3>ایمیل</h3>
              <p>{SITE.email}</p>
            </a>
            <div className="service-card">
              <div className="icon-box">
                <MapPin className="size-5" />
              </div>
              <h3>نشانی</h3>
              <p>
                {SITE.address}
                <br />
                {SITE.hours}
              </p>
            </div>
          </div>
          <InquiryForm />
        </div>
      </section>
    </>
  );
}
