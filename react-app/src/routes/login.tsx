import { createFileRoute, Link, useNavigate } from "@tanstack/react-router";
import { useState, type FormEvent } from "react";
import { GROK_PROVIDERS, authClient, authEnabled, signIn } from "@/lib/auth/client";
import { isValidIrMobile, phoneToEmail } from "@/lib/phone";
import { updateMyProfile } from "@/lib/server/app";
import { toast } from "sonner";

export const Route = createFileRoute("/login")({ component: Login });

function Login() {
  const [mode, setMode] = useState<"in" | "up">("in");
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [busy, setBusy] = useState(false);
  const navigate = useNavigate();

  async function onPhone(e: FormEvent) {
    e.preventDefault();
    if (!isValidIrMobile(phone)) {
      toast.error("شماره موبایل را به‌صورت 09xxxxxxxxx وارد کنید.");
      return;
    }
    const email = phoneToEmail(phone);
    setBusy(true);
    try {
      if (mode === "up") {
        const { error } = await authClient.signUp.email({
          email,
          password,
          name: name || phone,
        });
        if (error) throw new Error(error.message);
        await updateMyProfile({ data: { display_name: name || phone, phone } }).catch(() => {});
      } else {
        const { error } = await authClient.signIn.email({ email, password });
        if (error) throw new Error(error.message);
      }
      toast.success("وارد شدید");
      void navigate({ to: "/dashboard" });
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "ورود ناموفق بود");
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="page-hero">
      <div className="container-tz mx-auto max-w-md py-8">
        <span className="eyebrow">حساب کاربری</span>
        <h1 className="mt-3 text-3xl font-extrabold">ورود با شماره موبایل</h1>
        <p className="text-muted">برای پنل تیکت، ابزارهای ویژه و دستیار هوش مصنوعی. اولین حساب نقش مدیر می‌گیرد.</p>
        <div className="surface-card mt-6 space-y-3">
          <div className="mb-2 flex gap-2">
            <button type="button" className={`btn-tz ${mode === "in" ? "btn-primary-tz" : "btn-light-tz"}`} onClick={() => setMode("in")}>
              ورود
            </button>
            <button type="button" className={`btn-tz ${mode === "up" ? "btn-primary-tz" : "btn-light-tz"}`} onClick={() => setMode("up")}>
              ثبت‌نام
            </button>
          </div>
          {authEnabled ? (
            <>
              <form className="grid gap-3" onSubmit={onPhone}>
                {mode === "up" ? (
                  <div className="field">
                    <label htmlFor="nm">نام</label>
                    <input id="nm" value={name} onChange={(e) => setName(e.target.value)} />
                  </div>
                ) : null}
                <div className="field">
                  <label htmlFor="ph">شماره موبایل</label>
                  <input
                    id="ph"
                    dir="ltr"
                    inputMode="tel"
                    autoComplete="tel"
                    required
                    placeholder="09xxxxxxxxx"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                  />
                </div>
                <div className="field">
                  <label htmlFor="pw">رمز عبور</label>
                  <input id="pw" type="password" dir="ltr" required minLength={8} value={password} onChange={(e) => setPassword(e.target.value)} />
                </div>
                <button className="btn-tz btn-primary-tz w-full" type="submit" disabled={busy}>
                  {mode === "up" ? "ساخت حساب" : "ورود"}
                </button>
              </form>
              <p className="text-center text-xs text-muted">یا ورود سریع</p>
              {GROK_PROVIDERS.map((p) => (
                <button
                  key={p.providerId}
                  type="button"
                  onClick={() => signIn(p.providerId, { callbackURL: "/dashboard" })}
                  className="w-full rounded-xl border border-line px-4 py-3 font-bold hover:bg-soft"
                >
                  ادامه با {p.label}
                </button>
              ))}
            </>
          ) : (
            <p className="text-sm text-muted">ورود غیرفعال است.</p>
          )}
        </div>
        <Link to="/" className="link-arrow mt-6 inline-flex">
          بازگشت به خانه
        </Link>
      </div>
    </section>
  );
}
