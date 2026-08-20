import { Check } from "lucide-react";

export function CheckGrid({ items }: { items: string[] }) {
  return (
    <div className="check-grid" style={items.length <= 3 ? { gridTemplateColumns: `repeat(${items.length}, minmax(0,1fr))` } : undefined}>
      {items.map((item) => (
        <div key={item} className="check-card">
          <span className="mark" aria-hidden>
            <Check className="size-4" strokeWidth={3} />
          </span>
          <p>{item}</p>
        </div>
      ))}
    </div>
  );
}
