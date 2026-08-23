import { faNum } from "@/lib/format";
import { stripEmoji } from "@/lib/utils";
import type { FaqItem } from "@/lib/page-copy";

export function FaqGrid({
  items,
  embedded,
}: {
  items: FaqItem[];
  embedded?: boolean;
}) {
  if (!items.length) return null;
  const inner = (
    <>
      <div className="section-head center">
        <span className="eyebrow">پرسش‌های پرتکرار</span>
        <h2>قبل از سفارش، این‌ها را بخوانید</h2>
        <p>پاسخ‌ها شفاف است؛ اگر مورد شما فرق دارد، مشاوره رایگان بگیرید.</p>
      </div>
      <div className="faq-grid">
        {items.map((f, i) => (
          <article key={`${f.q}-${i}`} className={`faq-card tone-${(i % 9) + 1}`}>
            <div className="faq-card__head">
              <span className="faq-num" aria-hidden>
                {faNum(i + 1)}
              </span>
              <h3>{stripEmoji(f.q)}</h3>
            </div>
            <p>{stripEmoji(f.a)}</p>
          </article>
        ))}
      </div>
    </>
  );
  if (embedded) return <div className="tool-guide-block">{inner}</div>;
  return (
    <section className="section bg-soft">
      <div className="container-tz">{inner}</div>
    </section>
  );
}