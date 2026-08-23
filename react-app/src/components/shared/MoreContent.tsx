import { useState, type ReactNode } from "react";
import { FaIcon } from "@/components/ui/FaIcon";
import { stripEmoji } from "@/lib/utils";

export function MoreContent({
  eyebrow = "محتوای تکمیلی",
  title = "",
  paragraphs,
  children,
}: {
  eyebrow?: string;
  title?: string;
  paragraphs?: string[];
  children?: ReactNode;
}) {
  const [open, setOpen] = useState(false);
  const items = (paragraphs ?? []).map((p) => stripEmoji(p).trim()).filter(Boolean);
  if (!items.length && !children) return null;
  const preview = items.slice(0, 3);
  const rest = items.slice(3);

  return (
    <section className="section-sm tz-more-content">
      <div className="container-tz">
        <div className="seo-panel tz-classic-disclosure">
          <div className="text-xs font-extrabold text-brand">{eyebrow}</div>
          {title ? <h2>{title}</h2> : null}
          {preview.map((p) => (
            <p key={p.slice(0, 32)}>{p}</p>
          ))}
          {open ? (
            <>
              {rest.map((p) => (
                <p key={p.slice(0, 32)}>{p}</p>
              ))}
              {children}
            </>
          ) : null}
          {rest.length > 0 || children ? (
            <button
              type="button"
              className="seo-more-btn"
              aria-expanded={open}
              onClick={() => setOpen((v) => !v)}
            >
              <FaIcon icon={open ? "fa-chevron-up" : "fa-chevron-left"} />
              {open ? "نمایش کمتر" : "مشاهده بیشتر"}
            </button>
          ) : null}
        </div>
      </div>
    </section>
  );
}
