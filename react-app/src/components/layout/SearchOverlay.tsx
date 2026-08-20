import { useEffect, useMemo, useState } from "react";
import { Link, useNavigate } from "@tanstack/react-router";
import { Search, X } from "lucide-react";
import { ARTICLES, SERVICES, TOOLS } from "@/lib/content";
import { getPublicSite, logSearch } from "@/lib/server/catalog";

export function SearchOverlay({ open, onClose }: { open: boolean; onClose: () => void }) {
  const [q, setQ] = useState("");
  const [popular, setPopular] = useState<string[]>([]);
  const navigate = useNavigate();

  useEffect(() => {
    if (!open) return;
    void getPublicSite()
      .then((s) => setPopular(s.popular))
      .catch(() => setPopular([]));
  }, [open]);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open, onClose]);

  const results = useMemo(() => {
    const s = q.trim();
    const pages = [
      ...SERVICES.map((x) => ({ title: x.title, to: x.to, kind: "خدمت" })),
      ...TOOLS.map((x) => ({ title: x.title, to: `/tools/${x.slug}`, kind: "ابزار" })),
      ...ARTICLES.map((x) => ({ title: x.title, to: `/blog/${x.slug}`, kind: "مقاله" })),
    ];
    if (!s) return [];
    return pages.filter((p) => p.title.includes(s)).slice(0, 8);
  }, [q]);

  if (!open) return null;

  return (
    <div className="search-overlay" role="dialog" aria-modal="true" aria-label="جستجو">
      <button type="button" className="search-overlay-bg" aria-label="بستن جستجو" onClick={onClose} />
      <div className="search-overlay-panel">
        <form
          className="search-box"
          onSubmit={(e) => {
            e.preventDefault();
            const query = q.trim();
            if (query.length >= 2) void logSearch({ data: { query } }).catch(() => {});
            void navigate({ to: "/search", search: { q: query } });
            onClose();
          }}
        >
          <Search className="size-5 text-brand" aria-hidden />
          <input
            autoFocus
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="جستجوی خدمات، ابزار و مقالات…"
            aria-label="عبارت جستجو"
          />
          <button type="button" className="icon-btn" onClick={onClose} aria-label="بستن">
            <X className="size-4" />
          </button>
        </form>
        {results.length ? (
          <ul className="search-hits">
            {results.map((r) => (
              <li key={r.to}>
                <Link to={r.to} onClick={onClose}>
                  <small>{r.kind}</small>
                  {r.title}
                </Link>
              </li>
            ))}
          </ul>
        ) : (
          <div className="search-popular">
            <p>پرجستجوترین‌ها</p>
            <div>
              {popular.map((item) => (
                <button
                  key={item}
                  type="button"
                  className="chip-tz"
                  onClick={() => {
                    setQ(item);
                    void logSearch({ data: { query: item } }).catch(() => {});
                  }}
                >
                  {item}
                </button>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
