import { useEffect, useState, type FormEvent } from "react";
import { Link } from "@tanstack/react-router";
import { Sparkles } from "lucide-react";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { askAgent, listAgents, setMyAgent } from "@/lib/server/ai-hub";
import { toast } from "sonner";

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
  const [answer, setAnswer] = useState("");
  const [busy, setBusy] = useState(false);
  const [agentId, setAgentId] = useState("");
  const [agents, setAgents] = useState<{ id: string; name: string }[]>([]);

  useEffect(() => {
    void listAgents().then(setAgents).catch(() => setAgents([]));
  }, []);

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    setBusy(true);
    setAnswer("");
    try {
      if (agentId) await setMyAgent({ data: { agentId } }).catch(() => {});
      const res = await askAgent({ data: { tool, context, question, agentId: agentId || undefined } });
      if (res.ok) setAnswer(res.text);
      else toast.error(res.error === "AI is not available" ? "دستیار هوشمند فعلاً در دسترس نیست." : res.error);
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "ورود لازم است");
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="ask-ai ask-ai-pro surface-card">
      <div className="mb-3 flex items-center gap-2">
        <span className="icon-box">
          <Sparkles className="size-5" />
        </span>
        <div>
          <h3 className="m-0">از عامل هوش مصنوعی بپرس</h3>
          <p className="mt-0 mb-0 text-sm text-muted">عامل را انتخاب کنید؛ مهارت‌های Markdown بارگذاری‌شده در پاسخ لحاظ می‌شود.</p>
        </div>
      </div>
      {!user ? (
        <p className="text-sm text-muted">
          برای استفاده از دستیار،{" "}
          <Link to="/login" className="font-extrabold text-brand">
            وارد شوید
          </Link>
          .
        </p>
      ) : (
        <form onSubmit={onSubmit} className="grid gap-3">
          <div className="field">
            <label htmlFor="agent">عامل</label>
            <select id="agent" value={agentId} onChange={(e) => setAgentId(e.target.value)}>
              <option value="">پیشنهاد خودکار</option>
              {agents.map((a) => (
                <option key={a.id} value={a.id}>
                  {a.name}
                </option>
              ))}
            </select>
          </div>
          <div className="field">
            <label htmlFor="aiq">سؤال یا داده</label>
            <textarea
              id="aiq"
              rows={4}
              value={question}
              onChange={(e) => setQuestion(e.target.value)}
              placeholder={placeholder ?? "مثلاً: با این داده‌ها کدام آزمون مناسب است؟"}
              required
            />
          </div>
          <button className="btn-tz btn-primary-tz" type="submit" disabled={busy}>
            {busy ? "در حال تحلیل…" : "بپرس"}
          </button>
        </form>
      )}
      {answer ? <div className="ai-answer mt-4">{answer}</div> : null}
    </section>
  );
}
