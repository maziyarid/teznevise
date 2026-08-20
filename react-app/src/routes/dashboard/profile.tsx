import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState, type FormEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { getMyProfile, updateMyProfile } from "@/lib/server/app";
import { listAgents } from "@/lib/server/ai-hub";
import { toast } from "sonner";

export const Route = createFileRoute("/dashboard/profile")({ component: ProfilePage });

function ProfilePage() {
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [university, setUniversity] = useState("");
  const [field, setField] = useState("");
  const [degree, setDegree] = useState("");
  const [city, setCity] = useState("");
  const [bio, setBio] = useState("");
  const [agent, setAgent] = useState("");
  const [agents, setAgents] = useState<{ id: string; name: string }[]>([]);
  const [ref, setRef] = useState("");
  const [complete, setComplete] = useState(false);

  useEffect(() => {
    void getMyProfile().then((p) => {
      setName(p.display_name ?? "");
      setPhone(p.phone ?? "");
      setUniversity(p.university ?? "");
      setField(p.field ?? "");
      setDegree(p.degree ?? "");
      setCity(p.city ?? "");
      setBio(p.bio ?? "");
      setAgent(p.default_agent_id ?? "");
      setComplete(p.profile_complete === 1);
    });
    void listAgents().then(setAgents).catch(() => setAgents([]));
  }, []);

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    try {
      const res = await updateMyProfile({
        data: {
          display_name: name,
          phone,
          university,
          field,
          degree,
          city,
          bio,
          default_agent_id: agent || undefined,
          referral_code_in: ref || undefined,
        },
      });
      setComplete(res.profile.profile_complete === 1);
      if (res.bonus.granted) toast.success(`${res.bonus.coins} تزکوین هدیه پروفایل واریز شد`);
      else toast.success("پروفایل ذخیره شد");
    } catch {
      toast.error("ذخیره نشد");
    }
  }

  return (
    <AppPage
      title="پروفایل پژوهشی"
      hint={complete ? "پروفایل کامل است و نظر دادن در بلاگ برای شما باز است." : "نام، موبایل، دانشگاه و رشته را پر کنید تا ۱۰۰۰ تزکوین هدیه بگیرید."}
    >
      <form className="surface-card grid max-w-2xl gap-3" onSubmit={onSubmit}>
        <div className="grid gap-3 sm:grid-cols-2">
          <div className="field">
            <label htmlFor="dn">نام و نام خانوادگی</label>
            <input id="dn" value={name} onChange={(e) => setName(e.target.value)} required />
          </div>
          <div className="field">
            <label htmlFor="ph">موبایل</label>
            <input id="ph" dir="ltr" value={phone} onChange={(e) => setPhone(e.target.value)} required />
          </div>
          <div className="field">
            <label htmlFor="un">دانشگاه</label>
            <input id="un" value={university} onChange={(e) => setUniversity(e.target.value)} required />
          </div>
          <div className="field">
            <label htmlFor="fd">رشته</label>
            <input id="fd" value={field} onChange={(e) => setField(e.target.value)} required />
          </div>
          <div className="field">
            <label htmlFor="dg">مقطع</label>
            <select id="dg" value={degree} onChange={(e) => setDegree(e.target.value)}>
              <option value="">انتخاب کنید</option>
              <option>کارشناسی</option>
              <option>کارشناسی ارشد</option>
              <option>دکتری</option>
            </select>
          </div>
          <div className="field">
            <label htmlFor="ct">شهر</label>
            <input id="ct" value={city} onChange={(e) => setCity(e.target.value)} />
          </div>
        </div>
        <div className="field">
          <label htmlFor="bio">معرفی کوتاه پژوهشی</label>
          <textarea id="bio" rows={4} value={bio} onChange={(e) => setBio(e.target.value)} />
        </div>
        <div className="field">
          <label htmlFor="ag">عامل هوش مصنوعی پیش‌فرض</label>
          <select id="ag" value={agent} onChange={(e) => setAgent(e.target.value)}>
            <option value="">انتخاب خودکار</option>
            {agents.map((a) => (
              <option key={a.id} value={a.id}>
                {a.name}
              </option>
            ))}
          </select>
        </div>
        <div className="field">
          <label htmlFor="rf">کد معرفی (اختیاری)</label>
          <input id="rf" dir="ltr" value={ref} onChange={(e) => setRef(e.target.value)} />
        </div>
        <button className="btn-tz btn-primary-tz" type="submit">
          ذخیره پروفایل
        </button>
      </form>
    </AppPage>
  );
}
