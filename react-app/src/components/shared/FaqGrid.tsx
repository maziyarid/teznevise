import { faNum } from "@/lib/format";
import { stripEmoji } from "@/lib/utils";
import type { FaqItem } from "@/lib/page-copy";

export function FaqGrid({ items }: { items: FaqItem[] }) {
  if (!items.length) return null;
  return (
    <section className="section bg-soft">
      <div className="container-tz">
        <div className="section-head center">
          <span className="eyebrow">پرسش‌های پرتکرار</span>
          <h2>قبل از سفارش، این‌ها را بخوانید</h2>
          <p>پاسخ‌ها شفاف است؛ اگر مورد شما فرق دارد، مشاوره رایگان بگیرید.</p>
        </div>
        <div className="faq-grid">
          {items.map((f, i) => (
            <article key={`${f.q}-${i}`} className={`faq-card tone-${(i % 9) + 1}`}>
              <span className="faq-num" aria-hidden>
                {faNum(i + 1)}
              </span>
              <h3>{stripEmoji(f.q)}</h3>
              <p>{stripEmoji(f.a)}</p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
