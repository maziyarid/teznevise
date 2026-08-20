import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listAdminComments, setCommentStatus } from "@/lib/comments";

export const Route = createFileRoute("/admin/comments")({ component: AdminComments });

function AdminComments() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listAdminComments>>>([]);
  const load = () => void listAdminComments().then(setRows).catch(() => setRows([]));
  useEffect(() => {
    load();
  }, []);

  return (
    <AppPage title="نظرها" hint="دستیار هوشمند نظرها را تأیید یا نگه می‌دارد. این‌جا می‌توانید دستی تغییر دهید.">
      <ul className="space-y-3">
        {rows.map((c) => (
          <li key={c.id} className="surface-card">
            <div className="mb-2 flex flex-wrap items-center justify-between gap-2">
              <b>{c.name}</b>
              <span className="text-xs text-muted">
                {c.status === "approved" ? "منتشرشده" : "نگه‌داشته"} · {c.post_slug}
              </span>
            </div>
            <p className="text-sm">{c.body}</p>
            {c.ai_reason ? <p className="mt-2 text-xs text-muted">AI: {c.ai_reason}</p> : null}
            <div className="mt-3 flex gap-2">
              <button type="button" className="btn-tz btn-primary-tz" onClick={() => void setCommentStatus({ data: { id: c.id, status: "approved" } }).then(load)}>
                انتشار
              </button>
              <button type="button" className="btn-tz btn-light-tz" onClick={() => void setCommentStatus({ data: { id: c.id, status: "held" } }).then(load)}>
                نگه داشتن
              </button>
            </div>
          </li>
        ))}
        {rows.length === 0 ? <p className="text-muted">نظری نیست.</p> : null}
      </ul>
    </AppPage>
  );
}
