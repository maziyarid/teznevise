import { useEffect, useMemo, useRef, useState, type FormEvent } from "react";
import { Link } from "@tanstack/react-router";
import { FaIcon } from "@/components/ui/FaIcon";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { getAiQuota, listAgents, listMyThreads, sendToolChat } from "@/lib/server/ai-hub";
import { toast } from "sonner";
import { faNum } from "@/lib/format";

const GREETING = "اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری";

type ChatMsg = {
  id: string;
  role: "user" | "assistant";
  agentName: string;
  thinking?: string;
  content: string;
};

export function AskAiPanel({
  tool,
  context,
  placeholder,
}: {
  tool: string;
  context: string;
  placeholder?: string;
}) {
  const { user } = useCurrentUserState();
  const [question, setQuestion] = useState("");
  const [busy, setBusy] = useState(false);
  const [agentIds, setAgentIds] = useState<string[]>([]);
  const [agents, setAgents] = useState<{ id: string; name: string; provider: string }[]>([]);
  const [mode, setMode] = useState<"single" | "collab" | "reflect">("single");
  const [showThinking, setShowThinking] = useState(false);
  const [threadId, setThreadId] = useState<string | null>(null);
  const [quota, setQuota] = useState<{ remaining: number; limit: number; cost: number; credits: number } | null>(null);
  const [messages, setMessages] = useState<ChatMsg[]>([
    { id: "greet", role: "assistant", agentName: "دستیار تزنویسه", content: GREETING },
  ]);
  const scroller = useRef<HTMLDivElement>(null);
  const [elapsed, setElapsed] = useState(0);
  const [thoughtOpen, setThoughtOpen] = useState(false);

  useEffect(() => {
    if (!busy) return;
    const t0 = Date.now();
    const id = window.setInterval(() => setElapsed(Math.max(1, Math.round((Date.now() - t0) / 1000))), 400);
    return () => window.clearInterval(id);
  }, [busy]);

  useEffect(() => {
    void listAgents().then(setAgents).catch(() => setAgents([]));
    void getAiQuota()
      .then(setQuota)
      .catch(() => setQuota(null));
  }, [user]);

  useEffect(() => {
    if (!user) return;
    void listMyThreads({ data: { tool } })
      .then((rows) => {
        const last = rows[0];
        if (last?.messages?.length) {
          setThreadId(last.id);
          setMessages([
            { id: "greet", role: "assistant", agentName: "دستیار تزنویسه", content: GREETING },
            ...last.messages,
          ]);
        }
      })
      .catch(() => {});
  }, [user, tool]);

  useEffect(() => {
    scroller.current?.scrollTo({ top: scroller.current.scrollHeight, behavior: "smooth" });
  }, [messages, busy]);

  const selectedNames = useMemo(
    () => agents.filter((a) => agentIds.includes(a.id)).map((a) => a.name),
    [agents, agentIds],
  );

  function toggleAgent(id: string) {
    setAgentIds((cur) => (cur.includes(id) ? cur.filter((x) => x !== id) : [...cur, id]));
  }

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    const q = question.trim();
    if (q.length < 2) return;
    setBusy(true);
    setElapsed(1);
    setThoughtOpen(false);
    setQuestion("");
    const userMsg: ChatMsg = { id: crypto.randomUUID(), role: "user", agentName: user?.displayName || "شما", content: q };
    setMessages((m) => [...m, userMsg]);
    try {
      const res = await sendToolChat({
        data: {
          tool,
          context,
          question: q,
          agentIds,
          mode,
          thinking: true,
          threadId: threadId ?? undefined,
        },
      });
      if (!res.ok) {
        toast.error(res.error);
        return;
      }
      setThreadId(res.threadId);
      setQuota(res.quota);
      setMessages((m) => [
        ...m,
        ...res.replies.map((r) => ({
          id: crypto.randomUUID(),
          role: "assistant" as const,
          agentName: r.agentName,
          thinking: r.thinking,
          content: r.content,
        })),
      ]);
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "ارسال ممکن نشد");
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="ask-ai ask-ai-pro surface-card">
      <div className="mb-3 flex flex-wrap items-center justify-between gap-3">
        <div className="flex items-center gap-2">
          <span className="icon-box">
            <FaIcon icon="fa-comments" />
          </span>
          <div>
            <h3 className="m-0">گفتگو با دستیار پژوهش</h3>
            <p className="mt-0 mb-0 text-sm text-muted">عامل را انتخاب کنید؛ می‌توانند جدا یا با هم کار کنند.</p>
          </div>
        </div>
        {quota ? (
          <p className="mb-0 text-xs font-bold text-muted">
            باقیمانده امروز {faNum(quota.remaining)} از {faNum(quota.limit)}
            {user ? ` · هر پیام ${faNum(quota.cost)} تزکوین` : " · مهمان"}
          </p>
        ) : null}
      </div>

      <div className="ai-agent-pills">
        {agents.map((a) => (
          <button
            key={a.id}
            type="button"
            className={agentIds.includes(a.id) ? "is-on" : ""}
            onClick={() => toggleAgent(a.id)}
          >
            <FaIcon icon="fa-robot" />
            {a.name}
          </button>
        ))}
      </div>

      <div className="ai-mode-row">
        <label>
          <input type="radio" name="aimode" checked={mode === "single"} onChange={() => setMode("single")} />
          جداگانه
        </label>
        <label>
          <input type="radio" name="aimode" checked={mode === "collab"} onChange={() => setMode("collab")} />
          همکاری عامل‌ها
        </label>
        <label>
          <input type="radio" name="aimode" checked={mode === "reflect"} onChange={() => setMode("reflect")} />
          بازتاب نهایی
        </label>
        <label>
          <input type="checkbox" checked={showThinking} onChange={(e) => setShowThinking(e.target.checked)} />
          نمایش تفکر
        </label>
      </div>

      <div className="ai-thread" ref={scroller} role="log" aria-live="polite">
        {messages.map((m) => (
          <article key={m.id} className={m.role === "user" ? "ai-bubble is-user" : "ai-bubble is-bot"}>
            {m.thinking ? (
              <details className="ai-think">
                <summary>
                  <FaIcon icon="fa-brain" /> روند فکر
                </summary>
                <pre>{m.thinking}</pre>
              </details>
            ) : null}
            <div className="ai-bubble-body">{m.content}</div>
            <footer>{m.agentName}</footer>
          </article>
        ))}
        {busy ? (
          <article className="ai-bubble is-bot">
            {showThinking ? (
            <details className="ai-think is-live" open={thoughtOpen} onToggle={(e) => setThoughtOpen((e.target as HTMLDetailsElement).open)}>
              <summary>
                <FaIcon icon="fa-spinner" className="fa-spin" /> در حال استدلال · {faNum(elapsed)}ث — برای دیدن لمس کنید
              </summary>
              <pre>چیدن استدلال…</pre>
            </details>
            ) : null}
            <div className="ai-bubble-body">در حال نوشتن پاسخ…</div>
            <footer>{selectedNames.join("، ") || "دستیار تزنویسه"}</footer>
          </article>
        ) : null}
      </div>

      {!user ? (
        <p className="text-sm text-muted">
          مهمان تا سقف محدود می‌تواند بپرسد. برای تاریخچه و سهمیه بیشتر{" "}
          <Link to="/login" className="font-extrabold text-brand">
            وارد شوید
          </Link>
          .
        </p>
      ) : null}

      <form onSubmit={onSubmit} className="ai-compose">
        <label className="sr-only" htmlFor="aiq">
          پیام
        </label>
        <textarea
          id="aiq"
          rows={3}
          value={question}
          onChange={(e) => setQuestion(e.target.value)}
          placeholder={placeholder ?? "سؤال خود را بنویسید…"}
          required
        />
        <button className="btn-tz btn-primary-tz" type="submit" disabled={busy}>
          <FaIcon icon="fa-paper-plane" /> ارسال
        </button>
      </form>
    </section>
  );
}
