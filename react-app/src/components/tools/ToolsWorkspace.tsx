import { Link } from "@tanstack/react-router";
import { Lock, Sparkles } from "lucide-react";
import { TOOLS, type ToolDef } from "@/lib/content";
import { CALC_MAP } from "./Calculators";
import { AskAiPanel } from "./AskAiPanel";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { cn } from "@/lib/utils";

const GROUPS = ["آمار پایه", "آمار پیشرفته", "دستیار پژوهشی"] as const;

export function ToolsWorkspace({ tool }: { tool: ToolDef }) {
  const { user } = useCurrentUserState();
  const Calc = CALC_MAP[tool.slug as keyof typeof CALC_MAP];
  const locked = tool.tier === "pro" && !user;
  const context = `ابزار فعال: ${tool.title}. ${tool.text}`;

  return (
    <div className="tools-shell">
      <aside className="tools-rail" aria-label="فهرست ابزارها">
        {GROUPS.map((g) => (
          <div key={g} className="tools-group">
            <p className="tools-group-title">{g}</p>
            {TOOLS.filter((t) => t.group === g).map((t) => (
              <Link
                key={t.slug}
                to="/tools/$slug"
                params={{ slug: t.slug }}
                className={cn("tool-item", t.slug === tool.slug && "is-active")}
              >
                <span>{t.title}</span>
                <em className={t.tier === "pro" ? "badge-pro" : "badge-free"}>{t.tier === "pro" ? "ویژه" : "رایگان"}</em>
              </Link>
            ))}
          </div>
        ))}
      </aside>
      <div className="tools-main">
        <header className="tools-head">
          <span className={tool.tier === "pro" ? "badge-pro" : "badge-free"}>{tool.tier === "pro" ? "ابزار ویژه" : "ابزار رایگان"}</span>
          <h1>{tool.title}</h1>
          <p>{tool.text}</p>
        </header>
        {locked ? (
          <div className="tool-lock">
            <Lock className="size-8 text-brand" />
            <h2>این ابزار برای اعضای واردشده است</h2>
            <p>با ثبت‌نام از طریق شماره موبایل، ANOVA، خی‌دو، توان آزمون و دستیار هوش مصنوعی باز می‌شود.</p>
            <Link to="/login" className="btn-tz btn-primary-tz">
              ورود / ثبت‌نام
            </Link>
          </div>
        ) : (
          <>
            {Calc ? <Calc /> : (
              <div className="tool-card">
                <p className="mb-0 flex items-center gap-2 text-muted">
                  <Sparkles className="size-4 text-brand" /> این ابزار کاملاً با هوش مصنوعی کار می‌کند. سؤال را پایین بنویسید.
                </p>
              </div>
            )}
            <AskAiPanel
              tool={tool.title}
              context={context}
              placeholder={
                tool.kind === "ai"
                  ? "موضوع، رشته و سؤال روش را بنویسید…"
                  : "اگر می‌خواهید همین محاسبه را هوش مصنوعی برایتان تفسیر یا تکرار کند، اینجا بنویسید."
              }
            />
          </>
        )}
      </div>
    </div>
  );
}
