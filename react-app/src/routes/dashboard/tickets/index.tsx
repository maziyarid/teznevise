import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { createTicket, listMyTickets } from "@/lib/server/app";
import { statusFa } from "@/lib/ticket-status";
import { toast } from "sonner";

export const Route = createFileRoute("/dashboard/tickets/")({ component: TicketsPage });

function TicketsPage() {
  const [rows, setRows] = useState<Awaited<ReturnType<typeof listMyTickets>>>([]);
  const [subject, setSubject] = useState("");
  const [body, setBody] = useState("");
  const [busy, setBusy] = useState(false);

  const load = () => void listMyTickets().then(setRows).catch(() => setRows([]));
  useEffect(() => {
    load();
  }, []);

  return (
    <AppPage title="تیکت‌های پشتیبانی" hint="سوال یا پیگیری پروژه را اینجا بنویسید. فایل‌ها در انبار امن جدا ذخیره می‌شوند.">
      <form
        className="surface-card mb-6 grid gap-3"
        onSubmit={async (e) => {
          e.preventDefault();
          setBusy(true);
          try {
            await createTicket({ data: { subject, body } });
            toast.success("تیکت ثبت شد");
            setSubject("");
            setBody("");
            load();
          } catch {
            toast.error("ثبت تیکت ممکن نشد. دوباره وارد شوید.");
          } finally {
            setBusy(false);
          }
        }}
      >
        <div className="field">
          <label htmlFor="ts">موضوع</label>
          <input id="ts" value={subject} onChange={(e) => setSubject(e.target.value)} required />
        </div>
        <div className="field">
          <label htmlFor="tb">پیام</label>
          <textarea id="tb" value={body} onChange={(e) => setBody(e.target.value)} required />
        </div>
        <button className="btn-tz btn-primary-tz" type="submit" disabled={busy}>
          ارسال تیکت
        </button>
      </form>
      <ul className="space-y-2">
        {rows.map((t) => (
          <li key={t.id} className="surface-card">
            <Link to="/dashboard/tickets/$id" params={{ id: t.id }} className="flex items-center justify-between">
              <span className="font-extrabold">{t.subject}</span>
              <span className="text-xs text-muted">{statusFa(t.status)}</span>
            </Link>
          </li>
        ))}
      </ul>
    </AppPage>
  );
}
