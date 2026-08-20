import { createFileRoute } from "@tanstack/react-router";
import { TOOLS } from "@/lib/content";
import { ToolsWorkspace } from "@/components/tools/ToolsWorkspace";

export const Route = createFileRoute("/tools/")({ component: Tools });

function Tools() {
  return (
    <section className="section pt-6">
      <div className="container-tz">
        <p className="eyebrow">میز کار آماری</p>
        <h1 className="mt-3 mb-2 text-3xl font-extrabold">ابزارها را همین‌جا اجرا کنید</h1>
        <p className="mb-8 max-w-2xl text-muted">
          ماشین‌حساب‌های رایگان برای همه؛ ابزارهای ویژه و «از هوش مصنوعی بپرس» بعد از ورود با شماره موبایل.
        </p>
        <ToolsWorkspace tool={TOOLS[0]} />
      </div>
    </section>
  );
}
