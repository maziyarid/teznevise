import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listMyInquiries, listMyTickets } from "@/lib/server/app";
import { listMyProjects, PROJECT_STATUS_FA } from "@/lib/server/projects";
import { getMyWallet } from "@/lib/server/wallet";
import { useCurrentUser } from "@/lib/auth/use-current-user";
import { statusFa } from "@/lib/ticket-status";
import { faNum } from "@/lib/format";
import { FaIcon } from "@/components/ui/FaIcon";

export const Route = createFileRoute("/dashboard/")({ component: DashHome });

function DashHome() {
  const user = useCurrentUser();
  const [tickets, setTickets] = useState<Awaited<ReturnType<typeof listMyTickets>>>([]);
  const [orders, setOrders] = useState<Awaited<ReturnType<typeof listMyInquiries>>>([]);
  const [projects, setProjects] = useState<Awaited<ReturnType<typeof listMyProjects>>>([]);
  const [coins, setCoins] = useState(0);

  useEffect(() => {
    void listMyTickets().then(setTickets).catch(() => setTickets([]));
    void listMyInquiries().then(setOrders).catch(() => setOrders([]));
    void listMyProjects().then(setProjects).catch(() => setProjects([]));
    void getMyWallet().then((w) => setCoins(w.balance)).catch(() => setCoins(0));
  }, []);

  const openTickets = tickets.filter((t) => t.status !== "closed").length;
  const greeting = user?.displayName || user?.primaryEmail || "";

  return (
    <AppPage title="پنل کاربری" hint={`خوش آمدید ${greeting}`}>
      <div className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article className="dash-stat tone-1">
          <p className="k">
            <FaIcon icon="fa-coins" /> تزکوین
          </p>
          <p className="v">{faNum(coins)}</p>
          <Link to="/dashboard/wallet" className="text-xs font-bold text-brand">
            خرید اعتبار و تاریخچه
          </Link>
        </article>
        <article className="dash-stat tone-2">
          <p className="k">
            <FaIcon icon="fa-folder-open" /> پروژه‌ها
          </p>
          <p className="v">{faNum(projects.length)}</p>
          <Link to="/dashboard/projects" className="text-xs font-bold text-brand">
            مدیریت پروژه‌ها
          </Link>
        </article>
        <article className="dash-stat tone-5">
          <p className="k">
            <FaIcon icon="fa-ticket" /> تیکت‌های باز
          </p>
          <p className="v">{faNum(openTickets)}</p>
          <Link to="/dashboard/tickets" className="text-xs font-bold text-brand">
            پیگیری پشتیبانی
          </Link>
        </article>
        <article className="dash-stat tone-3">
          <p className="k">
            <FaIcon icon="fa-file-lines" /> درخواست‌ها
          </p>
          <p className="v">{faNum(orders.length)}</p>
          <Link to="/dashboard/orders" className="text-xs font-bold text-brand">
            همه سفارش‌ها
          </Link>
        </article>
      </div>

      <div className="mb-6 grid gap-3 sm:grid-cols-3">
        <Link to="/inquiry" className="btn-tz btn-primary-tz">
          <FaIcon icon="fa-pen-to-square" /> ثبت سفارش جدید
        </Link>
        <Link to="/tools" className="btn-tz btn-light-tz">
          <FaIcon icon="fa-calculator" /> ابزارهای آنلاین
        </Link>
        <Link to="/dashboard/tickets" className="btn-tz btn-light-tz">
          <FaIcon icon="fa-headset" /> تیکت جدید
        </Link>
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <section className="surface-card">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-extrabold">وضعیت پروژه‌ها</h2>
            <Link to="/dashboard/projects" className="text-sm font-bold text-brand">
              همه
            </Link>
          </div>
          {projects.length === 0 ? (
            <p className="text-sm text-muted">پروژه‌ای ثبت نشده. از ثبت درخواست شروع کنید.</p>
          ) : (
            <ul className="space-y-3">
              {projects.slice(0, 4).map((p) => (
                <li key={p.id}>
                  <Link to="/dashboard/projects/$id" params={{ id: p.id }} className="block">
                    <div className="flex justify-between text-sm">
                      <b>{p.title}</b>
                      <span>{PROJECT_STATUS_FA[p.status]}</span>
                    </div>
                    <div className="mt-1 h-1.5 overflow-hidden rounded-full bg-soft">
                      <span className="block h-full bg-brand" style={{ width: `${p.progress}%` }} />
                    </div>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </section>
        <section className="surface-card">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-extrabold">آخرین تیکت‌ها</h2>
            <Link to="/dashboard/tickets" className="text-sm font-bold text-brand">
              همه
            </Link>
          </div>
          {tickets.length === 0 ? (
            <p className="text-sm text-muted">تیکتی ندارید.</p>
          ) : (
            <ul className="space-y-3">
              {tickets.slice(0, 4).map((t) => (
                <li key={t.id}>
                  <Link to="/dashboard/tickets/$id" params={{ id: t.id }} className="flex justify-between text-sm">
                    <b>{t.subject}</b>
                    <span>{statusFa(t.status)}</span>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </section>
        <section className="surface-card">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="font-extrabold">درخواست‌های اخیر</h2>
            <Link to="/dashboard/orders" className="text-sm font-bold text-brand">
              همه
            </Link>
          </div>
          {orders.length === 0 ? (
            <p className="text-sm text-muted">درخواستی ثبت نشده.</p>
          ) : (
            <ul className="space-y-3">
              {orders.slice(0, 4).map((o) => (
                <li key={o.id} className="flex justify-between text-sm">
                  <b>{o.service || o.name}</b>
                  <span className="text-muted">{o.created_at.slice(0, 10)}</span>
                </li>
              ))}
            </ul>
          )}
        </section>
        <section className="surface-card tone-6">
          <h2 className="font-extrabold">دستیار و اعتبار</h2>
          <p className="text-sm text-muted">
            هر پیام دستیار از سهمیه روزانه و تزکوین شما کم می‌شود. تاریخچه گفتگو برای حساب واردشده ذخیره می‌شود.
          </p>
          <div className="mt-3 flex flex-wrap gap-2">
            <Link to="/dashboard/wallet" className="btn-tz btn-primary-tz">
              شارژ کیف پول
            </Link>
            <Link to="/dashboard/referrals" className="btn-tz btn-light-tz">
              معرفی دوستان
            </Link>
            <Link to="/dashboard/profile" className="btn-tz btn-light-tz">
              تکمیل پروفایل
            </Link>
          </div>
        </section>
      </div>
    </AppPage>
  );
}
