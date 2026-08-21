import { createFileRoute } from "@tanstack/react-router";
import { useCallback, useEffect, useRef, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { TicketFiles } from "@/components/tickets/TicketFiles";
import { getTicket, replyTicket } from "@/lib/server/app";
import { toast } from "sonner";

export const Route = createFileRoute("/dashboard/tickets/$id")({ component: TicketThread });

function TicketThread() {
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

  if (!data) return <AppPage title="تیکت">در حال بارگذاری…</AppPage>;

  return (
    <AppPage title={data.ticket.subject} hint={`وضعیت: ${data.ticket.status}`}>
      <ol className="mb-6 space-y-3">
        {data.messages.map((m) => (
          <li key={m.id} className={`surface-card ${m.is_staff ? "border-brand/30 bg-soft" : ""}`}>
            <p className="text-xs font-bold text-muted">{m.is_staff ? "پشتیبانی تزنویسه" : "شما"}</p>
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
            toast.error("ارسال پاسخ ممکن نشد");
          }
        }}
      >
        <label className="field">
          <span>پاسخ</span>
          <textarea value={body} onChange={(e) => setBody(e.target.value)} required rows={4} />
        </label>
        <button className="btn-tz btn-primary-tz" type="submit">
          ارسال پاسخ
        </button>
      </form>
      <TicketFiles ticketId={id} />
    </AppPage>
  );
}
