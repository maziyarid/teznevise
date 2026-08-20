import { createFileRoute } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { AppFrame } from "@/components/layout/AppFrame";
import { getMyProfile } from "@/lib/server/app";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { RedirectToSignIn } from "@/lib/auth/gates";
import { Link } from "@tanstack/react-router";

export const Route = createFileRoute("/admin")({
  component: AdminGate,
});

function AdminGate() {
  const { user, isPending } = useCurrentUserState();
  const [role, setRole] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!user) return;
    void getMyProfile()
      .then((p) => setRole(p.role))
      .catch(() => setError("خطا در بررسی دسترسی"));
  }, [user]);

  if (isPending) return <div className="grid min-h-screen place-items-center text-muted">در حال بارگذاری…</div>;
  if (!user) return <RedirectToSignIn to="/login" />;
  if (error) return <div className="grid min-h-screen place-items-center text-muted">{error}</div>;
  if (role === null) return <div className="grid min-h-screen place-items-center text-muted">بررسی نقش…</div>;
  if (role !== "admin") {
    return (
      <div className="grid min-h-screen place-items-center p-8 text-center">
        <div>
          <h1 className="text-2xl font-extrabold">دسترسی مدیریت ندارید</h1>
          <p className="mt-2 text-muted">این بخش فقط برای مدیر سایت است. اولین حساب ساخته‌شده در پیش‌نمایش نقش مدیر می‌گیرد.</p>
          <Link to="/dashboard" className="btn-tz btn-primary-tz mt-6 inline-flex">
            رفتن به پنل کاربری
          </Link>
        </div>
      </div>
    );
  }
  return <AppFrame kind="admin" />;
}
