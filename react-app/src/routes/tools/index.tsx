import { createFileRoute } from "@tanstack/react-router";
import { TOOLS } from "@/lib/content";
import { IMPORTED_TOOLS } from "@/lib/imported-pages";
import { splitFaqs } from "@/lib/page-copy";
import { ToolsWorkspace } from "@/components/tools/ToolsWorkspace";
import { PageHero } from "@/components/shared/PageHero";
import { FaqGrid } from "@/components/shared/FaqGrid";
import { usePageOverlay } from "@/lib/page-overlay";

export const Route = createFileRoute("/tools/")({ component: Tools });

function Tools() {
  const hub = IMPORTED_TOOLS["online-calculation-tools"];
  const overlay = usePageOverlay("tools");
  const faqs = splitFaqs(hub?.features ?? []).faqs;
  return (
    <>
      <PageHero
        eyebrow={overlay?.eyebrow || "میز کار آماری"}
        title={overlay?.title || hub?.heroTitle || "ابزارها را همین‌جا اجرا کنید"}
        lead={overlay?.lead || hub?.lead || "ماشین‌حساب‌های رایگان برای همه؛ ابزارهای ویژه و دستیار هوش مصنوعی بعد از ورود با شماره موبایل."}
      />
      <section className="section pt-8">
        <div className="container-tz">
          <ToolsWorkspace tool={TOOLS[0]} />
        </div>
      </section>
      <FaqGrid items={faqs} />
    </>
  );
}