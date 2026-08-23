import { useMemo, useState } from "react";
import { sendToolChat } from "@/lib/server/ai-hub";

type Msg = { role: "user" | "assistant"; text: string; thought?: string };

export function LiveChat() {
  const [open, setOpen] = useState(false);
  const [text, setText] = useState("");
  const [busy, setBusy] = useState(false);
  const [status, setStatus] = useState("");
  const [research, setResearch] = useState(false);
  const [msgs, setMsgs] = useState<Msg[]>([
    {
      role: "assistant",
      text: "سلام. سؤال پژوهشی‌تان را بپرسید تا مسیر را برایتان توضیح بدهم. دقت علمی تضمین نمی‌شود؛ برای بررسی انسانی تماس رزرو کنید.",
    },
  ]);
  const [handoff, setHandoff] = useState({ name: "", phone: "", email: "", note: "" });

  const history = useMemo(
    () => msgs.map((m) => `${m.role === "user" ? "کاربر" : "تزنویسه"}: ${m.text}`).join("\n"),
    [msgs],
  );

  async function send() {
    const q = text.trim();
    if (q.length < 4 || busy) return;
    setText("");
    setMsgs((m) => [...m, { role: "user", text: q }]);
    setBusy(true);
    setStatus(research ? "در حال اتصال به رایانه…" : "در حال فکر کردن…");
    try {
      const res = await sendToolChat({
        data: {
          tool: "live-chat",
          context: research ? "research" : "live-chat",
          question: q,
          thinking: true,
          mode: research ? "collab" : "single",
        },
      });
      if (!res || !("ok" in res) || !res.ok) {
        const err = res && "error" in res ? String(res.error) : "پاسخ در دسترس نیست.";
        setMsgs((m) => [...m, { role: "assistant", text: err }]);
        return;
      }
      const replies = res.replies || [];
      if (!replies.length) {
        setMsgs((m) => [...m, { role: "assistant", text: "پاسخ آماده نشد. تماس رزرو کنید." }]);
        return;
      }
      setMsgs((m) => [
        ...m,
        ...replies.map((r) => ({
          role: "assistant" as const,
          text: r.content,
          thought: r.thinking,
        })),
      ]);
    } catch {
      setMsgs((m) => [
        ...m,
        {
          role: "assistant",
          text: "ارتباط برقرار نشد. می‌توانید تماس رزرو کنید تا تاریخچه گفتگو ایمیل شود.",
        },
      ]);
    } finally {
      setBusy(false);
      setStatus("");
    }
  }

  function mailHandoff(e: React.FormEvent) {
    e.preventDefault();
    const body = encodeURIComponent(
      `نام: ${handoff.name}\nموبایل: ${handoff.phone}\nایمیل: ${handoff.email}\n\nتاریخچه گفتگو:\n${history}`,
    );
    window.location.href = `mailto:teznevisan@gmail.com?subject=${encodeURIComponent("درخواست تماس از گفتگوی زنده")}&body=${body}`;
    setHandoff((h) => ({ ...h, note: "درخواست تماس آماده شد." }));
  }

  return (
    <div className="tz-livechat">
      <button type="button" className="tz-livechat__fab" onClick={() => setOpen((v) => !v)} aria-expanded={open}>
        گفتگو
      </button>
      {open ? (
        <section className="tz-ai-chat tz-gpt tz-livechat__panel" data-live-chat="1">
          <header className="tz-gpt__top">
            <strong>گفتگوی زنده پژوهشی</strong>
            <button type="button" className="tz-gpt__iconbtn" onClick={() => setOpen(false)}>
              بستن
            </button>
          </header>
          <p className="tz-livechat__note">
            پاسخ‌ها آموزشی‌اند و دقت علمی را تضمین نمی‌کنند. برای بررسی تخصصی، تماس رزرو کنید.
          </p>
          <div className="tz-gpt__log" role="log">
            {msgs.map((m, i) => (
              <article key={i} className={`tz-ai-msg is-${m.role}`}>
                {m.thought ? (
                  <details className="tz-ai-think">
                    <summary>مشاهده استدلال درونی</summary>
                    <pre>{m.thought}</pre>
                  </details>
                ) : null}
                <div className="tz-ai-msg__bubble">{m.text}</div>
              </article>
            ))}
            {status ? <p className="tz-ai-status">{status}</p> : null}
          </div>
          <form
            className="tz-gpt-composer"
            onSubmit={(e) => {
              e.preventDefault();
              void send();
            }}
          >
            <textarea
              rows={2}
              value={text}
              onChange={(e) => setText(e.target.value)}
              placeholder="سؤال خود را بنویسید…"
              required
              minLength={4}
            />
            <label className="tz-ai-chat__check">
              <input type="checkbox" checked={research} onChange={(e) => setResearch(e.target.checked)} /> پژوهش
            </label>
            <button type="submit" className="tz-gpt-send" disabled={busy}>
              ارسال
            </button>
          </form>
          <form className="tz-livechat__handoff" onSubmit={mailHandoff}>
            <p>رزرو تماس و ارسال تاریخچه گفتگو</p>
            <div className="tz-livechat__handoff-grid">
              <label>
                نام
                <input value={handoff.name} onChange={(e) => setHandoff({ ...handoff, name: e.target.value })} required />
              </label>
              <label>
                موبایل
                <input value={handoff.phone} onChange={(e) => setHandoff({ ...handoff, phone: e.target.value })} required />
              </label>
              <label className="full">
                ایمیل
                <input type="email" value={handoff.email} onChange={(e) => setHandoff({ ...handoff, email: e.target.value })} required />
              </label>
            </div>
            <button type="submit" className="btn-tz btn-light-tz">
              زمان‌بندی تماس
            </button>
            {handoff.note ? <p>{handoff.note}</p> : null}
          </form>
        </section>
      ) : null}
    </div>
  );
}