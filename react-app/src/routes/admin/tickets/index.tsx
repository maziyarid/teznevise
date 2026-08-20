import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listAdminTickets } from "@/lib/server/app";
import { priorityFa, statusFa } from "@/lib/ticket-status";

export const Route = createFileRoute("/admin/tickets/")({ component: AdminTickets });

function AdminTickets() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listAdminTickets>>>([]);
  useEffect(() => {
    void listAdminTickets().then(setRows).catch(() => setRows([]));
  }, []);
  return (
    <AppPage title="صندوق تیکت‌ها" hint="پاسخ پشتیبانی وضعیت را «در انتظار پاسخ» می‌کند.">
      <ul className="space-y-2">
        {rows.map((t) => (
          <li key={t.id} className="surface-card">
            <Link to="/admin/tickets/$id" params={{ id: t.id }} className="flex items-center justify-between gap-3">
              <span>
                <b>{t.subject}</b>
                <span className="mr-2 text-xs text-muted">اولویت {priorityFa(t.priority)}</span>
              </span>
              <span className="text-xs text-muted">{statusFa(t.status)}</span>
            </Link>
          </li>
        ))}
        {rows.length === 0 ? <p className="text-muted">تیکتی نیست.</p> : null}
      </ul>
    </AppPage>
  );
}
