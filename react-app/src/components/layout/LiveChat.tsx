import { useEffect, useMemo, useRef, useState } from "react";
import { FaIcon } from "@/components/ui/FaIcon";
import { sendToolChat, listAgents, PUBLIC_CHAT_MODELS } from "@/lib/server/ai-hub";
import { submitInquiry } from "@/lib/inquiries";

type Msg = {
  role: "user" | "assistant";
  text: string;
  thought?: string;
  name?: string;
  live?: boolean;
  elapsed?: number;
};

const ROSTER = [
  { id: "teznevise", name: "تزنویسه", desc: "ترکیب‌گر پژوهشی" },
  { id: "christina", name: "کریستینا", desc: "ویراستار نگارش علمی" },
  { id: "ada", name: "آدا", desc: "تحلیل‌گر داده و کد" },
  { id: "professor", name: "پروفسور", desc: "روش‌شناس پژوهش" },
  { id: "parantez", name: "پرانتز", desc: "آمار کاربردی" },
  { id: "elara", name: "الارا ووس", desc: "پژوهش کیفی و اخلاق" },
  { id: "cyrus", name: "کوروش لکس", desc: "استدلال حقوقی" },
  { id: "mira", name: "دکتر میرا ساتو", desc: "علوم پزشکی و STEM" },
];

