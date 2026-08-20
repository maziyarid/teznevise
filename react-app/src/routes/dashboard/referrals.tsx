import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState, type FormEvent } from "react";
import { AppPage } from "@/components/layout/AppFrame";
import { applyReferral, getMyWallet } from "@/lib/server/wallet";
import { toast } from "sonner";

export const Route = createFileRoute("/dashboard/referrals")({ component: ReferralsPage });

function ReferralsPage() {
  const [code, setCode] = useState("");
  const [mine, setMine] = useState("");
  const [input, setInput] = useState("");

  useEffect(() => {
    void getMyWallet().then((w) => setMine(w.referral_code ?? "")).catch(() => {});
  }, []);

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    try {
      await applyReferral({ data: { code: input } });
      toast.success("کد معرفی ثبت شد");
      setInput("");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "کد نامعتبر است");
    }
  }

  const share = typeof window !== "undefined" && mine ? `${window.location.origin}/login?ref=${mine}` : "";

  return (
    <AppPage title="معرفی دوستان" hint="با هر معرفی موفق، هر دو نفر تزکوین هدیه می‌گیرید.">
      <div className="surface-card mb-6">
        <p className="text-sm text-muted">کد اختصاصی شما</p>
        <p className="mt-1 font-black tracking-widest text-brand" dir="ltr">
          {mine || "—"}
        </p>
        {share ? (
          <button
            type="button"
            className="btn-tz btn-light-tz mt-3"
            onClick={() => {
              void navigator.clipboard.writeText(share);
              toast.success("لینک کپی شد");
            }}
          >
            کپی لینک دعوت
          </button>
        ) : null}
      </div>
      <form className="surface-card grid gap-3" onSubmit={onSubmit}>
        <div className="field">
          <label htmlFor="rc">کد معرفی دوست</label>
          <input id="rc" dir="ltr" value={input || code} onChange={(e) => { setInput(e.target.value); setCode(e.target.value); }} />
        </div>
        <button className="btn-tz btn-primary-tz" type="submit">
          ثبت کد
        </button>
      </form>
    </AppPage>
  );
}
