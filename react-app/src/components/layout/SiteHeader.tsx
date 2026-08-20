import { useEffect, useState } from "react";
import { Link, useRouterState } from "@tanstack/react-router";
import {
  ChevronDown,
  CircleHelp,
  Clock,
  Coins,
  Lock,
  Menu,
  PenSquare,
  Phone,
  Search,
  UserRound,
  X,
} from "lucide-react";
import { PRIMARY_NAV, SITE, UTILITY_LINKS } from "@/lib/site";
import { signOut } from "@/lib/auth/client";
import { useCurrentUserState } from "@/lib/auth/use-current-user";
import { getMyProfile } from "@/lib/server/app";
import { getHeaderCredits } from "@/lib/server/wallet";
import { Logo } from "./Logo";
import { SearchOverlay } from "./SearchOverlay";
import { faNum } from "@/lib/format";
import { cn } from "@/lib/utils";

export function SiteHeader() {
  const pathname = useRouterState({ select: (s) => s.location.pathname });
  const [open, setOpen] = useState(false);
  const [openMenu, setOpenMenu] = useState<string | null>(null);
  const [acc, setAcc] = useState<string | null>(null);
  const [searchOpen, setSearchOpen] = useState(false);

  useEffect(() => {
    setOpen(false);
    setOpenMenu(null);
    setAcc(null);
    setSearchOpen(false);
  }, [pathname]);

  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  return (
    <>
      <header className="site-header">
        <div className="announce">
          <div className="announce-inner">
            <div className="announce-items">
              <a className="pill" href={`tel:${SITE.phoneIntl}`}>
                <Phone className="size-3.5" aria-hidden />
                <strong>{SITE.phoneDisplay}</strong>
              </a>
              <span className="pill announce-desktop">
                <Clock className="size-3.5" aria-hidden />
                {SITE.hours}
              </span>
              <span className="pill announce-desktop">
                <Lock className="size-3.5" aria-hidden />
                مشاوره محرمانه و تخصصی
              </span>
            </div>
            <div className="announce-utils announce-desktop" aria-label="لینک‌های راهنما">
              {UTILITY_LINKS.map((l) => (
                <Link key={l.to} to={l.to} className="pill">
                  {l.label}
                </Link>
              ))}
            </div>
          </div>
        </div>

        <nav className="main-nav" aria-label="منوی اصلی">
          <button
            type="button"
            className="menu-btn"
            aria-label={open ? "بستن منو" : "باز کردن منو"}
            aria-expanded={open}
            onClick={() => setOpen((v) => !v)}
          >
            {open ? <X className="size-5" /> : <Menu className="size-5" />}
          </button>

          <Logo />

          <ul className="nav-links">
            {PRIMARY_NAV.map((item) => {
              const active =
                item.to === "/"
                  ? pathname === "/"
                  : pathname === item.to || pathname.startsWith(item.to + "/");
              const hasKids = Boolean(item.children?.length);
              return (
                <li
                  key={item.to}
                  className={cn(openMenu === item.to && "is-open")}
                  onMouseEnter={() => hasKids && setOpenMenu(item.to)}
                  onMouseLeave={() => setOpenMenu(null)}
                >
                  {hasKids ? (
                    <button
                      type="button"
                      className={cn("nav-top", active && "is-active")}
                      aria-expanded={openMenu === item.to}
                      onClick={() => setOpenMenu((cur) => (cur === item.to ? null : item.to))}
                    >
                      {item.label}
                      <ChevronDown className="nav-chevron" aria-hidden />
                    </button>
                  ) : (
                    <Link to={item.to} className={cn(active && "is-active")}>
                      {item.label}
                    </Link>
                  )}
                  {hasKids ? (
                    <div className={cn("nav-panel", (item.children?.length ?? 0) > 1 && "wide")}>
                      {item.children!.map((col, i) => (
                        <div key={i}>
                          {col.heading ? <h4>{col.heading}</h4> : null}
                          {col.items.map((c) => (
                            <Link key={c.to} to={c.to}>
                              {c.label}
                            </Link>
                          ))}
                        </div>
                      ))}
                    </div>
                  ) : null}
                </li>
              );
            })}
          </ul>

          <div className="nav-actions">
            <button
              type="button"
              className="icon-btn header-search"
              aria-label="جستجو"
              onClick={() => setSearchOpen(true)}
            >
              <Search className="size-4" />
            </button>
            <CreditsSlot />
            <div className="header-auth">
              <AuthSlot />
            </div>
            <div className="desktop-cta flex items-center gap-2">
              <Link to="/about" className="nav-cta nav-cta-outline">
                <CircleHelp className="size-4" />
                <span>درباره ما</span>
              </Link>
              <Link to="/inquiry" className="nav-cta nav-cta-solid">
                <PenSquare className="size-4" />
                <span>ثبت درخواست</span>
              </Link>
            </div>
          </div>
        </nav>
      </header>

      <SearchOverlay open={searchOpen} onClose={() => setSearchOpen(false)} />

      <div className={cn("mobile-nav", open && "open")} role="dialog" aria-modal="true" aria-label="منوی موبایل">
        <div className="mobile-nav-panel">
          <div className="mobile-nav-header">
            <Logo />
            <button type="button" className="icon-btn" aria-label="بستن منو" onClick={() => setOpen(false)}>
              <X className="size-5" />
            </button>
          </div>
          {PRIMARY_NAV.map((item) => {
            const hasKids = Boolean(item.children?.length);
            return (
              <div key={item.to}>
                {hasKids ? (
                  <button
                    type="button"
                    className="mobile-link mobile-acc"
                    aria-expanded={acc === item.to}
                    onClick={() => setAcc((c) => (c === item.to ? null : item.to))}
                  >
                    {item.label}
                    <ChevronDown className={cn("size-4 transition-transform", acc === item.to && "rotate-180")} />
                  </button>
                ) : (
                  <Link to={item.to} className="mobile-link">
                    {item.label}
                  </Link>
                )}
                {hasKids && acc === item.to ? (
                  <div className="mobile-sub">
                    <Link to={item.to}>همه {item.label}</Link>
                    {item.children!.flatMap((c) => c.items).map((s) => (
                      <Link key={s.to} to={s.to}>
                        {s.label}
                      </Link>
                    ))}
                  </div>
                ) : null}
              </div>
            );
          })}
          <Link to="/about" className="mobile-link">
            درباره ما
          </Link>
          <Link to="/privacy" className="mobile-link">
            حریم خصوصی
          </Link>
          <div className="mt-5 flex flex-col gap-2.5">
            <Link to="/inquiry" className="btn-tz btn-primary-tz">
              <PenSquare className="size-4" /> ثبت درخواست مشاوره
            </Link>
            <a className="btn-tz btn-light-tz" href={`tel:${SITE.phoneIntl}`}>
              تماس {SITE.phoneDisplay}
            </a>
          </div>
        </div>
      </div>
    </>
  );
}

