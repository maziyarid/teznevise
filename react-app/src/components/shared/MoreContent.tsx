import { useState, type ReactNode } from "react";
import { FaIcon } from "@/components/ui/FaIcon";
import { stripEmoji } from "@/lib/utils";

export function MoreContent({
  eyebrow = "محتوای تکمیلی",
  title = "جزئیات و توضیحات بیشتر",
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

  return (
    <section className="section section-sm">
      <div className="container-tz">
        <div className="seo-panel">
          <div className="text-xs font-extrabold text-brand">{eyebrow}</div>
          <h2>{title}</h2>
          {items[0] ? <p>{items[0]}</p> : null}
          {open ? (
            <>
              {items.slice(1).map((p) => (
                <p key={p.slice(0, 32)}>{p}</p>
              ))}
              {children}
            </>
          ) : null}
          <button
            type="button"
            className="seo-more-btn"
            aria-expanded={open}
            onClick={() => setOpen((v) => !v)}
          >
            <FaIcon icon={open ? "fa-chevron-up" : "fa-chevron-left"} />
            {open ? "نمایش کمتر" : "مشاهده بیشتر"}
          </button>
        </div>
      </div>
    </section>
  );
}
