import { createFileRoute, Link } from "@tanstack/react-router";
import { useMemo, useState } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { FIELD_CATALOG, FIELD_GROUPS } from "@/lib/field-catalog";

export const Route = createFileRoute("/admin/fields")({ component: FieldsIndex });

function FieldsIndex() {
  const [q, setQ] = useState("");
  const [group, setGroup] = useState<string>("همه");
  const filtered = useMemo(() => {
    const s = q.trim();
    return FIELD_CATALOG.filter((p) => {
      const okGroup = group === "همه" || p.group === group;
      const okQ = !s || p.title.includes(s) || p.path.includes(s) || p.slug.includes(s);
      return okGroup && okQ;
    });
  }, [q, group]);

  return (
    <AppPage
      title="فیلدهای سفارشی صفحات"
      hint="عنوان، مقدمه، ویژگی‌ها و دکمه هر صفحه را از اینجا عوض کنید؛ ذخیره فوری روی صفحه عمومی دیده می‌شود."
    >
      <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input
          value={q}
          onChange={(e) => setQ(e.target.value)}
          placeholder="جستجوی عنوان یا مسیر…"
          aria-label="جستجوی صفحات"
        />
        <div className="flex flex-wrap gap-2">
          {["همه", ...FIELD_GROUPS].map((g) => (
            <button
              key={g}
              type="button"
              className={`btn-tz ${group === g ? "btn-primary-tz" : "btn-light-tz"}`}
              onClick={() => setGroup(g)}
            >
              {g}
            </button>
          ))}
        </div>
      </div>
      <ul className="grid gap-2 md:grid-cols-2">
        {filtered.map((p) => (
          <li key={p.slug} className="surface-card">
            <Link to="/admin/fields/$slug" params={{ slug: p.slug }} className="block">
              <span className="text-xs font-bold text-brand">{p.group}</span>
              <b className="mt-1 block">{p.title}</b>
              <p className="text-xs text-muted" dir="ltr">
                {p.path}
              </p>
            </Link>
          </li>
        ))}
      </ul>
      {filtered.length === 0 ? <p className="mt-4 text-muted">صفحه‌ای با این جستجو پیدا نشد.</p> : null}
    </AppPage>
  );
}
