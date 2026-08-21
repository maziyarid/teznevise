import { createFileRoute, Link } from "@tanstack/react-router";
import { useCallback, useEffect, useRef, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { TicketFiles } from "@/components/tickets/TicketFiles";
import { getTicket, replyTicket, setTicketStatus } from "@/lib/server/app";
import { statusFa } from "@/lib/ticket-status";
import { toast } from "sonner";

export const Route = createFileRoute("/admin/tickets/$id")({ component: AdminTicket });

function AdminTicket() {
  const { id } = Route.useParams();
  const [data, setData] = useState<Awaited<ReturnType<typeof getTicket>> | null>(null);
  const [body, setBody] = useState("");
  const loadVersion = useRef(0);

  const load = useCallback(() => {
    const version = ++loadVersion.current;
    void getTicket({ data: { id } })
      .then((nextData) => {
        if (loadVersion.current === version) setData(nextData);
      })
      .catch(() => {
        if (loadVersion.current === version) setData(null);
      });
  }, [id]);
  useEffect(() => {
    setData(null);
    load();
    return () => {
      loadVersion.current += 1;
    };
  }, [load]);

  if (!data) return <AppPage title="تیکت">بارگذاری…</AppPage>;

  return (
    <AppPage title={data.ticket.subject} hint={`وضعیت فعلی: ${statusFa(data.ticket.status)}`}>
      <Link to="/admin/tickets" className="link-arrow mb-4 inline-flex">
        بازگشت به صندوق
      </Link>
      <div className="mb-4 flex flex-wrap gap-2">
        {(["open", "pending", "closed"] as const).map((s) => (
          <button
            key={s}
            type="button"
            className={`btn-tz ${data.ticket.status === s ? "btn-primary-tz" : "btn-light-tz"}`}
            onClick={() =>
              void setTicketStatus({ data: { id, status: s } })
                .then(load)
                .then(() => toast.success("وضعیت به‌روز شد"))
            }
          >
            {statusFa(s)}
          </button>
        ))}
      </div>
      <ol className="mb-6 space-y-3">
        {data.messages.map((m) => (
          <li key={m.id} className={`surface-card ${m.is_staff ? "bg-soft" : ""}`}>
            <p className="text-xs font-bold text-muted">{m.is_staff ? "پشتیبانی" : "متقاضی"}</p>
            <p className="mt-1 whitespace-pre-wrap">{m.body}</p>
          </li>
        ))}
      </ol>
      <form
        className="surface-card grid gap-3"
        onSubmit={async (e) => {
          e.preventDefault();
          try {
            await replyTicket({ data: { id, body } });
            setBody("");
            load();
          } catch {
            toast.error("ارسال نشد");
          }
        }}
      >
        <label className="field">
          <span>پاسخ</span>
          <textarea value={body} onChange={(e) => setBody(e.target.value)} required rows={4} />
        </label>
        <button className="btn-tz btn-primary-tz" type="submit">
          پاسخ پشتیبانی
        </button>
      </form>
      <TicketFiles ticketId={id} />
    </AppPage>
  );
}
