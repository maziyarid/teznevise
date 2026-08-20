import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState, type FormEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { getAdminSettingsMap, listBottomNav, saveAdminSettingsMap, saveBottomNav } from "@/lib/server/catalog";
import { toast } from "sonner";

export const Route = createFileRoute("/admin/settings")({ component: SettingsPage });

function SettingsPage() {
  const [map, setMap] = useState<Record<string, string>>({});
  const [nav, setNav] = useState<{ id: string; label: string; href: string; icon: string; sort: number; active: boolean }[]>([]);

  useEffect(() => {
    void getAdminSettingsMap().then(setMap);
    void listBottomNav().then((rows) =>
      setNav(rows.map((r) => ({ ...r, active: r.active === 1 }))),
    );
  }, []);

  function set(key: string, value: string) {
    setMap((m) => ({ ...m, [key]: value }));
  }

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    try {
      await saveAdminSettingsMap({ data: map });
      await saveBottomNav({ data: { items: nav } });
      toast.success("تنظیمات ذخیره شد");
    } catch {
      toast.error("ذخیره نشد");
    }
  }

  return (
    <AppPage
      title="تنظیمات سایت"
      hint="کلید درگاه و هوش مصنوعی فقط در سرور می‌ماند. اسکریپت خارجی Analytics/Clarity بارگذاری نمی‌شود."
    >
      <form className="grid gap-6" onSubmit={onSubmit}>
        <section className="surface-card grid gap-3">
          <h2 className="m-0 text-lg font-extrabold">تحلیل</h2>
          <Field id="ga_id" label="شناسه Google Analytics" value={map.ga_id ?? ""} onChange={set} />
          <Field id="clarity_id" label="شناسه Microsoft Clarity" value={map.clarity_id ?? ""} onChange={set} />
        </section>
        <section className="surface-card grid gap-3">
          <h2 className="m-0 text-lg font-extrabold">درگاه پرداخت</h2>
          <Field id="zarinpal_merchant" label="مرچنت زرین‌پال" value={map.zarinpal_merchant ?? ""} onChange={set} />
          <Field id="aqaye_pin" label="پین آقای پرداخت" value={map.aqaye_pin ?? ""} onChange={set} />
          <div className="field">
            <label htmlFor="gateway_mode">حالت درگاه</label>
            <select id="gateway_mode" value={map.gateway_mode ?? "sandbox"} onChange={(e) => set("gateway_mode", e.target.value)}>
              <option value="sandbox">آزمایشی (شبیه‌سازی)</option>
              <option value="live">زنده</option>
            </select>
          </div>
        </section>
        <section className="surface-card grid gap-3">
          <h2 className="m-0 text-lg font-extrabold">کلیدهای هوش مصنوعی</h2>
          <Field id="openrouter_key" label="OpenRouter API key" value={map.openrouter_key ?? ""} onChange={set} />
          <Field id="youcom_key" label="You.com API key" value={map.youcom_key ?? ""} onChange={set} />
          <Field id="tavily_key" label="Tavily API key" value={map.tavily_key ?? ""} onChange={set} />
        </section>
        <section className="surface-card grid gap-3">
          <h2 className="m-0 text-lg font-extrabold">نوار پایین (ویجت)</h2>
          {nav.map((item, i) => (
            <div key={item.id} className="grid gap-2 sm:grid-cols-4">
              <input value={item.label} onChange={(e) => updateNav(i, { label: e.target.value })} aria-label="برچسب" />
              <input dir="ltr" value={item.href} onChange={(e) => updateNav(i, { href: e.target.value })} aria-label="آدرس" />
              <input dir="ltr" value={item.icon} onChange={(e) => updateNav(i, { icon: e.target.value })} aria-label="آیکون" />
              <label className="flex items-center gap-2 text-sm">
                <input type="checkbox" checked={item.active} onChange={(e) => updateNav(i, { active: e.target.checked })} />
                فعال
              </label>
            </div>
          ))}
        </section>
        <button className="btn-tz btn-primary-tz" type="submit">
          ذخیره همه تنظیمات
        </button>
      </form>
    </AppPage>
  );

  function updateNav(i: number, patch: Partial<(typeof nav)[number]>) {
    setNav((rows) => rows.map((r, idx) => (idx === i ? { ...r, ...patch } : r)));
  }
}

function Field({
  id,
  label,
  value,
  onChange,
}: {
  id: string;
  label: string;
  value: string;
  onChange: (k: string, v: string) => void;
}) {
  return (
    <div className="field">
      <label htmlFor={id}>{label}</label>
      <input id={id} dir="ltr" value={value} onChange={(e) => onChange(id, e.target.value)} />
    </div>
  );
}
