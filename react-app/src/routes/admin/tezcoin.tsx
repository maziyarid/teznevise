import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState, type FormEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { adminAccounting, listCoinPacks, saveCoinPack } from "@/lib/server/wallet";
import { getAdminSettingsMap, saveAdminSettingsMap } from "@/lib/server/catalog";
import { faNum } from "@/lib/format";
import { toast } from "sonner";

export const Route = createFileRoute("/admin/tezcoin")({ component: TezcoinAdmin });

function TezcoinAdmin() {
  const [irr, setIrr] = useState("1000");
  const [profileBonus, setProfileBonus] = useState("1000");
  const [comment, setComment] = useState("25");
  const [share, setShare] = useState("40");
  const [ref, setRef] = useState("200");
  const [packs, setPacks] = useState<Awaited<ReturnType<typeof listCoinPacks>>>([]);
  const [acc, setAcc] = useState<Awaited<ReturnType<typeof adminAccounting>> | null>(null);
  const [title, setTitle] = useState("");
  const [coins, setCoins] = useState("500");
  const [price, setPrice] = useState("450000");

  const load = () => {
    void getAdminSettingsMap().then((m) => {
      setIrr(m.tezcoin_irr || "1000");
      setProfileBonus(m.profile_bonus || "1000");
      setComment(m.comment_reward || "25");
      setShare(m.share_reward || "40");
      setRef(m.referral_bonus || "200");
    });
    void listCoinPacks().then(setPacks);
    void adminAccounting().then(setAcc).catch(() => setAcc(null));
  };
  useEffect(() => {
    load();
  }, []);

  async function saveRates(e: FormEvent) {
    e.preventDefault();
    try {
      await saveAdminSettingsMap({
        data: {
          tezcoin_irr: irr,
          profile_bonus: profileBonus,
          comment_reward: comment,
          share_reward: share,
          referral_bonus: ref,
        },
      });
      toast.success("نرخ تزکوین ذخیره شد");
    } catch {
      toast.error("ذخیره نشد");
    }
  }

  return (
    <AppPage title="تزکوین و حسابداری" hint="قیمت هر سکه، پاداش‌ها و بسته‌های فروش را از اینجا تعیین کنید.">
      <div className="mb-6 grid gap-4 sm:grid-cols-3">
        <div className="surface-card">
          <p className="text-sm text-muted">کیف‌های فعال</p>
          <p className="text-3xl font-black text-brand">{faNum(acc?.totals.wallets ?? 0)}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">تزکوین در گردش</p>
          <p className="text-3xl font-black text-brand">{faNum(acc?.totals.coins ?? 0)}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">ریال پرداخت‌شده</p>
          <p className="text-3xl font-black text-brand">{faNum(acc?.totals.paid ?? 0)}</p>
        </div>
      </div>

      <form className="surface-card mb-6 grid gap-3 md:grid-cols-2" onSubmit={saveRates}>
        <div className="field">
          <label htmlFor="irr">قیمت هر تزکوین (ریال)</label>
          <input id="irr" dir="ltr" value={irr} onChange={(e) => setIrr(e.target.value)} />
        </div>
        <div className="field">
          <label htmlFor="pb">هدیه تکمیل پروفایل</label>
          <input id="pb" dir="ltr" value={profileBonus} onChange={(e) => setProfileBonus(e.target.value)} />
        </div>
        <div className="field">
          <label htmlFor="cr">پاداش نظر</label>
          <input id="cr" dir="ltr" value={comment} onChange={(e) => setComment(e.target.value)} />
        </div>
        <div className="field">
          <label htmlFor="sr">پاداش اشتراک</label>
          <input id="sr" dir="ltr" value={share} onChange={(e) => setShare(e.target.value)} />
        </div>
        <div className="field">
          <label htmlFor="rr">پاداش معرفی</label>
          <input id="rr" dir="ltr" value={ref} onChange={(e) => setRef(e.target.value)} />
        </div>
        <div className="flex items-end">
          <button className="btn-tz btn-primary-tz" type="submit">
            ذخیره نرخ‌ها
          </button>
        </div>
      </form>

      <form
        className="surface-card mb-6 grid gap-3 md:grid-cols-4"
        onSubmit={async (e) => {
          e.preventDefault();
          try {
            await saveCoinPack({ data: { title, coins: Number(coins), irr_price: Number(price) } });
            toast.success("بسته ذخیره شد");
            setTitle("");
            load();
          } catch {
            toast.error("بسته ذخیره نشد");
          }
        }}
      >
        <div className="field md:col-span-2">
          <label htmlFor="pt">عنوان بسته</label>
          <input id="pt" value={title} onChange={(e) => setTitle(e.target.value)} required />
        </div>
        <div className="field">
          <label htmlFor="pc">تعداد تزکوین</label>
          <input id="pc" dir="ltr" value={coins} onChange={(e) => setCoins(e.target.value)} />
        </div>
        <div className="field">
          <label htmlFor="pp">قیمت ریال</label>
          <input id="pp" dir="ltr" value={price} onChange={(e) => setPrice(e.target.value)} />
        </div>
        <button className="btn-tz btn-primary-tz md:col-span-4" type="submit">
          افزودن بسته
        </button>
      </form>

      <ul className="space-y-2">
        {packs.map((p) => (
          <li key={p.id} className="surface-card flex justify-between text-sm">
            <b>{p.title}</b>
            <span>
              {faNum(p.coins)} تزکوین — {faNum(p.irr_price)} ریال
            </span>
          </li>
        ))}
      </ul>
    </AppPage>
  );
}
