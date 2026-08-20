import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState, type FormEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { createProject, listMyProjects, PROJECT_STATUS_FA } from "@/lib/server/projects";
import { toast } from "sonner";

export const Route = createFileRoute("/dashboard/projects/")({ component: ProjectsPage });

function ProjectsPage() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listMyProjects>>>([]);
  const [title, setTitle] = useState("");
  const [service, setService] = useState("پایان‌نامه");
  const [notes, setNotes] = useState("");

  const load = () => void listMyProjects().then(setRows).catch(() => setRows([]));
  useEffect(() => {
    load();
  }, []);

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    try {
      await createProject({ data: { title, service, notes } });
      toast.success("پروژه ثبت شد");
      setTitle("");
      setNotes("");
      load();
    } catch {
      toast.error("ثبت پروژه ممکن نشد");
    }
  }

  return (
    <AppPage title="پیگیری پروژه‌ها" hint="وضعیت سفارش نگارش، تحلیل و دفاع را از اینجا دنبال کنید.">
      <form className="surface-card mb-6 grid gap-3" onSubmit={onSubmit}>
        <div className="grid gap-3 sm:grid-cols-2">
          <div className="field">
            <label htmlFor="pt">عنوان پروژه</label>
            <input id="pt" value={title} onChange={(e) => setTitle(e.target.value)} required minLength={4} />
          </div>
          <div className="field">
            <label htmlFor="ps">نوع خدمت</label>
            <select id="ps" value={service} onChange={(e) => setService(e.target.value)}>
              {["پایان‌نامه", "پروپوزال", "تحلیل آماری", "شبیه‌سازی", "مقاله"].map((s) => (
                <option key={s}>{s}</option>
              ))}
            </select>
          </div>
        </div>
        <div className="field">
          <label htmlFor="pn">توضیح کوتاه</label>
          <textarea id="pn" rows={3} value={notes} onChange={(e) => setNotes(e.target.value)} />
        </div>
        <button className="btn-tz btn-primary-tz" type="submit">
          ثبت پروژه جدید
        </button>
      </form>
      <ul className="space-y-3">
        {rows.map((p) => (
          <li key={p.id} className="surface-card">
            <Link to="/dashboard/projects/$id" params={{ id: p.id }} className="block">
              <div className="flex items-center justify-between gap-3">
                <b>{p.title}</b>
                <span className="text-xs font-bold text-brand">{PROJECT_STATUS_FA[p.status] ?? p.status}</span>
              </div>
              <p className="mb-2 text-sm text-muted">{p.service}</p>
              <div className="h-2 overflow-hidden rounded-full bg-soft">
                <span className="block h-full rounded-full bg-brand" style={{ width: `${p.progress}%` }} />
              </div>
            </Link>
          </li>
        ))}
        {rows.length === 0 ? <p className="text-sm text-muted">هنوز پروژه‌ای ندارید.</p> : null}
      </ul>
    </AppPage>
  );
}
