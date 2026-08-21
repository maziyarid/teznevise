import { useEffect, useState } from "react";
import { Link, useRouterState } from "@tanstack/react-router";
import { BookOpen, Calculator, Home, Phone, UserRound, Wallet } from "lucide-react";
import { BOTTOM_NAV } from "@/lib/site";
import { getPublicSite } from "@/lib/server/catalog";
import { cn } from "@/lib/utils";

const ICONS = {
  home: Home,
  tools: Calculator,
  blog: BookOpen,
  phone: Phone,
  user: UserRound,
  wallet: Wallet,
} as const;

type Item = { label: string; href: string; icon: string };

export function BottomNav() {
  const pathname = useRouterState({ select: (s) => s.location.pathname });
  const [items, setItems] = useState<Item[]>(
    BOTTOM_NAV.map((b) => ({ label: b.label, href: b.to, icon: b.icon })),
  );

  useEffect(() => {
    void getPublicSite()
      .then((s) => {
        if (s.nav?.length) setItems(s.nav.map((n) => ({ label: n.label, href: n.href, icon: n.icon })));
      })
      .catch(() => {});
  }, []);

  return (
    <nav className="bottom-nav" aria-label="ناوبری سریع">
      {items.map((item) => {
        const Icon = ICONS[item.icon as keyof typeof ICONS] ?? Home;
        if (item.href.startsWith("tel:") || item.href.startsWith("http")) {
          return (
            <a key={item.label} href={item.href} className="bottom-nav-item">
              <Icon className="size-5" aria-hidden />
              <span>{item.label}</span>
            </a>
          );
        }
        const active =
          item.href === "/"
            ? pathname === "/"
            : pathname === item.href || pathname.startsWith(item.href + "/");
        return (
          <Link key={item.href} to={item.href} className={cn("bottom-nav-item", active && "is-active")}>
            <Icon className="size-5" aria-hidden />
            <span>{item.label}</span>
          </Link>
        );
      })}
    </nav>
  );
}