function CreditsSlot() {
  const { user } = useCurrentUserState();
  const [balance, setBalance] = useState<number | null>(null);
  const [bonus, setBonus] = useState(1000);

  useEffect(() => {
    void getHeaderCredits()
      .then((c) => {
        setBalance(c.balance);
        setBonus(c.bonus);
      })
      .catch(() => setBalance(null));
  }, [user]);

  const tip = user
    ? balance
      ? `${faNum(balance)} تزکوین در کیف پول شما`
      : `با تکمیل پروفایل ${faNum(bonus)} تزکوین هدیه می‌گیرید`
    : `پس از ثبت‌نام و تکمیل پروفایل ${faNum(bonus)} تزکوین هدیه می‌گیرید`;

  return (
    <Link
      to={user ? "/dashboard/wallet" : "/login"}
      className="icon-btn credits-chip"
      aria-label="تزکوین"
      title={tip}
    >
      <Coins className="size-4" aria-hidden />
      <span>{balance == null ? "—" : faNum(balance)}</span>
    </Link>
  );
}

function AuthSlot() {
  const { user, isPending } = useCurrentUserState();
  const [role, setRole] = useState<string | null>(null);

  useEffect(() => {
    if (!user) {
      setRole(null);
      return;
    }
    void getMyProfile()
      .then((p) => setRole(p.role))
      .catch(() => setRole("user"));
  }, [user]);

  if (isPending) {
    return <div className="h-10 w-10 shrink-0 animate-pulse rounded-xl bg-brand/10" />;
  }
  if (user) {
    return (
      <div className="flex items-center gap-1">
        {role === "admin" ? (
          <Link to="/admin" className="icon-btn header-admin" aria-label="پنل مدیریت" title="پنل مدیریت">
            <span className="text-[11px] font-extrabold text-brand">مدیر</span>
          </Link>
        ) : null}
        <Link to="/dashboard" className="icon-btn" aria-label="پنل کاربری" title="پنل کاربری">
          <UserRound className="size-4" />
        </Link>
        <button type="button" className="icon-btn header-logout" onClick={() => void signOut()} aria-label="خروج">
          <span className="text-[11px] font-extrabold text-brand">خروج</span>
        </button>
      </div>
    );
  }
  return (
    <Link to="/login" className="icon-btn" aria-label="ورود" title="ورود">
      <UserRound className="size-4" />
    </Link>
  );
}
