import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listMyInquiries, listMyTickets } from "@/lib/server/app";
import { listMyProjects, PROJECT_STATUS_FA } from "@/lib/server/projects";
import { getMyWallet } from "@/lib/server/wallet";
import { useCurrentUser } from "@/lib/auth/use-current-user";
import { statusFa } from "@/lib/ticket-status";
import { faNum } from "@/lib/format";

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

  return (
    <AppPage title="پنل کاربری" hint={`خوش آمدید ${user?.displayName || user?.primaryEmail || ""}`}>
      <div className="mb-6 grid gap-4 sm:grid-cols-4">
        <div className="surface-card">
          <p className="text-sm text-muted">تزکوین</p>
          <p className="mt-1 text-3xl font-black text-brand">{faNum(coins)}</p>
          <Link to="/dashboard/wallet" className="text-xs font-bold text-brand">
            خرید اعتبار
          </Link>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">پروژه‌ها</p>
          <p className="mt-1 text-3xl font-black text-brand">{projects.length}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">تیکت‌های باز</p>
          <p className="mt-1 text-3xl font-black text-brand">{openTickets}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">درخواست‌ها</p>
          <p className="mt-1 text-3xl font-black text-brand">{orders.length}</p>
        </div>
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
            <p className="text-sm text-muted">پروژه‌ای ثبت نشده.</p>
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
            <p className="text-sm text-muted">هنوز تیکتی ندارید.</p>
          ) : (
            <ul className="space-y-2">
              {tickets.slice(0, 5).map((t) => (
                <li key={t.id}>
                  <Link to="/dashboard/tickets/$id" params={{ id: t.id }} className="block rounded-xl px-2 py-2 hover:bg-soft">
                    <b>{t.subject}</b>
                    <span className="mr-2 text-xs text-muted">{statusFa(t.status)}</span>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </section>
      </div>
    </AppPage>
  );
}
