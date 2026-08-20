import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";
import { AppPage } from "@/components/layout/AppFrame";
import { getAnalytics } from "@/lib/server/app";

export const Route = createFileRoute("/admin/analytics")({ component: AnalyticsPage });

function AnalyticsPage() {
  const [data, setData] = useState<Awaited<ReturnType<typeof getAnalytics>> | null>(null);
  const [err, setErr] = useState(false);
  useEffect(() => {
    void getAnalytics()
      .then(setData)
      .catch(() => setErr(true));
  }, []);

  return (
    <AppPage
      title="بازدید و کلیک‌ها"
      hint="موتور داخلی هم‌ارز Google Analytics (بازدید مسیر) و Microsoft Clarity (کلیک و مختصات). شناسه‌های خارجی در تنظیمات ذخیره می‌شوند اما اسکریپت ثالث بارگذاری نمی‌شود."
    >
      {err ? <p className="mb-4 text-sm text-muted">بارگذاری آمار ممکن نشد. دوباره وارد شوید.</p> : null}
      <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div className="surface-card">
          <p className="text-sm text-muted">بازدید (GA-like)</p>
          <p className="text-3xl font-black text-brand">{data?.totals.views ?? 0}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">کلیک (Clarity-like)</p>
          <p className="text-3xl font-black text-brand">{data?.totals.clicks ?? 0}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">تیکت‌ها</p>
          <p className="text-3xl font-black text-brand">{data?.totals.tickets ?? 0}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">سفارش‌ها</p>
          <p className="text-3xl font-black text-brand">{data?.totals.inquiries ?? 0}</p>
        </div>
      </div>
      <section className="surface-card mb-6 h-72">
        <h2 className="mb-3 font-extrabold">بازدید روزانه</h2>
        {(data?.daily.length ?? 0) === 0 ? (
          <p className="text-sm text-muted">هنوز بازدیدی ثبت نشده. چند صفحه عمومی را باز کنید.</p>
        ) : (
          <ResponsiveContainer width="100%" height="85%">
            <BarChart data={data?.daily ?? []}>
              <CartesianGrid strokeDasharray="3 3" stroke="#dfe9e5" />
              <XAxis dataKey="day" tick={{ fontSize: 11 }} />
              <YAxis allowDecimals={false} tick={{ fontSize: 11 }} />
              <Tooltip />
              <Bar dataKey="views" fill="#145d4a" radius={[6, 6, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        )}
      </section>
      <section className="surface-card mb-6">
        <h2 className="mb-3 font-extrabold">نقشه کلیک (Clarity-like)</h2>
        <p className="mb-3 text-sm text-muted">نقاط بر اساس مختصات کلیک در پنجره مرورگر.</p>
        <div className="heat-map" aria-hidden>
          {(data?.recentClicks ?? [])
            .filter((c) => c.x != null && c.y != null)
            .map((c, i) => (
              <span
                key={`${c.created_at}-${i}`}
                className="heat-dot"
                style={{
                  left: `${Math.min(96, Math.max(2, ((c.x ?? 0) / 1440) * 100))}%`,
                  top: `${Math.min(92, Math.max(4, ((c.y ?? 0) / 900) * 100))}%`,
                }}
              />
            ))}
        </div>
      </section>
      <div className="grid gap-6 lg:grid-cols-2">
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">مسیرهای پربازدید</h2>
          <ul className="space-y-2 text-sm">
            {(data?.pages ?? []).map((p) => (
              <li key={p.path} className="flex justify-between" dir="ltr">
                <span>{p.path}</span>
                <b>{p.views}</b>
              </li>
            ))}
            {(data?.pages.length ?? 0) === 0 ? <li className="text-muted">داده‌ای نیست.</li> : null}
          </ul>
        </section>
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">آخرین کلیک‌ها</h2>
          <ul className="space-y-2 text-sm">
            {(data?.recentClicks ?? []).map((c, i) => (
              <li key={i} className="flex justify-between gap-2">
                <span className="truncate">{c.label}</span>
                <span className="shrink-0 text-muted" dir="ltr">
                  {c.path}
                </span>
              </li>
            ))}
            {(data?.recentClicks.length ?? 0) === 0 ? <li className="text-muted">کلیکی نیست.</li> : null}
          </ul>
        </section>
      </div>
    </AppPage>
  );
}
