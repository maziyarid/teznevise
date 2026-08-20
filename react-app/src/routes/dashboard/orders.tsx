import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listMyInquiries } from "@/lib/server/app";

export const Route = createFileRoute("/dashboard/orders")({ component: Orders });

function Orders() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listMyInquiries>>>([]);
  useEffect(() => {
    void listMyInquiries().then(setRows).catch(() => setRows([]));
  }, []);
  return (
    <AppPage title="درخواست‌های من" hint="سفارش‌هایی که با همین حساب ثبت شده‌اند.">
      {rows.length === 0 ? (
        <p className="text-muted">
          هنوز درخواستی ندارید.{" "}
          <Link to="/inquiry" className="font-bold text-brand">
            ثبت سفارش
          </Link>
        </p>
      ) : (
        <ul className="space-y-3">
          {rows.map((o) => (
            <li key={o.id} className="surface-card">
              <b>{o.service || "مشاوره"}</b>
              <p className="text-sm text-muted">{o.message || "بدون توضیح"}</p>
              <p className="mt-1 text-xs text-muted">{o.created_at}</p>
            </li>
          ))}
        </ul>
      )}
    </AppPage>
  );
}
