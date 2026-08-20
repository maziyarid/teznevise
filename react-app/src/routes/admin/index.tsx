import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { getAnalytics } from "@/lib/server/app";

export const Route = createFileRoute("/admin/")({ component: AdminHome });

function AdminHome() {
  const [data, setData] = useState<Awaited<ReturnType<typeof getAnalytics>> | null>(null);
  useEffect(() => {
    void getAnalytics().then(setData).catch(() => setData(null));
  }, []);
  const t = data?.totals;
  return (
    <AppPage title="نمای مدیریت" hint="تیکت‌ها، سفارش‌ها و بازدیدهای داخلی (بدون اسکریپت خارجی).">
      <div className="mb-6 grid gap-4 sm:grid-cols-4">
        <Stat label="بازدید صفحات" value={t?.views ?? 0} />
        <Stat label="کلیک‌ها" value={t?.clicks ?? 0} />
        <Stat label="تیکت‌ها" value={t?.tickets ?? 0} to="/admin/tickets" />
        <Stat label="سفارش‌ها" value={t?.inquiries ?? 0} to="/admin/inquiries" />
      </div>
      <div className="grid gap-6 lg:grid-cols-2">
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">صفحات پربازدید</h2>
          <ul className="space-y-2 text-sm">
            {(data?.pages ?? []).map((p) => (
              <li key={p.path} className="flex justify-between">
                <span dir="ltr">{p.path}</span>
                <b>{p.views}</b>
              </li>
            ))}
          </ul>
        </section>
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">کلیک‌های پرتکرار (Clarity-like)</h2>
          <ul className="space-y-2 text-sm">
            {(data?.clicks ?? []).map((c) => (
              <li key={c.label} className="flex justify-between gap-3">
                <span className="truncate">{c.label}</span>
                <b>{c.n}</b>
              </li>
            ))}
          </ul>
        </section>
      </div>
    </AppPage>
  );
}

function Stat({ label, value, to }: { label: string; value: number; to?: "/admin/tickets" | "/admin/inquiries" }) {
  const inner = (
    <>
      <p className="text-sm text-muted">{label}</p>
      <p className="mt-1 text-3xl font-black text-brand">{value}</p>
    </>
  );
  if (to) {
    return (
      <Link to={to} className="surface-card block">
        {inner}
      </Link>
    );
  }
  return <div className="surface-card">{inner}</div>;
}
