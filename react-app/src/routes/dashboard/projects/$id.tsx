import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { getProject, PROJECT_STATUS_FA } from "@/lib/server/projects";

export const Route = createFileRoute("/dashboard/projects/$id")({ component: ProjectTrack });

function ProjectTrack() {
  const { id } = Route.useParams();
  const [data, setData] = useState<Awaited<ReturnType<typeof getProject>> | null>(null);

  useEffect(() => {
    void getProject({ data: { id } })
      .then(setData)
      .catch(() => setData(null));
  }, [id]);

  if (!data) return <AppPage title="پروژه">در حال بارگذاری…</AppPage>;
  const p = data.project;

  return (
    <AppPage title={p.title} hint={p.service}>
      <Link to="/dashboard/projects" className="link-arrow mb-4 inline-flex">
        بازگشت به پروژه‌ها
      </Link>
      <div className="surface-card mb-6">
        <div className="mb-2 flex justify-between text-sm">
          <span>{PROJECT_STATUS_FA[p.status]}</span>
          <b>{p.progress}٪</b>
        </div>
        <div className="h-2.5 overflow-hidden rounded-full bg-soft">
          <span className="block h-full rounded-full bg-brand" style={{ width: `${p.progress}%` }} />
        </div>
        {p.due_at ? <p className="mt-3 text-sm text-muted">موعد بعدی: {p.due_at}</p> : null}
        {p.notes ? <p className="mt-2 text-sm">{p.notes}</p> : null}
      </div>
      <ol className="relative space-y-4 border-r-2 border-line pr-5">
        {data.events.map((e) => (
          <li key={e.id}>
            <span className="absolute right-[-7px] mt-1 size-3 rounded-full bg-brand" />
            <b>{PROJECT_STATUS_FA[e.status] ?? e.status}</b>
            <p className="mb-0 text-sm text-muted">{e.note}</p>
            <small className="text-muted">{e.created_at.slice(0, 16).replace("T", " ")}</small>
          </li>
        ))}
      </ol>
    </AppPage>
  );
}
