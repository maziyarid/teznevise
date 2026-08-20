import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState, type FormEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { listAdminAgents, listSkills, saveAgent, saveSkill } from "@/lib/server/ai-hub";
import { TOOLS } from "@/lib/content";
import { toast } from "sonner";

export const Route = createFileRoute("/admin/agents")({ component: AgentsAdmin });

function AgentsAdmin() {
  const [agents, setAgents] = useState<Awaited<ReturnType<typeof listAdminAgents>>>([]);
  const [skills, setSkills] = useState<Awaited<ReturnType<typeof listSkills>>>([]);
  const [name, setName] = useState("");
  const [provider, setProvider] = useState<"xai" | "openrouter" | "tavily" | "you">("xai");
  const [model, setModel] = useState("");
  const [prompt, setPrompt] = useState("");
  const [slug, setSlug] = useState(TOOLS[0]?.slug ?? "descriptive-statistics");
  const [stitle, setStitle] = useState("");
  const [body, setBody] = useState("");

  const load = () => {
    void listAdminAgents().then(setAgents).catch(() => setAgents([]));
    void listSkills().then(setSkills).catch(() => setSkills([]));
  };
  useEffect(() => {
    load();
  }, []);

  async function onAgent(e: FormEvent) {
    e.preventDefault();
    try {
      await saveAgent({ data: { name, provider, model, system_prompt: prompt } });
      toast.success("عامل ذخیره شد");
      setName("");
      setPrompt("");
      load();
    } catch {
      toast.error("ذخیره عامل ممکن نشد");
    }
  }

  async function onSkill(e: FormEvent) {
    e.preventDefault();
    try {
      await saveSkill({ data: { tool_slug: slug, title: stitle, body_md: body } });
      toast.success("مهارت بارگذاری شد");
      setStitle("");
      setBody("");
      load();
    } catch {
      toast.error("بارگذاری مهارت ممکن نشد");
    }
  }

  return (
    <AppPage title="عامل‌های هوش مصنوعی و مهارت‌ها" hint="نام عامل‌ها را شما تعیین می‌کنید. کلید OpenRouter، You.com و Tavily در تنظیمات ذخیره می‌شود.">
      <div className="grid gap-6 lg:grid-cols-2">
        <form className="surface-card grid gap-3" onSubmit={onAgent}>
          <h2 className="m-0 text-lg font-extrabold">عامل جدید</h2>
          <div className="field">
            <label htmlFor="an">نام نمایشی</label>
            <input id="an" value={name} onChange={(e) => setName(e.target.value)} required />
          </div>
          <div className="field">
            <label htmlFor="ap">ارائه‌دهنده</label>
            <select id="ap" value={provider} onChange={(e) => setProvider(e.target.value as typeof provider)}>
              <option value="xai">xAI Grok</option>
              <option value="openrouter">OpenRouter</option>
              <option value="tavily">Tavily + خلاصه</option>
              <option value="you">You.com + خلاصه</option>
            </select>
          </div>
          <div className="field">
            <label htmlFor="am">مدل (اختیاری)</label>
            <input id="am" dir="ltr" value={model} onChange={(e) => setModel(e.target.value)} />
          </div>
          <div className="field">
            <label htmlFor="as">پرامپت سیستم</label>
            <textarea id="as" rows={5} value={prompt} onChange={(e) => setPrompt(e.target.value)} />
          </div>
          <button className="btn-tz btn-primary-tz" type="submit">
            ذخیره عامل
          </button>
        </form>
        <form className="surface-card grid gap-3" onSubmit={onSkill}>
          <h2 className="m-0 text-lg font-extrabold">مهارت Markdown</h2>
          <div className="field">
            <label htmlFor="ss">ابزار</label>
            <select id="ss" value={slug} onChange={(e) => setSlug(e.target.value)}>
              <option value="*">همه ابزارها</option>
              {TOOLS.map((t) => (
                <option key={t.slug} value={t.slug}>
                  {t.title}
                </option>
              ))}
            </select>
          </div>
          <div className="field">
            <label htmlFor="st">عنوان مهارت</label>
            <input id="st" value={stitle} onChange={(e) => setStitle(e.target.value)} required />
          </div>
          <div className="field">
            <label htmlFor="sb">متن Markdown / پرامپت</label>
            <textarea id="sb" rows={8} value={body} onChange={(e) => setBody(e.target.value)} required />
          </div>
          <label className="btn-tz btn-light-tz cursor-pointer">
            بارگذاری فایل .md
            <input
              type="file"
              accept=".md,text/markdown,text/plain"
              className="sr-only"
              onChange={async (e) => {
                const f = e.target.files?.[0];
                if (!f) return;
                setStitle(f.name.replace(/\.md$/i, ""));
                setBody(await f.text());
              }}
            />
          </label>
          <button className="btn-tz btn-primary-tz" type="submit">
            ذخیره مهارت
          </button>
        </form>
      </div>
      <div className="mt-6 grid gap-6 lg:grid-cols-2">
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">عامل‌ها</h2>
          <ul className="space-y-2 text-sm">
            {agents.map((a) => (
              <li key={a.id}>
                <b>{a.name}</b> — {a.provider}
              </li>
            ))}
          </ul>
        </section>
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">مهارت‌ها</h2>
          <ul className="space-y-2 text-sm">
            {skills.map((s) => (
              <li key={s.id}>
                <b>{s.title}</b> ({s.tool_slug})
              </li>
            ))}
          </ul>
        </section>
      </div>
    </AppPage>
  );
}
