import { createFileRoute, Link } from "@tanstack/react-router";
import { useEffect, useMemo, useState, type ChangeEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { FIELD_CATALOG } from "@/lib/field-catalog";
import { getPageField, savePageField } from "@/lib/server/app";
import { PROPOSAL_PAGES, SERVICE_PAGES, STATIC_PAGES, THESIS_PAGES } from "@/lib/content";
import { toast } from "sonner";

export const Route = createFileRoute("/admin/fields/$slug")({ component: FieldEditor });

type FormState = {
  eyebrow: string;
  title: string;
  lead: string;
  features: string;
  body: string;
  cta_text: string;
  cta_url: string;
};

function defaultsFor(slug: string): FormState {
  if (SERVICE_PAGES[slug]) {
    const p = SERVICE_PAGES[slug];
    return {
      eyebrow: p.eyebrow,
      title: p.title,
      lead: p.lead,
      features: p.features.join("\n"),
      body: (p.body ?? []).join("\n\n"),
      cta_text: "شروع مشاوره رایگان",
      cta_url: "/inquiry",
    };
  }
  if (slug.startsWith("thesis-")) {
    const p = THESIS_PAGES[slug.slice(7)];
    if (p) {
      return {
        eyebrow: p.eyebrow,
        title: p.title,
        lead: p.lead,
        features: p.features.join("\n"),
        body: "",
        cta_text: "شروع مشاوره رایگان",
        cta_url: "/inquiry",
      };
    }
  }
  if (slug.startsWith("proposal-")) {
    const p = PROPOSAL_PAGES[slug.slice(9)];
    if (p) {
      return {
        eyebrow: p.eyebrow,
        title: p.title,
        lead: p.lead,
        features: p.features.join("\n"),
        body: "",
        cta_text: "شروع مشاوره رایگان",
        cta_url: "/inquiry",
      };
    }
  }
  const st = STATIC_PAGES[slug as keyof typeof STATIC_PAGES];
  if (st) {
    return {
      eyebrow: st.eyebrow,
      title: st.title,
      lead: st.lead,
      features: "",
      body: st.sections.map((s) => `${s.title}\n${s.body}`).join("\n\n"),
      cta_text: "",
      cta_url: "",
    };
  }
  return { eyebrow: "", title: "", lead: "", features: "", body: "", cta_text: "", cta_url: "" };
}

function FieldEditor() {
  const { slug } = Route.useParams();
  const meta = FIELD_CATALOG.find((p) => p.slug === slug);
  const base = useMemo(() => defaultsFor(slug), [slug]);
  const [form, setForm] = useState<FormState>(base);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    setForm(base);
    void getPageField({ data: { slug } }).then((row) => {
      if (!row) return;
      setForm({
        eyebrow: row.eyebrow || base.eyebrow,
        title: row.title || base.title,
        lead: row.lead || base.lead,
        features: row.features || base.features,
        body: row.body || base.body,
        cta_text: row.cta_text || base.cta_text,
        cta_url: row.cta_url || base.cta_url,
      });
    });
  }, [slug, base]);

  const set = (k: keyof FormState) => (e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
    setForm((f) => ({ ...f, [k]: e.target.value }));

  return (
    <AppPage title={meta?.title || slug} hint="هر فیلد خالی بماند، متن پیش‌فرض سایت نمایش داده می‌شود.">
      <div className="mb-4 flex flex-wrap items-center gap-3">
        <Link to="/admin/fields" className="link-arrow inline-flex">
          بازگشت به فهرست
        </Link>
        {meta ? (
          <a href={meta.path} className="text-sm font-bold text-brand" target="_blank" rel="noreferrer">
            مشاهده صفحه عمومی
          </a>
        ) : null}
      </div>
      <div className="grid items-start gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <form
          className="surface-card grid gap-3"
          onSubmit={async (e) => {
            e.preventDefault();
            setBusy(true);
            try {
              await savePageField({ data: { slug, ...form } });
              toast.success("فیلدها ذخیره شد و روی صفحه عمومی اعمال می‌شود");
            } catch {
              toast.error("ذخیره نشد");
            } finally {
              setBusy(false);
            }
          }}
        >
          <div className="field">
            <label htmlFor="eyebrow">برچسب کوچک بالای عنوان</label>
            <input id="eyebrow" value={form.eyebrow} onChange={set("eyebrow")} maxLength={80} />
            <span className="field-hint">{form.eyebrow.length}/80 — مثلاً «خدمات پژوهشی»</span>
          </div>
          <div className="field">
            <label htmlFor="title">عنوان صفحه</label>
            <input id="title" value={form.title} onChange={set("title")} maxLength={200} />
            <span className="field-hint">{form.title.length}/200</span>
          </div>
          <div className="field">
            <label htmlFor="lead">مقدمه کوتاه</label>
            <textarea id="lead" value={form.lead} onChange={set("lead")} rows={3} maxLength={800} />
            <span className="field-hint">{form.lead.length}/800 — یک یا دو جمله برای هیرو</span>
          </div>
          <div className="field">
            <label htmlFor="features">ویژگی‌ها و خروجی‌ها</label>
            <textarea id="features" value={form.features} onChange={set("features")} rows={6} />
            <span className="field-hint">هر خط یک مورد. در صفحه به‌صورت فهرست تیک‌دار دیده می‌شود.</span>
          </div>
          <div className="field">
            <label htmlFor="body">متن تکمیلی</label>
            <textarea id="body" value={form.body} onChange={set("body")} rows={8} />
            <span className="field-hint">پاراگراف‌ها را با یک خط خالی جدا کنید.</span>
          </div>
          <div className="grid gap-3 sm:grid-cols-2">
            <div className="field">
              <label htmlFor="cta">متن دکمه</label>
              <input id="cta" value={form.cta_text} onChange={set("cta_text")} maxLength={80} />
            </div>
            <div className="field">
              <label htmlFor="ctaurl">آدرس دکمه</label>
              <input id="ctaurl" dir="ltr" value={form.cta_url} onChange={set("cta_url")} placeholder="/inquiry" />
            </div>
          </div>
          <div className="flex flex-wrap gap-2">
            <button className="btn-tz btn-primary-tz" type="submit" disabled={busy}>
              ذخیره فیلدها
            </button>
            <button
              type="button"
              className="btn-tz btn-light-tz"
              onClick={() => setForm(base)}
            >
              بازگشت به پیش‌فرض
            </button>
          </div>
        </form>
        <aside className="surface-card preview-card" aria-label="پیش‌نمایش">
          <p className="eyebrow">{form.eyebrow || "برچسب"}</p>
          <h2 className="mt-2 text-2xl font-extrabold">{form.title || "عنوان صفحه"}</h2>
          <p className="mt-2 text-sm text-muted">{form.lead || "مقدمه اینجا دیده می‌شود."}</p>
          {form.features.trim() ? (
            <ul className="mt-4 space-y-1 text-sm">
              {form.features
                .split(/\n+/)
                .map((x) => x.trim())
                .filter(Boolean)
                .map((x) => (
                  <li key={x}>✓ {x}</li>
                ))}
            </ul>
          ) : null}
          {form.cta_text ? (
            <span className="btn-tz btn-primary-tz mt-5 inline-flex">{form.cta_text}</span>
          ) : null}
        </aside>
      </div>
    </AppPage>
  );
}