export function LiveChat() {
  const [open, setOpen] = useState(false);
  const [menu, setMenu] = useState(false);
  const [text, setText] = useState("");
  const [busy, setBusy] = useState(false);
  const [thinkingOn, setThinkingOn] = useState(false);
  const [collab, setCollab] = useState(false);
  const [research, setResearch] = useState(false);
  const [handoffOpen, setHandoffOpen] = useState(false);
  const [agentId, setAgentId] = useState("teznevise");
	const [model, setModel] = useState("");
  const [agents, setAgents] = useState(ROSTER);
  const [thoughtOpen, setThoughtOpen] = useState(false);
  const [elapsed, setElapsed] = useState(0);
  const [hintsOn, setHintsOn] = useState(true);
  const [msgs, setMsgs] = useState<Msg[]>([
    {
      role: "assistant",
      name: "تزنویسه",
	  text: "سلام. سؤال پژوهشی‌تان را بپرسید؛ مراحل آماده‌سازی پاسخ به‌صورت زنده نمایش داده می‌شود.",
    },
  ]);
  const [handoff, setHandoff] = useState({ name: "", phone: "", email: "", note: "" });
  const logRef = useRef<HTMLDivElement>(null);
  const abortRef = useRef<AbortController | null>(null);
	const requestRef = useRef(0);

  useEffect(() => {
    void listAgents()
      .then((rows) => {
        if (rows?.length) {
          setAgents(rows.map((r) => ({ id: r.id, name: r.name, desc: r.provider })));
        }
      })
      .catch(() => {});
  }, []);

  useEffect(() => {
    if (!busy) return;
    const t0 = Date.now();
    const id = window.setInterval(() => setElapsed(Math.max(1, Math.round((Date.now() - t0) / 1000))), 400);
    return () => window.clearInterval(id);
  }, [busy]);

  useEffect(() => {
    if (thoughtOpen) logRef.current?.scrollTo({ top: logRef.current.scrollHeight });
  }, [msgs, busy, thoughtOpen]);

  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (e.key !== "Escape") return;
      if (menu) {
        setMenu(false);
        return;
      }
      if (open) setOpen(false);
    }
    document.addEventListener("keydown", onKey);
    return () => document.removeEventListener("keydown", onKey);
  }, [menu, open]);

  useEffect(() => {
    document.body.classList.toggle("tz-livechat-open", open);
    return () => document.body.classList.remove("tz-livechat-open");
  }, [open]);

  const agent = agents.find((a) => a.id === agentId) ?? agents[0];
  const history = useMemo(
    () => msgs.map((m) => `${m.role === "user" ? "کاربر" : m.name || "تزنویسه"}: ${m.text}`).join("\n"),
    [msgs],
  );

  async function send(raw?: string) {
    const q = (raw ?? text).trim();
    if (q.length < 4 || busy) return;
    setText("");
    setMsgs((m) => [...m, { role: "user", text: q, name: "شما" }]);
    setBusy(true);
    setElapsed(1);
	setThoughtOpen(true);
    setHintsOn(false);
    const ctl = new AbortController();
	const requestId = ++requestRef.current;
    abortRef.current = ctl;
    try {
      const res = await sendToolChat({
        data: {
          tool: "live-chat",
          context: research ? "research" : "live-chat",
          question: q,
		  thinking: thinkingOn,
		  model: model || undefined,
          mode: research || collab ? "collab" : "single",
          agentIds: [agent.id],
        },
      });
      if (ctl.signal.aborted) return;
      if (!res || !("ok" in res) || !res.ok) {
        const err = res && "error" in res ? String(res.error) : "پاسخ در دسترس نیست.";
        setMsgs((m) => [...m, { role: "assistant", text: err, name: agent.name }]);
        return;
      }
      const replies = res.replies || [];
      if (!replies.length) {
        setMsgs((m) => [...m, { role: "assistant", text: "پاسخ آماده نشد. تماس رزرو کنید.", name: agent.name }]);
        return;
      }
      setMsgs((m) => [
        ...m,
        ...replies.map((r) => ({
          role: "assistant" as const,
          text: r.content,
		  thought: undefined,
          name: r.agentName || agent.name,
        })),
      ]);
    } catch {
      if (ctl.signal.aborted) {
        setMsgs((m) => [...m, { role: "assistant", text: "تولید متوقف شد.", name: agent.name }]);
        return;
      }
      setMsgs((m) => [
        ...m,
        {
          role: "assistant",
          name: agent.name,
          text: "ارتباط برقرار نشد. می‌توانید تماس رزرو کنید تا تاریخچه گفتگو ایمیل شود.",
        },
      ]);
    } finally {
	  if (requestRef.current === requestId) {
		setBusy(false);
		setThoughtOpen(thinkingOn);
		abortRef.current = null;
	  }
    }
  }

  async function mailHandoff(e: React.FormEvent) {
    e.preventDefault();
	setHandoff((h) => ({ ...h, note: "در حال ثبت…" }));
	try {
	  await submitInquiry({ data: {
		name: handoff.name,
		phone: handoff.phone,
		email: handoff.email,
		service: "chat-handoff",
		message: `تاریخچه گفتگو:\n${history}`.slice(0, 2000),
	  } });
	  setHandoff((h) => ({ ...h, note: "درخواست تماس و تاریخچه گفتگو ثبت شد." }));
	} catch {
	  setHandoff((h) => ({ ...h, note: "ثبت درخواست ناموفق بود؛ دوباره تلاش کنید." }));
	}
  }

  return (
    <div className={open ? "tz-livechat is-open" : "tz-livechat"}>
      <button
        type="button"
        className="tz-livechat__fab"
        onClick={() => setOpen((v) => !v)}
        aria-expanded={open}
        aria-label="باز کردن گفتگوی زنده پژوهشی"
        title="گفتگوی زنده"
      >
        <FaIcon icon="fa-comment-dots" />
        <span className="tz-livechat__fab-label">گفتگو</span>
      </button>
      {open ? (
		<section className={`tz-ai-chat tz-gpt tz-livechat__panel${menu ? " is-picking" : ""}`} data-live-chat="1" role="dialog" aria-modal="false" aria-label="گفتگوی پژوهشی تزنویسه">
          <header className="tz-gpt__top">
			<div className="tz-gpt-selectors">
			<div className="tz-gpt-model">
              <button
                type="button"
                className="tz-gpt-model__btn"
                aria-haspopup="listbox"
                aria-expanded={menu}
				aria-label="انتخاب عامل پژوهشی"
                onClick={() => setMenu((v) => !v)}
				onKeyDown={(e) => {
				  if (e.key === "ArrowDown") {
					e.preventDefault();
					setMenu(true);
					window.setTimeout(() => document.querySelector<HTMLButtonElement>('.tz-gpt-model__list [role="option"]')?.focus(), 0);
				  }
				}}
              >
                <FaIcon icon="fa-brain" />
                <span>{agent?.name}</span>
                <FaIcon icon="fa-chevron-down" />
              </button>
              {menu ? (
				<div className="tz-gpt-model__list" role="listbox" aria-label="عامل‌های پژوهشی" onKeyDown={(e) => {
				  const options = Array.from(e.currentTarget.querySelectorAll<HTMLButtonElement>('[role="option"]'));
				  const index = options.indexOf(document.activeElement as HTMLButtonElement);
				  if (e.key === "Escape") { e.preventDefault(); setMenu(false); }
				  if (e.key === "ArrowDown") { e.preventDefault(); (options[index + 1] || options[0])?.focus(); }
				  if (e.key === "ArrowUp") { e.preventDefault(); (options[index - 1] || options.at(-1))?.focus(); }
				  if (e.key === "Home") { e.preventDefault(); options[0]?.focus(); }
				  if (e.key === "End") { e.preventDefault(); options.at(-1)?.focus(); }
				}}>
                  <div className="tz-gpt-model__list-head">
                    <strong>انتخاب عامل</strong>
                    <button
                      type="button"
                      className="tz-gpt__iconbtn tz-gpt-model__done"
                      aria-label="بازگشت به گفتگو"
                      title="بازگشت به گفتگو"
                      onClick={() => setMenu(false)}
                    >
                      <FaIcon icon="fa-xmark" />
                    </button>
                  </div>
                  {agents.map((a) => (
                    <button
                      key={a.id}
                      type="button"
                      role="option"
                      className={a.id === agentId ? "tz-gpt-agent is-on" : "tz-gpt-agent"}
                      aria-selected={a.id === agentId}
                      onClick={() => {
                        setAgentId(a.id);
                        setMenu(false);
                      }}
                    >
                      <span className="tz-gpt-agent__copy">
                        <strong>{a.name}</strong>
                        <small>{a.desc}</small>
                      </span>
                    </button>
                  ))}
                </div>
              ) : null}
            </div>
			<label className="tz-gpt-llm" title="انتخاب مدل زبانی">
			  <FaIcon icon="fa-microchip" />
			  <span className="sr-only">مدل زبانی</span>
			  <select value={model} onChange={(e) => setModel(e.target.value)} aria-label="انتخاب مدل زبانی">
				<option value="">خودکار</option>
				{PUBLIC_CHAT_MODELS.map((item) => <option key={item.id} value={item.id}>{item.label}</option>)}
			  </select>
			</label>
			</div>
            <div className="tz-gpt__top-actions">
              <button
                type="button"
                className="tz-gpt__iconbtn"
                aria-label="گفتگوی تازه"
                title="گفتگوی تازه"
                onClick={() => {
                  abortRef.current?.abort();
				  requestRef.current += 1;
                  setBusy(false);
                  setMenu(false);
                  setMsgs([
                    {
                      role: "assistant",
                      name: agent?.name,
					  text: "سلام. سؤال پژوهشی‌تان را بپرسید؛ مراحل آماده‌سازی پاسخ به‌صورت زنده نمایش داده می‌شود.",
                    },
                  ]);
                  setHintsOn(true);
                }}
              >
                <FaIcon icon="fa-plus" />
              </button>
              <button
                type="button"
                className="tz-gpt__iconbtn"
                aria-label={menu ? "بازگشت به گفتگو" : "بستن گفتگو"}
                title={menu ? "بازگشت به گفتگو" : "بستن"}
                onClick={() => (menu ? setMenu(false) : setOpen(false))}
              >
                <FaIcon icon="fa-xmark" />
              </button>
            </div>
          </header>
          {menu ? (
            <button
              type="button"
              className="tz-gpt-model__scrim"
              aria-label="بازگشت به گفتگو"
              onClick={() => setMenu(false)}
            />
          ) : null}
          <p className="tz-livechat__note">
            پاسخ‌ها بر اساس محتوای تزنویسه‌اند و مشاوره آموزشی‌اند، نه نوشتن پایان‌نامه.
          </p>
		  <div className="tz-gpt__log" role="log" aria-live="polite" aria-relevant="additions text" tabIndex={0} ref={logRef} aria-busy={busy}>
            {msgs.map((m, i) => (
              <article key={i} className={`tz-ai-msg is-${m.role}`}>
                <div className="tz-ai-msg__bubble">{m.text}</div>
              </article>
            ))}
            {busy ? (
              <article className="tz-ai-msg is-assistant is-pending">
				<details className="tz-ai-think is-live" open={thoughtOpen} onToggle={(e) => setThoughtOpen((e.target as HTMLDetailsElement).open)}>
                    <summary className="tz-ai-think__sum">
					  <FaIcon icon="fa-spinner" className="fa-spin" /> فرآیند پاسخ · {elapsed}ث
                    </summary>
					<pre className="tz-ai-think__stream">{elapsed < 2 ? "اتصال امن به مدل…" : elapsed < 4 ? "بررسی سؤال و زمینه صفحه…" : elapsed < 7 ? "انتخاب منابع و روش پاسخ…" : "آماده‌سازی پاسخ نهایی…"}</pre>
                  </details>
                <div className="tz-ai-msg__bubble tz-ai-msg__bubble--wait">
                  <span className="tz-ai-dots" aria-hidden>
                    <i /><i /><i />
                  </span>
                  <span className="tz-ai-wait-label">در حال پاسخ…</span>
                </div>
              </article>
            ) : null}
          </div>
          {hintsOn && !busy ? (
            <div className="tz-gpt-hints">
              {[
                "از کجا مشاوره پایان‌نامه را شروع کنم؟",
                "کدام آزمون آماری برای داده من مناسب است؟",
                "ابزارهای آنلاین تزنویسه چه کمکی می‌کنند؟",
              ].map((hint) => (
                <button
                  key={hint}
                  type="button"
                  className="tz-gpt-hint"
                  onClick={() => {
                    void send(hint);
                  }}
                >
                  {hint}
                </button>
              ))}
            </div>
          ) : null}
          <form
            className="tz-gpt-composer"
            onSubmit={(e) => {
              e.preventDefault();
              void send();
            }}
          >
            <div className="tz-gpt-box">
              <label className="sr-only" htmlFor="tz-live-q">
                پیام
              </label>
              <textarea
                id="tz-live-q"
                rows={1}
                value={text}
                onChange={(e) => setText(e.target.value)}
                placeholder="سؤال خود را بنویسید…"
                required
                minLength={4}
                onKeyDown={(e) => {
                  if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    void send();
                  }
                  if (e.key === "Escape") setOpen(false);
                }}
              />
              <div className="tz-gpt-bar">
                <div className="tz-gpt-toggles" role="toolbar" aria-label="ابزار گفتگو">
                  <button
                    type="button"
                    className={`tz-gpt__iconbtn is-toggle${thinkingOn ? " is-on" : ""}`}
                    aria-pressed={thinkingOn}
					aria-label="باز نگه داشتن فرآیند پاسخ"
					title="فرآیند پاسخ"
                    onClick={() => setThinkingOn((v) => !v)}
                  >
					<FaIcon icon="fa-list-check" />
                  </button>
                  <button
                    type="button"
                    className={`tz-gpt__iconbtn is-toggle${collab ? " is-on" : ""}`}
                    aria-pressed={collab}
                    aria-label="هم‌فکری عامل‌ها"
                    title="هم‌فکری"
                    onClick={() => setCollab((v) => !v)}
                  >
                    <FaIcon icon="fa-users" />
                  </button>
                  <button
                    type="button"
                    className={`tz-gpt__iconbtn is-toggle${research ? " is-on" : ""}`}
                    aria-pressed={research}
                    aria-label="پژوهش وب"
                    title="پژوهش"
                    onClick={() => setResearch((v) => !v)}
                  >
                    <FaIcon icon="fa-globe" />
                  </button>
                  <button
                    type="button"
                    className={`tz-gpt__iconbtn${handoffOpen ? " is-on" : ""}`}
                    aria-expanded={handoffOpen}
                    aria-label="رزرو تماس"
                    title="رزرو تماس"
                    onClick={() => setHandoffOpen((v) => !v)}
                  >
                    <FaIcon icon="fa-phone" />
                  </button>
                </div>
                {busy ? (
                  <button
                    type="button"
                    className="tz-gpt-stop"
                    aria-label="توقف"
                    title="توقف"
					onClick={() => {
					  abortRef.current?.abort();
					  requestRef.current += 1;
					  setBusy(false);
					  setThoughtOpen(false);
					  setMsgs((items) => [...items, { role: "assistant", text: "تولید متوقف شد.", name: agent.name }]);
					}}
                  >
                    <FaIcon icon="fa-stop" />
                  </button>
                ) : (
                  <button type="submit" className="tz-gpt-send" aria-label="ارسال" title="ارسال">
                    <FaIcon icon="fa-arrow-up" />
                  </button>
                )}
              </div>
            </div>
          </form>
          {handoffOpen ? (
            <form className="tz-livechat__handoff" onSubmit={mailHandoff}>
			  <p>رزرو تماس و ثبت تاریخچه گفتگو</p>
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
				ثبت درخواست تماس
              </button>
              {handoff.note ? <p>{handoff.note}</p> : null}
            </form>
          ) : null}
        </section>
      ) : null}
    </div>
  );
}
