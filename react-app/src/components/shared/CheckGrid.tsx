import { faNum } from "@/lib/format";
import { stripEmoji } from "@/lib/utils";

export function CheckGrid({ items }: { items: string[] }) {
  const cols = items.length <= 3 ? items.length : items.length === 4 ? 4 : 3;
  return (
    <div className="check-grid" style={{ gridTemplateColumns: `repeat(${Math.min(cols, 3)}, minmax(0,1fr))` }}>
      {items.map((item, i) => (
        <div key={`${item}-${i}`} className={`check-card tone-${(i % 9) + 1}`}>
          <span className="mark" aria-hidden>
            {faNum(i + 1)}
          </span>
          <p>{stripEmoji(item)}</p>
        </div>
      ))}
    </div>
  );
}