import type { ReactNode } from "react";
import { useEffect, useState } from "react";
import { Link, Outlet, useRouterState } from "@tanstack/react-router";
import { FaIcon } from "@/components/ui/FaIcon";
import { signOut } from "@/lib/auth/client";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { RedirectToSignIn } from "@/lib/auth/gates";
import { getMyProfile } from "@/lib/server/app";
import { Logo } from "./Logo";
import { cn } from "@/lib/utils";

const USER_NAV = [
  { to: "/dashboard", label: "خلاصه", icon: "fa-table-columns", exact: true },
  { to: "/dashboard/projects", label: "پروژه‌ها", icon: "fa-folder-open", exact: false },
  { to: "/dashboard/tickets", label: "تیکت‌ها", icon: "fa-ticket", exact: false },
  { to: "/dashboard/orders", label: "درخواست‌ها", icon: "fa-file-lines", exact: false },
  { to: "/dashboard/wallet", label: "کیف پول", icon: "fa-wallet", exact: false },
  { to: "/dashboard/referrals", label: "معرفی دوستان", icon: "fa-gift", exact: false },
  { to: "/dashboard/profile", label: "پروفایل", icon: "fa-user", exact: false },
] as const;

const ADMIN_NAV = [
  { to: "/admin", label: "نمای کلی", icon: "fa-table-columns", exact: true },
  { to: "/admin/tickets", label: "تیکت‌ها", icon: "fa-inbox", exact: false },
  { to: "/admin/projects", label: "پروژه‌ها", icon: "fa-folder-open", exact: false },
  { to: "/admin/inquiries", label: "سفارش‌ها", icon: "fa-file-lines", exact: false },
  { to: "/admin/tezcoin", label: "تزکوین", icon: "fa-coins", exact: false },
  { to: "/admin/agents", label: "عامل‌های هوش مصنوعی", icon: "fa-robot", exact: false },
  { to: "/admin/analytics", label: "بازدید و کلیک", icon: "fa-chart-column", exact: false },
  { to: "/admin/comments", label: "نظرها", icon: "fa-comments", exact: false },
  { to: "/admin/fields", label: "فیلدهای صفحات", icon: "fa-pen-to-square", exact: false },
  { to: "/admin/settings", label: "تنظیمات", icon: "fa-gear", exact: false },
] as const;

export function AppFrame({ kind }: { kind: "user" | "admin" }) {
  const { user, isPending } = useCurrentUserState();
  const pathname = useRouterState({ select: (s) => s.location.pathname });
  const nav = kind === "admin" ? ADMIN_NAV : USER_NAV;
  const [role, setRole] = useState<string | null>(null);

  useEffect(() => {
    if (!user) return;
    void getMyProfile()
      .then((p) => setRole(p.role))
      .catch(() => setRole("user"));
  }, [user]);

  if (isPending) {
    return <div className="grid min-h-screen place-items-center text-muted">در حال بارگذاری حساب…</div>;
  }
  if (!user) return <RedirectToSignIn to="/login" />;

  return (
    <div className="app-frame">
      <aside className="app-side">
        <div className="mb-6 px-2">
          <Logo to={kind === "admin" ? "/admin" : "/dashboard"} />
          <p className="mt-3 text-xs font-bold text-muted">{kind === "admin" ? "پنل مدیریت" : "پنل کاربری"}</p>
        </div>
        <nav className="app-side-nav">
          {nav.map((item) => {
            const active = item.exact ? pathname === item.to : pathname === item.to || pathname.startsWith(item.to + "/");
            return (
              <Link key={item.to} to={item.to} className={cn("app-nav-link", active && "is-active")}>
                <FaIcon icon={item.icon} />
                {item.label}
              </Link>
            );
          })}
        </nav>
        <div className="mt-4 border-t border-line pt-4">
          <p className="mb-2 truncate px-3 text-xs text-muted">{user.displayName || user.primaryEmail}</p>
          {kind === "user" && role === "admin" ? (
            <Link to="/admin" className="app-nav-link">
              <FaIcon icon="fa-shield-halved" />
              پنل مدیریت
            </Link>
          ) : null}
          {kind === "admin" ? (
            <Link to="/dashboard" className="app-nav-link">
              <FaIcon icon="fa-user" />
              پنل کاربری
            </Link>
          ) : null}
          <Link to="/" className="app-nav-link">
            <FaIcon icon="fa-house" />
            بازگشت به سایت
          </Link>
          <button type="button" className="app-nav-link w-full" onClick={() => void signOut("/")}>
            <FaIcon icon="fa-right-from-bracket" />
            خروج
          </button>
        </div>
      </aside>
      <div className="app-main">
        <Outlet />
      </div>
    </div>
  );
}

export function AppPage({ title, hint, children }: { title: string; hint?: string; children: ReactNode }) {
  return (
    <div>
      <header className="mb-6">
        <h1 className="text-2xl font-extrabold text-ink">{title}</h1>
        {hint ? <p className="mt-1 text-sm text-muted">{hint}</p> : null}
      </header>
      {children}
    </div>
  );
}
