import { createFileRoute, useRouterState } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { Coins, CreditCard } from "lucide-react";
import { AppPage } from "@/components/layout/AppFrame";
import { getMyWallet } from "@/lib/server/wallet";
import { listMyPayments, startPayment } from "@/lib/server/payments";
import { faMoney, faNum } from "@/lib/format";
import { toast } from "sonner";

export const Route = createFileRoute("/dashboard/wallet")({ component: WalletPage });

function WalletPage() {
  const search = useRouterState({ select: (s) => s.location.search });
  const [data, setData] = useState<Awaited<ReturnType<typeof getMyWallet>> | null>(null);
  const [pays, setPays] = useState<Awaited<ReturnType<typeof listMyPayments>>>([]);
  const [busy, setBusy] = useState<string | null>(null);

  const load = () => {
    void getMyWallet().then(setData).catch(() => setData(null));
    void listMyPayments().then(setPays).catch(() => setPays([]));
  };
  useEffect(() => {
    load();
  }, []);

  useEffect(() => {
    if (typeof search === "object" && search && "paid" in search) {
      toast.success("تزکوین به کیف پول افزوده شد");
    }
  }, [search]);

  return (
    <AppPage title="کیف پول و حسابداری" hint="تزکوین واحد اعتبار تزنویسه است؛ برای ابزار ویژه، دستیار هوش مصنوعی و پاداش فعالیت.">
      <div className="mb-6 grid gap-4 sm:grid-cols-3">
        <div className="surface-card">
          <p className="text-sm text-muted">موجودی</p>
          <p className="mt-1 flex items-center gap-2 text-3xl font-black text-brand">
            <Coins className="size-7" />
            {faNum(data?.balance ?? 0)}
          </p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">نرخ هر تزکوین</p>
          <p className="mt-1 text-2xl font-black">{faMoney(data?.irr ?? 1000)}</p>
        </div>
        <div className="surface-card">
          <p className="text-sm text-muted">هدیه پروفایل</p>
          <p className="mt-1 text-sm font-bold">
            {data?.bonus_granted ? "۱۰۰۰ تزکوین دریافت شده" : "با تکمیل پروفایل ۱۰۰۰ تزکوین می‌گیرید"}
          </p>
        </div>
      </div>

      <h2 className="mb-3 text-lg font-extrabold">خرید اعتبار</h2>
      <div className="mb-8 grid gap-4 md:grid-cols-3">
        {(data?.packs ?? []).map((p) => (
          <article key={p.id} className="surface-card">
            <h3 className="m-0">{p.title}</h3>
            <p className="text-3xl font-black text-brand">{faNum(p.coins)}</p>
            <p className="text-sm text-muted">{faMoney(Math.round(p.irr_price / 10))} (تقریبی)</p>
            <p className="text-xs text-muted">مبلغ درگاه: {faNum(p.irr_price)} ریال</p>
            <div className="mt-3 flex flex-wrap gap-2">
              {(["zarinpal", "aqaye"] as const).map((g) => (
                <button
                  key={g}
                  type="button"
                  className="btn-tz btn-primary-tz"
                  disabled={busy === p.id + g}
                  onClick={() => void buy(p.id, g)}
                >
                  <CreditCard className="size-4" />
                  {g === "zarinpal" ? "زرین‌پال" : "آقای پرداخت"}
                </button>
              ))}
            </div>
          </article>
        ))}
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">گردش حساب</h2>
          <ul className="space-y-2 text-sm">
            {(data?.ledger ?? []).map((l) => (
              <li key={l.id} className="flex justify-between gap-3 border-b border-line pb-2">
                <span>
                  <b>{l.reason}</b>
                  <small className="mt-0.5 block text-muted">{l.created_at.slice(0, 16).replace("T", " ")}</small>
                </span>
                <b className={l.amount >= 0 ? "text-brand" : "text-red-700"}>
                  {l.amount >= 0 ? "+" : ""}
                  {faNum(l.amount)}
                </b>
              </li>
            ))}
            {!data?.ledger.length ? <p className="text-muted">هنوز گردشی ثبت نشده.</p> : null}
          </ul>
        </section>
        <section className="surface-card">
          <h2 className="mb-3 font-extrabold">پرداخت‌ها</h2>
          <ul className="space-y-2 text-sm">
            {pays.map((p) => (
              <li key={p.id} className="flex justify-between">
                <span>
                  {p.gateway} — {faNum(p.coins)} تزکوین
                </span>
                <b>{p.status === "paid" ? "موفق" : p.status}</b>
              </li>
            ))}
          </ul>
        </section>
      </div>
    </AppPage>
  );

  async function buy(packId: string, gateway: "zarinpal" | "aqaye") {
    setBusy(packId + gateway);
    try {
      const res = await startPayment({ data: { packId, gateway, origin: window.location.origin } });
      if (res.mode === "sandbox") {
        toast.success("در حالت آزمایشی اعتبار افزوده شد");
        load();
      } else {
        window.location.href = res.redirect;
      }
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "پرداخت شروع نشد");
    } finally {
      setBusy(null);
    }
  }
}
