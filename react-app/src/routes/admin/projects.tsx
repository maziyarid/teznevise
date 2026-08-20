import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listAdminProjects, PROJECT_STATUS_FA, PROJECT_STATUSES, setProjectStatus } from "@/lib/server/projects";
import { toast } from "sonner";

export const Route = createFileRoute("/admin/projects")({ component: AdminProjects });

function AdminProjects() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listAdminProjects>>>([]);
  const load = () => void listAdminProjects().then(setRows).catch(() => setRows([]));
  useEffect(() => {
    load();
  }, []);

  return (
    <AppPage title="پروژه‌های در جریان" hint="وضعیت سفارش نگارش را برای دانشجو به‌روز کنید.">
      <ul className="space-y-3">
        {rows.map((p) => (
          <li key={p.id} className="surface-card">
            <div className="mb-2 flex items-center justify-between">
              <b>{p.title}</b>
              <span className="text-xs">{PROJECT_STATUS_FA[p.status]}</span>
            </div>
            <p className="text-sm text-muted">{p.service}</p>
            <div className="mt-2 flex flex-wrap gap-2">
              {PROJECT_STATUSES.map((s) => (
                <button
                  key={s}
                  type="button"
                  className={`btn-tz ${p.status === s ? "btn-primary-tz" : "btn-light-tz"}`}
                  onClick={() =>
                    void setProjectStatus({ data: { id: p.id, status: s } })
                      .then(load)
                      .then(() => toast.success("وضعیت پروژه به‌روز شد"))
                  }
                >
                  {PROJECT_STATUS_FA[s]}
                </button>
              ))}
            </div>
            <Link to="/dashboard/projects/$id" params={{ id: p.id }} className="mt-2 inline-block text-sm font-bold text-brand">
              جزئیات تایم‌لاین
            </Link>
          </li>
        ))}
      </ul>
    </AppPage>
  );
}
