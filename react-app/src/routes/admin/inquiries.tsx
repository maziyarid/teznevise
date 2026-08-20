import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listAdminInquiries } from "@/lib/server/app";

export const Route = createFileRoute("/admin/inquiries")({ component: AdminInquiries });

function AdminInquiries() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listAdminInquiries>>>([]);
  useEffect(() => {
    void listAdminInquiries().then(setRows).catch(() => setRows([]));
  }, []);
  return (
    <AppPage title="سفارش‌ها و فرم‌ها">
      <div className="overflow-x-auto">
        <table className="w-full text-right text-sm">
          <thead>
            <tr className="border-b border-line text-muted">
              <th className="p-2">نام</th>
              <th className="p-2">تلفن</th>
              <th className="p-2">خدمت</th>
              <th className="p-2">پیام</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((r) => (
              <tr key={r.id} className="border-b border-line/60">
                <td className="p-2 font-bold">{r.name}</td>
                <td className="p-2" dir="ltr">
                  {r.phone}
                </td>
                <td className="p-2">{r.service}</td>
                <td className="p-2 text-muted">{r.message}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppPage>
  );
}
