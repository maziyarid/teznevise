import { Link } from "@tanstack/react-router";
import { Lock } from "lucide-react";
import { toolPageCopy, TOOL_GROUPS, TOOLS, type ToolDef } from "@/lib/content";
import { usePageOverlay } from "@/lib/page-overlay";
import { CALC_MAP } from "./Calculators";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { CheckGrid } from "@/components/shared/CheckGrid";
import { FaqGrid } from "@/components/shared/FaqGrid";
import { MoreContent } from "@/components/shared/MoreContent";
import { stripEmoji } from "@/lib/utils";
import { cn } from "@/lib/utils";

export function ToolsWorkspace({ tool }: { tool: ToolDef }) {
  const { user } = useCurrentUserState();
  const Calc = CALC_MAP[tool.slug as keyof typeof CALC_MAP];
  const locked = tool.tier === "pro" && !user;
  const overlay = usePageOverlay(`tool-${tool.slug}`);
  const copy = toolPageCopy(tool.slug);
  const title = overlay?.title?.trim() || copy.heroTitle || tool.title;
  const text = overlay?.lead?.trim() || copy.lead || tool.text;
  const features = overlay?.features
    ? overlay.features.split(/\n+/).map((s) => s.trim()).filter(Boolean)
    : copy.features;
  const faqs = copy.faqs;
  const context = `ابزار فعال: ${title}. ${text}`;

  return (
    <div className="tools-shell">
      <aside className="tools-rail" aria-label="فهرست ابزارها">
        {TOOL_GROUPS.map((g) => {
          const items = TOOLS.filter((t) => t.group === g);
          if (!items.length) return null;
          return (
            <div key={g} className="tools-group">
              <p className="tools-group-title">{g}</p>
              {items.map((t) => (
                <Link
                  key={t.slug}
                  to="/tools/$slug"
                  params={{ slug: t.slug }}
                  className={cn("tool-item", t.slug === tool.slug && "is-active")}
                >
                  <span>{shortToolLabel(t.slug, t.title)}</span>
                  <em className={t.tier === "pro" ? "badge-pro" : "badge-free"}>
                    {t.tier === "pro" ? "ویژه" : "رایگان"}
                  </em>
                </Link>
              ))}
            </div>
          );
        })}
      </aside>
      <div className="tools-main">
        <header className="tools-head">
          <span className={tool.tier === "pro" ? "badge-pro" : "badge-free"}>
            {tool.tier === "pro" ? "ابزار ویژه" : "ابزار رایگان"}
          </span>
          <h1>{stripEmoji(title)}</h1>
          <p>{stripEmoji(text)}</p>
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
                <p className="mb-0 text-muted">
                  این ابزار کاملاً با هوش مصنوعی کار می‌کند. سؤال را پایین بنویسید.
                </p>
              </div>
            )}
            {features.length ? (
              <div className="tool-guide-block">
                <div className="section-head">
                  <span className="eyebrow">چطور از این ابزار استفاده کنیم؟</span>
                  <h2>راهنمای استفاده</h2>
                </div>
                <CheckGrid items={features} />
              </div>
            ) : null}
            {copy.sections.map((s, i) => (
              <article key={s.heading} className={`service-card tone-${(i % 9) + 1} tool-guide-card`}>
                <h3>{s.heading}</h3>
                {s.paragraphs.map((p) => (
                  <p key={p.slice(0, 32)}>{p}</p>
                ))}
              </article>
            ))}
            <FaqGrid items={faqs} embedded />
            {copy.body?.length ? (
              <MoreContent paragraphs={copy.body} title="توضیحات و محتوای صفحه" />
            ) : null}
            <div className="cta-band cta-band-inline">
              <div>
                <h2>{copy.ctaTitle || "برای تفسیر نتایج کمک می‌خواهید؟"}</h2>
                <p>
                  {copy.ctaText ||
                    "تیم تحلیل آماری تزنویسه نتایج را به‌صورت علمی برای پژوهش شما تفسیر می‌کند."}
                </p>
              </div>
              <Link to="/inquiry" className="btn-tz btn-light-tz btn-lg-tz">
                سفارش تحلیل آماری
              </Link>
            </div>
          </>
        )}
      </div>
    </div>
  );
}

function shortToolLabel(slug: string, title: string) {
  const map: Record<string, string> = {
    "descriptive-statistics": "آمار توصیفی",
    "sample-size": "حجم نمونه",
    "cronbachs-alpha": "آلفای کرونباخ",
    "pearson-correlation": "همبستگی پیرسون",
    spearman: "همبستگی اسپیرمن",
    "t-test": "آزمون t",
    regression: "رگرسیون",
    anova: "تحلیل واریانس",
    "chi-square": "خی‌دو",
    "power-analysis": "توان آزمون",
    "content-validity": "روایی محتوا",
    kr20: "KR-20 / KR-21",
    "cohens-kappa": "کاپای کوهن",
    icc: "ICC",
    "mann-whitney": "من-ویتنی",
    wilcoxon: "ویلکاکسون",
    "kruskal-wallis": "کروسکال-والیس",
    "goodness-of-fit": "نیکویی برازش",
    price: "برآورد هزینه",
    "method-advisor": "مشاور روش",
    "apa-citation": "ارجاع APA",
    "theme-extractor": "استخراج مضمون",
  };
  return map[slug] || title;
}