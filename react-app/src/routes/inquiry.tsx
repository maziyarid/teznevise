import { createFileRoute } from "@tanstack/react-router";
import { Phone } from "lucide-react";
import { SITE } from "@/lib/site";
import { InquiryForm } from "@/components/shared/InquiryForm";
import { PageHero } from "@/components/shared/PageHero";

export const Route = createFileRoute("/inquiry")({ component: Inquiry });

function Inquiry() {
  return (
    <>
      <PageHero
        eyebrow="ثبت سفارش"
        title="ثبت درخواست پروژه پژوهشی"
        lead="موضوع، مقطع و مرحله فعلی را بفرستید تا برآورد اولیه و مسیر کار مشخص شود."
      />
      <section className="section">
        <div className="container-tz grid items-start gap-8 lg:grid-cols-[0.9fr_1.1fr]">
          <div className="space-y-4">
            <div className="surface-card text-center">
              <h2 className="mt-0 text-xl font-extrabold">تماس بگیرید</h2>
              <p className="text-muted">اگر ترجیح می‌دهید تلفنی صحبت کنیم:</p>
              <a href={`tel:${SITE.phoneIntl}`} className="btn-tz btn-primary-tz mt-2">
                <Phone className="size-4" /> {SITE.phoneDisplay}
              </a>
            </div>
            <div className="flex flex-wrap gap-2">
              <a className="pill" href={SITE.whatsapp} target="_blank" rel="noopener noreferrer">
                واتساپ
              </a>
              <a className="pill" href={SITE.telegram} target="_blank" rel="noopener noreferrer">
                تلگرام
              </a>
              <a className="pill" href={SITE.bale} target="_blank" rel="noopener noreferrer">
                بله
              </a>
            </div>
          </div>
          <InquiryForm />
        </div>
      </section>
    </>
  );
}
