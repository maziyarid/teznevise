import type { ReactNode } from "react";
import { useRouterState } from "@tanstack/react-router";
import { SiteHeader } from "./SiteHeader";
import { SiteFooter } from "./SiteFooter";
import { Fab } from "./Fab";
import { BottomNav } from "./BottomNav";
import { Tracker } from "@/components/analytics/Tracker";

export function SiteShell({ children }: { children: ReactNode }) {
  const pathname = useRouterState({ select: (s) => s.location.pathname });
  const isApp = pathname.startsWith("/dashboard") || pathname.startsWith("/admin");

  if (isApp) {
    return <>{children}</>;
  }

  return (
    <div className="flex min-h-screen flex-col bg-surface text-ink">
      <Tracker />
      <a className="skip-link" href="#main-content">
        رفتن به محتوا
      </a>
      <SiteHeader />
      <main id="main-content" className="flex-1">
        {children}
      </main>
      <SiteFooter />
      <Fab />
      <BottomNav />
    </div>
  );
}
